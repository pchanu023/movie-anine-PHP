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
    $id = intval($_GET['id']); // แปลง id เป็นจำนวนเต็มเพื่อป้องกัน SQL Injection

    // คำสั่ง SQL เพื่อลบข้อมูลอนิเมะ
    $sql = "DELETE FROM anime WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // ผูกตัวแปร $id กับ ? ในคำสั่ง SQL

    // ทำการลบ
    if ($stmt->execute()) {
        echo "ลบข้อมูลอนิเมะเรียบร้อยแล้ว.";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error;
    }

    // ปิดการเตรียมคำสั่ง
    $stmt->close();
}

// ปิดการเชื่อมต่อ
$conn->close();

// กลับไปที่หน้ารายชื่ออนิเมะ
header("Location: viewanime.php"); // เปลี่ยนเป็นชื่อไฟล์ของหน้าที่แสดงรายชื่ออนิเมะ
exit();
?>
