<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
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

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // ถ้ายังไม่ได้เข้าสู่ระบบ
    exit();
}

$username = $_SESSION['username'];
$profile_picture = $_SESSION['profile_picture']; // รูปโปรไฟล์จาก session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aninne Comedy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    margin: 0;
    font-family: 'Helvetica Neue', Arial, sans-serif;
    background-color: #F5F5F5;
    color: #333;
}

.container {
    display: flex;
    position: relative;
}

.sidebar {
    width: 0;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background-color: #fff;
    overflow-x: hidden;
    transition: width 0.3s ease-in-out;
    padding-top: 60px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar-open {
    width: 250px;
}

.sidebar a {
    padding: 12px 20px;
    text-decoration: none;
    font-size: 18px;
    color: #555;
    display: block;
    transition: 0.3s;
}

.sidebar a:hover {
    background-color: #f1f1f1;
    color: #222;
}

.sidebar .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
    color: #333;
}

.main-content {
    margin-left: 0;
    padding: 20px;
    flex: 1;
    transition: margin-left 0.3s ease;
    overflow-y: auto;
    box-sizing: border-box;
}

.sidebar-open + .main-content {
    margin-left: 250px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
    padding: 15px 25px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.menu-icon {
    font-size: 30px;
    cursor: pointer;
    color: #333;
}

.search-bar {
    width: 300px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 25px;
    background-color: #fff;
    outline: none;
    font-size: 16px;
}

.search-bar:focus {
    border-color: #5B8C5A;
}

.right-icons {
    display: flex;
    align-items: center;
}

.right-icons a {
    margin-right: 20px;
    text-decoration: none;
    color: #5B8C5A;
    font-size: 16px;
    font-weight: bold;
}

.right-icons a:hover {
    color: #333;
}

.right-icons img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #ddd;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

        .anime-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr); /* แสดง 6 รายการต่อแถว */
            gap: 20px;

        }

        .anime-item {
            text-align: center;
            transition: transform 0.3s ease;
            margin: 5px;
        }

        .anime-item:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }

        .anime-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

.anime-item:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
}


.anime-title {
            margin-top: 10px;
            font-size: 16px;
            text-align: center;
            color: #333;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <a href="#" class="closebtn" onclick="toggleSidebar()">×</a>
            <a href="homepage.php">Home</a>
            <a href="Fantasy.php">Fantasy</a>
            <a href="Action.php">Action</a>
            <a href="#">Drama</a>
            <a href="Comedy.php">Comedy</a>
            <a href="Sport.php">Sport</a>
            <a href="Gourmet.php">Gourmet</a>
            <br><br><br><br><br>
            <a href="favorite.php">Favorite</a>
            <a href="Library.php">Library</a>
        </div>

        <div class="main-content">
            <div class="header">
                <span class="menu-icon" onclick="toggleSidebar()">&#9776;</span>
                <input type="text" class="search-bar" placeholder="search">
                <div class="right-icons">
    <a href="loginn.php">Log out</a>
    <a href="profile.php?username=<?php echo urlencode($username); ?>">
    <span style="color: #0056B3; text-decoration: underline;"><?php echo htmlspecialchars($username); ?></span>
</a>
</div>
            </div>

            <h1>Comedy</h1> <!-- เพิ่มหัวข้อสำหรับหมวด Fantasy -->

            <div class="anime-grid">
                <?php
                // ดึงข้อมูลเฉพาะ anime ที่มี genreID = 1 (Fantasy)
                $sql = "SELECT id, title, image FROM anime WHERE genreID = 4"; // genreID = 1 หมายถึง Fantasy
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="anime-item">';
                        echo '<a href="anime_detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>'; // ลิงก์ไปยังหน้าแสดงรายละเอียด
                        echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "ไม่มีข้อมูลอนิเมะประเภท Fantasy";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            if (sidebar.style.width === "250px") {
                sidebar.style.width = "0";
            } else {
                sidebar.style.width = "250px";
            }
        }
        function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("sidebar-open");
}
    </script>
    
</body>
</html>
