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
$anime_id = isset($_POST['anime_id']) ? $_POST['anime_id'] : 0;  // รับค่า anime_id จากฟอร์ม

// ตรวจสอบว่า user_id และ anime_id ไม่ใช่ค่า 0
if ($user_id && $anime_id) {
    // ลบข้อมูลจากตาราง user_purchases
    $query = "DELETE FROM user_purchases WHERE user_id = ? AND anime_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die('Error in prepare: ' . $conn->error);  // แสดงข้อความข้อผิดพลาดหาก prepare() ล้มเหลว
    }
    
    $stmt->bind_param("ii", $user_id, $anime_id);  // ส่งค่าพารามิเตอร์
    $stmt->execute();  // รันคำสั่ง DELETE
    
    if ($stmt->affected_rows > 0) {
        // ถ้าลบสำเร็จ, ไปที่หน้า anime details หรือแสดงข้อความ
        echo "<script>alert('ยกเลิกการซื้อเรียบร้อย!'); window.location.href='Library.php';</script>";

        exit();  // หยุดการประมวลผลต่อ
    } else {
        echo "ไม่สามารถยกเลิกการซื้อได้";
    }

    $stmt->close();  // ปิดคำสั่ง
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}

$conn->close();  // ปิดการเชื่อมต่อฐานข้อมูล
?>
