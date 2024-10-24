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

// ตรวจสอบว่ามี id ที่ถูกส่งมาหรือไม่
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // เตรียมคำสั่ง SQL
    $sql = "SELECT first_name, last_name, email, username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // ตรวจสอบว่าการเตรียมคำสั่งสำเร็จหรือไม่
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    // ผูกตัวแปร
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // ผูกผลลัพธ์
    $stmt->bind_result($first_name, $last_name, $email, $username);
    
    // ตรวจสอบว่าพบข้อมูลผู้ใช้หรือไม่
    if ($stmt->fetch()) {
        // เก็บข้อมูลในอาเรย์
        $user = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'username' => $username
        ];
    } else {
        die("ไม่พบข้อมูลผู้ใช้");
    }
    
    $stmt->close();
} else {
    die("ID ไม่ถูกต้อง");
}

// ปิดการเชื่อมต่อหลังจากใช้ข้อมูลทั้งหมด
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #007BFF;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        ul {
            list-style: none;
            padding: 0;
            text-align: left;
            display: inline-block;
            width: 100%;
        }
        li {
            font-size: 1.1em;
            line-height: 1.6;
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #007BFF;
        }
        .back {
            text-align: left; /* จัดตำแหน่งปุ่มให้อยู่ด้านซ้าย */
            margin-top: 20px; /* ระยะห่างระหว่างเนื้อหาและปุ่มกลับ */
        }
        .back a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007BFF; /* สีฟ้า */
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .back a:hover {
            background-color: #0056b3; /* สีฟ้าเข้มเมื่อ hover */
            transform: scale(1.05); /* ขยายขนาดเล็กน้อยเมื่อ hover */
        }
        .back a:active {
            background-color: #003d7a; /* สีเข้มเมื่อคลิก */
            transform: scale(0.95); /* ลดขนาดเล็กน้อยเมื่อคลิก */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>ข้อมูลผู้ใช้</h2>
    <ul>
        <li><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
        <li><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
    </ul>
    <div class="back">
        <a href="admin.php">กลับไปยังรายชื่อผู้ใช้</a>
    </div>
</div>

</body>
</html>
