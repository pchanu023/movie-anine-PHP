<?php
// เริ่ม session
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "u299560388_651201";
$password = "UL2690Bg";
$dbname = "u299560388_651201";
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // สมมติว่า user_id เก็บไว้ใน session

// ดึงชื่อผู้ใช้งานจากตาราง users โดยใช้ user_id
$sql = "SELECT username FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username']; // ตั้งค่าชื่อผู้ใช้ในตัวแปร $username
} else {
    $username = "User"; // ถ้าไม่พบชื่อผู้ใช้ ให้ตั้งเป็น "User"
}

// รับค่า anime_id จาก GET parameter
$anime_id = isset($_GET['anime_id']) ? intval($_GET['anime_id']) : 0;

// ดึงข้อมูลรายละเอียดอนิเมะรวมถึงรูปภาพและ genre จากตารางที่เกี่ยวข้อง
$query_details = "
    SELECT a.title, g.genre AS genre, a.episode_count, a.synopsis, a.image, t.clip_url 
    FROM animedetails a 
    LEFT JOIN anime_trailers t ON a.anime_id = t.anime_id 
    LEFT JOIN genre g ON a.genreID = g.genreID 
    WHERE a.anime_id = ?";
$stmt_details = $conn->prepare($query_details);

// ตรวจสอบว่าการเตรียมคำสั่งสำเร็จหรือไม่
if (!$stmt_details) {
    die("Preparation failed: " . $conn->error);
}

$stmt_details->bind_param("i", $anime_id);
$stmt_details->execute();
$stmt_details->bind_result($title, $genre, $episode_count, $synopsis, $image, $trailer_url);
$stmt_details->fetch();
$stmt_details->close();
$conn->close();

// ตรวจสอบว่ามี URL ตัวอย่างหรือไม่
if (!$trailer_url) {
    die('ไม่มีตัวอย่างสำหรับอนิเมะนี้');
}

// ตรวจสอบว่าถ้า URL เป็น YouTube ให้แปลงเป็น embed URL
$embed_url = '';
if (strpos($trailer_url, 'youtube.com') !== false || strpos($trailer_url, 'youtu.be') !== false) {
    $video_id = '';
    
    if (strpos($trailer_url, 'youtu.be') !== false) {
        $video_id = substr(parse_url($trailer_url, PHP_URL_PATH), 1);
    }
    
    if (strpos($trailer_url, 'youtube.com/watch') !== false) {
        parse_str(parse_url($trailer_url, PHP_URL_QUERY), $query);
        $video_id = $query['v'];
    }
    
    if ($video_id) {
        $embed_url = "https://www.youtube.com/embed/" . htmlspecialchars($video_id);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Library</title>
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

        .video-container {
            width: 100%; 
            max-width: 1100px; 
            margin: 0 auto; 
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .anime-detail-container {
            display: flex;
            align-items: flex-start; /* Align items to the start (top) */
            margin-top: 20px;
            max-width: 900px;
            margin: 0 auto; /* Center the container */
        }

        .anime-image {
            max-width: 30%; /* Limit image width */
            margin-right: 8%; /* Add space between image and text */
        }

        .anime-image img {
            width: 100%;
            margin-top: 20px;
        }

        .anime-info {
            text-align: left; /* Align text to the left */
            flex: 1; /* Allow the info to take remaining space */
        }

        .anime-info h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .anime-info p {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .anime-info p strong {
            color: #333;
        }
        .back-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007BFF; /* สีพื้นหลังเป็นสีน้ำเงิน */
        color: #fff; /* สีข้อความ */
        text-decoration: none; /* ลบเส้นใต้ */
        border-radius: 5px; /* ทำมุมมน */
        transition: background-color 0.3s, transform 0.2s; /* เพิ่มการเปลี่ยนแปลง */
        margin-top: 20px; /* เพิ่มระยะห่างด้านบน */
        float: right;
    }

    .back-button:hover {
        background-color: #0056b3; /* เปลี่ยนสีเมื่อ hover */
        transform: scale(1.05); /* ขยายขนาดเล็กน้อยเมื่อ hover */
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
            <a href="Drama.php">Drama</a>
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
                <input type="text" class="search-bar" placeholder="Search...">
                <div class="right-icons">
                    <a href="login.php">Log out |</a>
                    <a href="profile.php?username=<?php echo urlencode($username); ?>">
    <span style="color: #0056B3; text-decoration: underline;"><?php echo htmlspecialchars($username); ?></span>
</a>

                </div>
            </div>
            <br>
            <br>

            <div class="video-container">
                <iframe src="<?php echo htmlspecialchars($embed_url); ?>" allowfullscreen></iframe>
            </div>

            <div class="anime-detail-container">
                <div class="anime-image">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                </div>
                <div class="anime-info">
                    <h2><?php echo htmlspecialchars($title); ?></h2>
                    <p><strong>ประเภท :</strong> <?php echo htmlspecialchars($genre); ?></p>
                    <p><strong>จํานวนตอน :</strong> <?php echo htmlspecialchars($episode_count); ?></p>
                    <p><strong>เรื่องย่อ :</strong> <?php echo htmlspecialchars($synopsis); ?></p>
                    <a href="javascript:history.back()" class="back-button">ย้อนกลับ</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-open');
            document.querySelector('.main-content').classList.toggle('sidebar-open');
        }
    </script>
</body>
</html>
