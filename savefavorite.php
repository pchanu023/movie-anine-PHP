<?php
$servername = "localhost"; 
$username = "u299560388_651201"; // ใส่ username ของคุณ
$password = "UL2690Bg"; // ใส่ password ของคุณ
$dbname = "u299560388_651201"; // ใส่ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$user_id = $_SESSION['user_id'];  // หรือค่าผู้ใช้จากระบบล็อกอินของคุณ

// รับค่า anime_id จากฟอร์ม
$anime_id = (int) $_POST['anime_id'];  // ตรวจสอบให้มั่นใจว่า id เป็น integer

// เพิ่มรายการโปรดลงในตาราง favorites
$query = "INSERT INTO favorites (user_id, anime_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $anime_id);
$stmt->execute();
$stmt->close();
$conn->close();

// ใช้ JavaScript เพื่อแจ้งเตือนและกลับไปยังหน้าเดิม
echo "<script>
    alert('บันทึกรายการโปรดเรียบร้อยแล้ว');
    window.location.href = 'homepage.php?id=$anime_id';
</script>";
exit();
?>
