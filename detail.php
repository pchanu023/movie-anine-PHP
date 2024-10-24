<?php
// เริ่มเซสชัน
session_start();

// กำหนดค่าการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
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

// รับค่า id จาก URL parameter (ตัวอย่างเช่น anime_id)
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// ดึงข้อมูลจากตาราง animedetails โดย join กับตาราง genre
$query_anime = "
    SELECT ad.title, g.genre, ad.episode_count, ad.price, ad.synopsis, ad.image 
    FROM animedetails ad
    INNER JOIN genre g ON ad.genreID = g.genreID
    WHERE ad.anime_id = ?";
$stmt_anime = $conn->prepare($query_anime);
if ($stmt_anime === false) {
    die('Error in prepare anime: ' . $conn->error);
}

$stmt_anime->bind_param("i", $id);
$stmt_anime->execute();
$stmt_anime->store_result();

// ถ้ามีผลลัพธ์จากฐานข้อมูล
if ($stmt_anime->num_rows > 0) {
    $stmt_anime->bind_result($title, $genre, $episode_count, $price, $synopsis, $image);
    $stmt_anime->fetch();  // ดึงข้อมูลจากฐานข้อมูล
} else {
    die('Anime not found.');
}

$stmt_anime->free_result();  // เคลียร์ผลลัพธ์ของคำสั่งนี้
$conn->close();  // ปิดการเชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดอนิเมะ</title>
    <style>
        /* เพิ่มสไตล์ต่างๆ ที่จำเป็น */
        body {
            background-color: #F3F3F8;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            align-items: flex-start;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-width: 600px;
            width: 100%;
            margin: auto;
        }

        .image-container {
            width: 100%;
            max-width: 300px;
            height: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .details {
            padding: 20px;
            flex-grow: 1;
        }

        h2 {
            color: #333;
            font-size: 26px;
            margin: 0;
        }

        p {
            margin: 10px 0;
            color: #666;
            line-height: 1.6;
        }

        .buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        button {
            cursor: pointer;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-gray {
            background-color: #ddd5d5;
        }

        .btn-gray:hover {
            background-color: #9f8e8e;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="image-container">
        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>">
    </div>
    <div class="details">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <p><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($genre); ?></p>
        <p><strong>จำนวนตอน:</strong> <?php echo htmlspecialchars($episode_count); ?></p>
        <p><strong>ราคา:</strong> <?php echo htmlspecialchars($price); ?></p>
        <p><strong>เรื่องย่อ:</strong> <?php echo nl2br(htmlspecialchars($synopsis)); ?></p>
        <div class="buttons">
            <form action="mainadmin.php" method="GET">
                <button type="submit" class="btn-gray">กลับไปยังหน้าหลัก</button>
            </form>

            <form action="admin_trailer.php" method="GET">
                    <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" class="btn-gray">ดูตัวอย่าง 🎬</button>
                </form>
        </div>
    </div>
</div>
</body>
</html>
