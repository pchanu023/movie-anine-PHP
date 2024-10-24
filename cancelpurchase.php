<?php
// เริ่มเซสชัน
session_start();

// เปิดแสดงข้อผิดพลาดทั้งหมด
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

// ตรวจสอบว่า anime_id ถูกส่งมาหรือไม่
if (isset($_POST['anime_id']) && isset($_SESSION['user_id'])) {
    $anime_id = (int) $_POST['anime_id'];
    $user_id = (int) $_SESSION['user_id']; // ค่าจากเซสชัน

    // สร้างคำสั่ง SQL สำหรับการลบการซื้อ
    $query = "DELETE FROM user_purchases WHERE user_id = ? AND anime_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $anime_id);

    // ลบการซื้อจากตาราง
    if ($stmt->execute()) {
        // หากลบสำเร็จให้แสดงข้อความและรีไดเรกต์ไปยังหน้ารายละเอียด
        echo "<script>alert('ยกเลิกการซื้อเรียบร้อยแล้ว'); window.location.href = 'Library.php';</script>";
    } else {
        // หากเกิดข้อผิดพลาดในการลบ
        echo "<script>alert('ไม่สามารถยกเลิกการซื้อได้'); window.location.href = 'Library.php';</script>";
    }

    // ปิดคำสั่ง SQL
    $stmt->close();
} else {
    // หากไม่ได้รับข้อมูล anime_id หรือไม่มีการเข้าสู่ระบบ
    echo "<script>alert('การร้องขอไม่ถูกต้อง'); window.location.href = 'Library.php';</script>";
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
