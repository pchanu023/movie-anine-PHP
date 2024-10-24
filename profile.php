<?php
session_start(); // เริ่มต้น session

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // เปลี่ยนไปหน้า login ถ้ายังไม่ได้ล็อกอิน
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

$user_type = $_SESSION['user_type'];

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง users
$user_logged_in = $_SESSION['username']; // รับค่า username ของผู้ใช้ที่ล็อกอิน
$sql = "SELECT first_name, last_name, email, username, profile_picture FROM users WHERE username = '$user_logged_in'";
$result = $conn->query($sql);

// ตรวจสอบว่าผู้ใช้มีข้อมูลในฐานข้อมูลหรือไม่
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // ดึงข้อมูลผู้ใช้เป็น array
} else {
    die("User not found");
}

// ปิดการเชื่อมต่อ
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f8;
            margin: 0;
            padding: 0;
            height: 100vh; /* กำหนดความสูงของ body */
            display: flex; /* ใช้ flexbox */
            justify-content: center; /* จัดกลางแนวนอน */
            align-items: center; /* จัดกลางแนวตั้ง */
        }
        .container {
            display: flex;
            max-width: 800px;
            width: 100%; /* กำหนดให้ความกว้างเต็มที่ */
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-picture {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-right: 1px solid #ddd;
        }
        .profile-picture img {
            width: 300px; /* ขนาดของภาพ */
            height: 400px; /* ขนาดของภาพ */
            border-radius: 3px; /* ทำให้ภาพเป็นวงกลม */
        }
        .profile-info {
            flex: 2;
            padding: 20px;
        }
        .profile-info h2 {
            margin: 0 0 20px 0;
        }
        .info-box {
            padding: 15px;
            margin-bottom: 20px; /* ระยะห่างด้านล่างของแต่ละกรอบ */
            border: 1px solid #ddd; /* กรอบรอบๆ */
            border-radius: 5px; /* มุมกรอบมน */
            background-color: #f9f9f9; /* สีพื้นหลัง */
        }
        .info-box label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .info-box p {
            margin: 0;
            color: #333;
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group a {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            color: #fff;
        }
        .button-group a.cancel {
            background-color: #dc3545;
            margin-right: 10px;
        }
        .button-group a.edit {
            background-color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-picture">
    <?php 
    // ตรวจสอบว่าค่าของ profile_picture ไม่ใช่ NULL และไม่ว่าง
    if (!empty($user['profile_picture'])) {
        // แก้ไขพาธให้ถูกต้อง โดยเอา 'pic/' เพียงครั้งเดียว
        $image_path = htmlspecialchars($user['profile_picture']);
        
        // ตรวจสอบว่ามีไฟล์อยู่ในพาธนี้หรือไม่
        if (file_exists($image_path)) {
            echo '<img src="' . $image_path . '" alt="Profile Picture">';
        } else {
            // แสดงข้อความเตือนถ้าไม่พบไฟล์รูปภาพ
            echo '<p style="color:red;">Error: Image file does not exist at ' . $image_path . '</p>';
        }
    } else {
        // ถ้าไม่มีภาพ ให้แสดงภาพเริ่มต้น
        echo '<img src="pic/default-profile.png" alt="Default Profile Picture">';
    }
    ?>
    </div>

    <div class="profile-info">
    <h2>Profile</h2>

    <div class="info-box">
        <label>ชื่อ-นามสกุล:</label>
        <p><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
    </div>
    
    <div class="info-box">
        <label>ชื่อผู้ใช้:</label>
        <p><?php echo htmlspecialchars($user['username']); ?></p>
    </div>

    <div class="info-box">
        <label>อีเมล:</label>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <div class="button-group">
            <?php
            // ตรวจสอบประเภทผู้ใช้ และแสดงปุ่มย้อนกลับไปหน้าที่ถูกต้อง
            if ($user_type == 'admin') {
                echo '<a href="mainadmin.php" class="cancel">ย้อนกลับ</a>'; // สำหรับ admin
            } else {
                echo '<a href="homepage.php" class="cancel">ย้อนกลับ</a>'; // สำหรับผู้ใช้ทั่วไป
            }
            ?>
            <a href="edit-profile.php" class="edit">แก้ไขโปรไฟล์</a>
        </div>
    </div>
</div>

</body>
</html>
