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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Anime</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .containerr {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .containerr h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
        }

        .containerr label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #555;
        }

        .containerr input[type="text"],
        .containerr input[type="number"],
        .containerr textarea,
        .containerr select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .containerr input[type="file"] {
            margin-bottom: 20px;
        }

        .containerr input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .containerr input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success-message {
            text-align: center;
            color: green;
            font-size: 18px;
            margin-top: 20px;
        }

        .total-anime {
            font-size: 18px;
            font-weight: bold;
            color: #444;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
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

        <label for="synopsis">คำบรรยาย:</label>
        <textarea name="synopsis" rows="4" required></textarea>

        <label for="episode_count">จำนวนตอน:</label>
        <input type="number" name="episode_count" required>

        <label for="price">ราคา:</label>
        <input type="number" name="price" required>

        <label for="youtube_url">URL ของคลิปตัวอย่าง (YouTube):</label>
        <input type="text" name="youtube_url" placeholder="กรุณาใส่ URL ของ YouTube">

        <label for="image">อัปโหลดภาพ:</label>
        <input type="file" name="image" accept="image/*" required>

        <input type="submit" value="เพิ่มอนิเมะ">
    </form>

    <?php if (isset($_GET['message'])): ?>
        <div class="success-message"><?php echo $_GET['message']; ?></div>
    <?php endif; ?>
</div>
</body>
</html>
