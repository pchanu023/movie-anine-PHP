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

// ตรวจสอบว่า user_id อยู่ใน session หรือไม่
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;  // เก็บ user_id จาก session หรือ 0 ถ้าไม่มี
$id = isset($_GET['id']) ? $_GET['id'] : 0;  // รับค่า id จาก URL parameter (ตัวอย่างเช่น anime_id)

// ตั้งค่าตัวแปรเพื่อเช็คว่า anime นี้อยู่ในรายการโปรดหรือไม่
$is_favorite = false;  // ตั้งค่าเริ่มต้นเป็น false
$is_purchased = false;  // ตั้งค่าเริ่มต้นเป็น false

// เช็คว่า anime นี้อยู่ในรายการโปรดหรือไม่
$query_favorite = "SELECT * FROM favorites WHERE user_id = ? AND anime_id = ?";
$stmt_favorite = $conn->prepare($query_favorite);
if ($stmt_favorite === false) {
    die('Error in prepare: ' . $conn->error);  // แสดงข้อความข้อผิดพลาดหาก prepare() ล้มเหลว
}

$stmt_favorite->bind_param("ii", $user_id, $id);
$stmt_favorite->execute();
$stmt_favorite->store_result();  // จัดเก็บผลลัพธ์ของคำสั่ง
if ($stmt_favorite->num_rows > 0) {
    $is_favorite = true;  // ถ้ามีข้อมูลแสดงว่า anime นี้อยู่ในรายการโปรด
}
$stmt_favorite->free_result();  // เคลียร์ผลลัพธ์ของคำสั่งนี้

// เช็คว่า anime นี้ถูกซื้อไปแล้วหรือไม่
$query_purchase = "SELECT * FROM user_purchases WHERE user_id = ? AND anime_id = ?";
$stmt_purchase = $conn->prepare($query_purchase);
if ($stmt_purchase === false) {
    die('Error in prepare: ' . $conn->error);  // แสดงข้อความข้อผิดพลาดหาก prepare() ล้มเหลว
}

$stmt_purchase->bind_param("ii", $user_id, $id);
$stmt_purchase->execute();
$stmt_purchase->store_result();  // จัดเก็บผลลัพธ์ของคำสั่ง
if ($stmt_purchase->num_rows > 0) {
    $is_purchased = true;  // ถ้ามีข้อมูลแสดงว่า anime นี้ถูกซื้อไปแล้ว
}
$stmt_purchase->free_result();  // เคลียร์ผลลัพธ์ของคำสั่งนี้

// ดึงข้อมูลจากตาราง animedetails โดย join กับตาราง genre
$query_anime = "
    SELECT ad.title, g.genre, ad.episode_count, ad.price, ad.synopsis, ad.image 
    FROM animedetails ad
    INNER JOIN genre g ON ad.genreID = g.genreID
    WHERE ad.anime_id = ?";
$stmt_anime = $conn->prepare($query_anime);
if ($stmt_anime === false) {
    die('Error in prepare: ' . $conn->error);  // แสดงข้อความข้อผิดพลาดหาก prepare() ล้มเหลว
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
    <title>รายละเอียด</title>
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
            max-width: auto;
            height: 100%;
            max-height: auto;
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

        .btn-red {
            background-color: #FF6B6B;
            color: white;
        }

        .btn-green {
            background-color: #4CAF50;
            color: white;
        }

        .btn-orange {
            background-color: #FFA500;
            color: white;
        }

        .btn-red:hover {
            background-color: #FF4C4C;
        }

        .btn-green:hover {
            background-color: #45A049;
        }

        .btn-orange:hover {
            background-color: #FF8C00;
        }
        .btn-gray {
            background-color: #ddd5d5;
        }
        .btn-gray:hover{
            background-color: #a49898;
        }

        @media (min-width: 600px) {
    .container {
        flex-direction: row; /* กลับไปเป็นการจัดเรียงแบบแถวสำหรับหน้าจอใหญ่ */
    }

    .image-container {
        width: 100%;
        max-width: 300px;
    }

    .details {
        padding: 20px;
        flex-grow: 1;
    }

    .buttons {
        flex-direction: row; /* ปรับการจัดเรียงปุ่มให้เป็นแถวสำหรับหน้าจอใหญ่ */
    }

    button {
        width: auto; /* กำหนดความกว้างปุ่มเป็นอัตโนมัติ */
    } 
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
            <form action="homepage.php" method="GET">
                <button type="submit" class="btn-red">ยกเลิก X </button>
            </form>

            <?php if ($is_favorite): ?>
                <form action="removefavorite.php" method="POST">
                    <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" class="btn-red">ยกเลิกรายการโปรด ❌</button>
                </form>
            <?php else: ?>
                <form action="savefavorite.php" method="POST">
                    <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" class="btn-orange">เพิ่มรายการโปรด ❤️</button>
                </form>
            <?php endif; ?>

            <?php if ($is_purchased): ?>
                <form action="unbuy.php" method="POST">
                  <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                  <button type="submit" class="btn-red">ยกเลิกการซื้อ ❌</button>
            </form>
            <?php else: ?>
                <form action="buy.php" method="POST">
                    <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" class="btn-green">ซื้อเลย 🛒</button>
                </form>
            <?php endif; ?>
            <!-- ปุ่มใหม่สำหรับดูตัวอย่าง -->
             <form action="anime_trailer.php" method="GET">
                    <input type="hidden" name="anime_id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" class="btn-gray">ดูตัวอย่าง 🎬</button>
                </form>
        </div>
    </div>
</div>
</body>
</html>
