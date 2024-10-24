<?php
// เปิดการแสดงข้อผิดพลาดทั้งหมด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// การตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "u299560388_651201"; // ชื่อผู้ใช้ฐานข้อมูลของคุณ
$password = "UL2690Bg"; // รหัสผ่านฐานข้อมูลของคุณ
$dbname = "u299560388_651201"; // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เริ่ม session เพื่อดึงข้อมูล user_id
session_start();
if (!isset($_SESSION['user_id'])) {
    die("กรุณาล็อกอินก่อนทำการซื้อ!");
}

$user_id = $_SESSION['user_id']; // รับ user_id จาก session
$anime_id = (int) $_POST['anime_id']; // รับ anime_id จาก POST

// ตรวจสอบว่ามี anime_id หรือไม่
if (empty($anime_id)) {
    die("ข้อมูลไม่ครบถ้วน กรุณาลองใหม่");
}

// ตรวจสอบว่าผู้ใช้ได้ซื้อ anime นี้ไปแล้วหรือไม่
$check_sql = "SELECT * FROM user_purchases WHERE user_id = ? AND anime_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $anime_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // ถ้าผู้ใช้ได้ซื้อ anime นี้แล้ว
    echo "<script>alert('คุณได้ซื้อรายการนี้ไปแล้ว ไม่สามารถซื้อซ้ำได้!'); window.location.href='test.php';</script>";
} else {
    // ถ้ายังไม่ได้ซื้อ ทำการบันทึกข้อมูลการซื้อ
    $sql = "INSERT INTO user_purchases (user_id, anime_id) VALUES (?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        // ผูกค่าพารามิเตอร์ (ii = integer, integer)
        $stmt->bind_param("ii", $user_id, $anime_id);
    
        // รัน statement
        if ($stmt->execute()) {
            // หากบันทึกข้อมูลสำเร็จ
            echo "<script>alert('การซื้อสำเร็จ!'); window.location.href='homepage.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$check_stmt->close();
$conn->close();
?>
