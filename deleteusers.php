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

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // แปลง id เป็นจำนวนเต็ม

    // คำสั่ง SQL เพื่อลบข้อมูล
    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "ลบข้อมูลผู้ใช้สำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
} else {
    echo "ไม่พบข้อมูลผู้ใช้ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
$conn->close();

// กลับไปยังหน้ารายชื่อผู้ใช้
header("Location: admin.php"); // เปลี่ยน user_list.php เป็นชื่อไฟล์ที่คุณใช้สำหรับแสดงรายชื่อผู้ใช้
exit();
?>
