<?php
$servername = "localhost"; 
$username = "u299560388_651201"; 
$password = "UL2690Bg"; 
$dbname = "u299560388_651201"; 

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_count = "SELECT COUNT(*) AS total FROM anime";
$result = $conn->query($sql_count);
$total_anime = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_anime = $row['total'];
}

// Query เพื่อนับจำนวนอนิเมะทั้งหมด
$sql_count = "SELECT COUNT(*) AS total FROM anime";
$result = $conn->query($sql_count);
$total_anime = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_anime = $row['total'];
}

// รับค่าจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $genreID = $_POST['genre'];
    $synopsis = $_POST['synopsis'];
    $episode_count = $_POST['episode_count'];
    $price = $_POST['price'];
    $youtube_url = $_POST['youtube_url']; // รับค่าจากช่อง URL YouTube

    // ตรวจสอบการอัปโหลดไฟล์
    if ($_FILES["image"]["error"] == 0) {
        $targetDir = "pic/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);  // ใช้ชื่อไฟล์เดิม
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // ตรวจสอบว่ามีโฟลเดอร์หรือไม่ ถ้าไม่มีให้สร้าง
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // ตรวจสอบประเภทไฟล์
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if ($_FILES["image"]["size"] <= 2000000) { // ตรวจสอบขนาดไฟล์ไม่เกิน 2MB
                if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png" || $imageFileType == "gif") {
                    // ไม่ตรวจสอบว่าไฟล์มีอยู่แล้ว (สามารถเขียนทับได้)
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                        // เพิ่มข้อมูลลงในตาราง anime โดยการกำหนด id เอง
                        $sql = "INSERT INTO anime (id, title, image, genreID) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("issi", $id, $title, $targetFile, $genreID); // ตรวจสอบให้แน่ใจว่าได้ส่งค่าที่ต้องการ

                        if ($stmt->execute()) {
                            // เพิ่มข้อมูลลงใน animedetails
                            $sql_details = "INSERT INTO animedetails (id, title, synopsis, episode_count, price, anime_id, genreID, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_details = $conn->prepare($sql_details);
                            $stmt_details->bind_param("issiiiss", $id, $title, $synopsis, $episode_count, $price, $id, $genreID, $targetFile); // จำนวนและลำดับของพารามิเตอร์ต้องตรงกับคำสั่ง SQL

                            if ($stmt_details->execute()) {
                                // เพิ่มข้อมูลลงใน anime_trailers
                                $sql_trailer = "INSERT INTO anime_trailers (anime_id, clip_url) VALUES (?, ?)";
                                $stmt_trailer = $conn->prepare($sql_trailer);
                                $stmt_trailer->bind_param("is", $id, $youtube_url); // ส่งค่า anime_id และ clip_url

                                if ($stmt_trailer->execute()) {
                                    header("Location: add.php?message=เพิ่มอนิเมะสำเร็จ&anime_id=" . $id);
                                    exit();
                                } else {
                                    echo "Error: " . $stmt_trailer->error;
                                }

                                $stmt_trailer->close();
                            } else {
                                echo "Error: " . $stmt_details->error;
                            }

                            $stmt_details->close();
                        } else {
                            echo "Error: " . $stmt->error;
                        }

                        $stmt->close();
                    } else {
                        echo "Sorry, there was an error moving your file.";
                    }
                } else {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                echo "Sorry, your file is too large. Maximum file size is 2MB.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "Error uploading file: " . $_FILES["image"]["error"];
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Management</title>
    <link rel="stylesheet" href="add.css">
    <style>
        /* สไตล์สำหรับคอนเทนเนอร์ */
        .container {
            max-width: 400px; /* ขนาดสูงสุดของคอนเทนเนอร์ */
            margin: 50px auto; /* จัดให้อยู่กลางหน้า */
            padding: 20px; /* ระยะห่างภายใน */
            background-color: #f9f9f9; /* สีพื้นหลัง */
            border-radius: 10px; /* ทำมุมให้กลม */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงาที่ให้ความลึก */
        }

        label {
            display: block; /* ให้ label แสดงเป็นบล็อก */
            margin-bottom: 10px; /* ระยะห่างด้านล่าง */
            font-size: 18px; /* ขนาดฟอนต์ */
        }

        button {
            display: block; /* ให้ปุ่มแสดงเป็นบล็อก */
            width: 100%; /* ความกว้างเต็ม */
            padding: 10px; /* ระยะห่างภายใน */
            margin-top: 10px; /* ระยะห่างด้านบน */
            font-size: 16px; /* ขนาดฟอนต์ */
            color: #fff; /* สีตัวอักษร */
            background-color: #007bff; /* สีพื้นหลังปุ่ม */
            border: none; /* ไม่ให้มีกรอบ */
            border-radius: 5px; /* ทำมุมให้กลม */
            cursor: pointer; /* แสดงลูกศรเมื่อชี้ไปที่ปุ่ม */
            text-align: center; /* จัดข้อความกลาง */
        }

        button:hover {
            background-color: #0056b3; /* เปลี่ยนสีพื้นหลังเมื่อชี้เมาส์ */
        }

        .buttons-group {
            display: flex; /* จัดกลุ่มปุ่มให้แสดงแบบแนวนอน */
            justify-content: space-between; /* แบ่งปุ่มให้ห่างกัน */
        }

        .buttons-group button {
            width: 48%; /* ความกว้างของปุ่มในกลุ่ม */
        }

        /* สไตล์สำหรับตารางผู้ใช้ */
        .user-list {
            display: none; /* ซ่อนรายการผู้ใช้ในตอนเริ่มต้น */
            margin-top: 20px; /* ระยะห่างด้านบน */
            max-width: 600px; /* ขนาดสูงสุดของตาราง */
            margin-left: auto; /* จัดกลางในแนวนอน */
            margin-right: auto; /* จัดกลางในแนวนอน */
            padding: 20px; /* ระยะห่างภายใน */
            background-color: #f9f9f9; /* สีพื้นหลัง */
            border-radius: 10px; /* ทำมุมให้กลม */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงาที่ให้ความลึก */
        }

        table {
            width: 100%; /* ความกว้างเต็มของตาราง */
            border-collapse: collapse; /* รวมกรอบของเซลล์ */
        }

        th, td {
            padding: 10px; /* ระยะห่างภายในของเซลล์ */
            border: 1px solid #ddd; /* สีกรอบของเซลล์ */
            text-align: left; /* จัดข้อความทางซ้าย */
        }

        th {
            background-color: #f2f2f2; /* สีพื้นหลังของหัวตาราง */
        }

        .button-group a {
            padding: 5px 10px; /* ระยะห่างภายในของลิงค์ */
            text-decoration: none; /* ไม่ให้ขีดเส้นใต้ */
            margin-right: 5px; /* ระยะห่างด้านขวา */
            border-radius: 5px; /* ทำมุมให้กลม */
            background-color: #007bff; /* สีพื้นหลังของลิงค์ */
            color: #fff; /* สีตัวอักษร */
        }

        .button-group a.delete-btn {
            background-color: #dc3545; /* สีพื้นหลังสำหรับลิงค์ลบ */
        }

        .button-group a:hover {
            background-color: #0056b3; /* เปลี่ยนสีเมื่อชี้เมาส์ */
        }

        .button-group a.delete-btn:hover {
            background-color: #c82333; /* เปลี่ยนสีเมื่อชี้เมาส์สำหรับลิงค์ลบ */
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        /* Style for the container */
.containerr {
    max-width: 600px; /* กำหนดความกว้างสูงสุด */
    margin: 50px auto; /* จัดกลางในแนวนอน */
    padding: 20px; /* ช่องว่างภายใน */
    background-color: white; /* สีพื้นหลัง */
    border-radius: 8px; /* มุมโค้ง */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
}

/* Heading Style */
.containerr h1 {
    text-align: center; /* จัดกลางหัวข้อ */
    color: #333; /* สีข้อความ */
    font-size: 24px; /* ขนาดตัวอักษร */
}

/* Label Style */
.containerr label {
    display: block; /* แสดงเป็นบล็อก */
    margin: 15px 0 5px; /* ช่องว่างรอบๆ */
    font-weight: bold; /* ตัวหนา */
    color: #555; /* สีข้อความ */
}

/* Input Style */
.containerr input[type="text"],
.containerr input[type="number"],
.containerr textarea,
.containerr select {
    width: 100%; /* ความกว้างเต็มที่ */
    padding: 10px; /* ช่องว่างภายใน */
    border: 1px solid #ccc; /* ขอบ */
    border-radius: 4px; /* มุมโค้ง */
    margin-bottom: 20px; /* ช่องว่างด้านล่าง */
    font-size: 16px; /* ขนาดตัวอักษร */
    box-sizing: border-box; /* รวมขอบในขนาด */
}

/* Input file style */
.containerr input[type="file"] {
    margin-bottom: 20px; /* ช่องว่างด้านล่าง */
}

/* Button Style */
.containerr input[type="submit"] {
    background-color: #4CAF50; /* สีพื้นหลังปุ่ม */
    color: white; /* สีข้อความ */
    padding: 10px; /* ช่องว่างภายใน */
    border: none; /* ไม่ให้มีขอบ */
    border-radius: 4px; /* มุมโค้ง */
    cursor: pointer; /* เปลี่ยนเคอร์เซอร์ */
    font-size: 16px; /* ขนาดตัวอักษร */
    transition: background-color 0.3s; /* การเปลี่ยนสีพื้นหลัง */
}

.containerr input[type="submit"]:hover {
    background-color: #45a049; /* สีพื้นหลังเมื่อ hover */
}


    </style>
    <script>
        // ฟังก์ชันสำหรับแสดง/ซ่อนรายชื่อผู้ใช้
        function toggleUserList() {
            const userList = document.getElementById('userList');
            userList.style.display = userList.style.display === 'block' ? 'none' : 'block';
        }
        function toggleUserListadd() {
            const userList = document.getElementById('addanime');
            userList.style.display = userList.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</head>
<body>

    <div class="container">
        <label for="test.php">Home page</label>
        <button onclick="window.location.href='mainadmin.php';">Home page</button>

        <label for="test.php">anime</label>
        <button onclick="window.location.href='viewanime.php';">anime</button>
        <!-- ปุ่มสำหรับเพิ่มอนิเมะ -->
        <label for="add">เพิ่มอนิเมะ:</label>
        <button onclick="toggleUserListadd()">เพิ่มอนิเมะ</button>

        <!-- ปุ่มสำหรับดูรายชื่อผู้ใช้ -->
        <label for="view_users">ดูรายชื่อผู้ใช้:</label>
        <button onclick="toggleUserList()">ดูผู้ใช้</button>
    </div>

    <!-- ตารางข้อมูลผู้ใช้ -->
    <div class="user-list" id="userList">
        <h2>ข้อมูลรายชื่อผู้ใช้</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>เพิ่มเติม</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "u299560388_651201";
                $password = "UL2690Bg";
                $dbname = "u299560388_651201";

                // สร้างการเชื่อมต่อ
                $conn = new mysqli($servername, $username, $password, $dbname);

                // ตรวจสอบการเชื่อมต่อ
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง users
                $sql = "SELECT id, first_name, last_name, email, username FROM users";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($user = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['first_name'] . " " . $user['last_name']) . "</td>";
                        echo "<td class='button-group'>
                                <a href='view.php?id=" . htmlspecialchars($user['id']) . "'>View</a>
                                <a href='deleteusers.php?id=" . htmlspecialchars($user['id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>ไม่พบข้อมูลผู้ใช้</td></tr>";
                }
                // ปิดการเชื่อมต่อ
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>


    <div class="containerr" id="addanime">

    <h1>เพิ่มอนิเมะใหม่</h1>

    <!-- แสดงจำนวนอนิเมะทั้งหมด -->
    <div class="total-anime">
        มีอนิเมะทั้งหมด <?php echo $total_anime; ?> เรื่อง
    </div>

    <form action="add.php" method="POST" enctype="multipart/form-data">
        <label for="id">อนิเมะเรื่องที่ (id):</label>
        <input type="number" name="id" required>

        <label for="title">ชื่ออนิเมะ:</label>
        <input type="text" name="title" required>

        <label for="genre">ประเภทอนิเมะ:</label>
        <select name="genre" required>
            <option value="">เลือกประเภท</option>
            <option value="1">Fantasy</option>
            <option value="2">Action</option>
            <option value="3">Drama</option>
            <option value="4">Comedy</option>
            <option value="5">Sport</option>
            <option value="6">Gourmet</option>
        </select>

        <label for="image">เลือกรูปภาพ:</label>
        <input type="file" name="image" accept="image/*" required>

        <label for="synopsis">เรื่องย่อ:</label>
        <textarea name="synopsis" required></textarea>

        <label for="episode_count">จำนวนตอน:</label>
        <input type="number" name="episode_count" required>

        <label for="price">ราคา:</label>
        <input type="number" name="price" step="0.01" required>

        <label for="youtube_url">URL ของคลิปตัวอย่าง (YouTube):</label>
        <input type="text" name="youtube_url" placeholder="กรุณาใส่ URL ของ YouTube">

        <input type="submit" value="เพิ่มอนิเมะ">
    </form>

    <?php if (isset($_GET['anime_id'])): ?>
        <div class="success-message">
            เพิ่มอนิเมะเรื่องที่ <?php echo htmlspecialchars($_GET['anime_id']); ?> สำเร็จ!
        </div>
    <?php endif; ?>
</div>
</body>
    

    
</body>
</html>
