<?php
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

// รับค่า anime_id และ user_id จาก POST
if (isset($_POST['anime_id']) && isset($_POST['user_id'])) {
    $anime_id = (int) $_POST['anime_id'];
    $user_id = (int) $_POST['user_id'];

    // สร้างคำสั่ง SQL เพื่อลบรายการโปรด
    $stmt = $conn->prepare("DELETE FROM favorites WHERE anime_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $anime_id, $user_id);

    // ตรวจสอบการดำเนินการลบ
    if ($stmt->execute()) {
        echo "ยกเลิกรายการโปรดเรียบร้อยแล้ว";
    } else {
        echo "เกิดข้อผิดพลาดในการยกเลิกรายการโปรด";
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();

    // เปลี่ยนเส้นทางกลับไปยังหน้าหลักหรือหน้าที่ต้องการ
    header("Location: your-redirect-page.php");
    exit();
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}
?>
