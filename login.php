<?php
session_start();
$error_message = ""; // สำหรับแสดงข้อความผิดพลาด

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $user = $_POST['username']; // เปลี่ยนชื่อให้ตรงกับฟิลด์ในฟอร์ม
    $pass = $_POST['password'];

    // ตรวจสอบข้อมูลจากฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            // ตั้งค่าข้อมูล session
            $_SESSION['user_id'] = $row['id']; // เก็บ user_id ลงใน session
            $_SESSION['username'] = $row['username'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
            $_SESSION['user_type'] = $row['user_type']; // เพิ่มการเก็บ user_type

            // ตรวจสอบประเภทผู้ใช้
            if ($_SESSION['user_type'] == 'admin') {
                header("Location: mainadmin.php"); // ถ้าเป็น admin ให้ส่งไปที่หน้า mainadmin.php
            } else {
                header("Location: homepage.php"); // ถ้าไม่ใช่ admin ให้ส่งไปที่หน้า homepage.php
            }
            exit();
        } else {
            $error_message = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error_message = "ไม่พบผู้ใช้";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aninne</title>
    <style>
    body {
        margin: 0; /* ไม่มีกระเบื้องขอบ */
        padding: 0; /* ไม่มีกระเบื้องขอบ */
        height: 100vh; /* ให้สูงเท่ากับความสูงของ viewport */
        overflow: hidden; /* ป้องกันไม่ให้มีการเลื่อน */
    }

    .video-background {
        position: fixed; /* ตำแหน่งคงที่ */
        top: 0;
        left: 0;
        width: 100%; /* ให้เต็มหน้าจอ */
        height: 100%; /* ให้เต็มหน้าจอ */
        object-fit: cover; /* ครอบคลุมพื้นที่ */
        z-index: -1; /* ให้อยู่ด้านหลังเนื้อหา */
    }

    .container {
        max-width: 400px; /* กำหนดความกว้างสูงสุดของฟอร์ม */
        margin: 50px auto; /* จัดกลางหน้า */
        height: 80%;

        padding: 20px; /* ระยะห่างภายใน */
        border-radius: 8px; /* มุมมน */
    }

    h1 {
        text-align: left;
        margin-bottom: 10%;
    }
    p {
        color: #b3b3b3;
    }
    form {
        display: flex;
        flex-direction: column; /* จัดเรียงแบบแนวตั้ง */
    }

    label {
        display: block; /* ทำให้ label ใช้พื้นที่ทั้งหมด */
        margin-bottom: 5px; /* ระยะห่างด้านล่าง */
        font-weight: bold; /* ตัวหนา */
    }

    input[type="text"],
    input[type="password"],
    input[type="submit"],
    .Register {
        width: 100%; /* กว้างเต็มที่ */
        padding: 12px; /* ระยะห่างภายใน */
        margin-bottom: 15px; /* ระยะห่างด้านล่าง */
        border: 1px solid #ccc; /* เส้นขอบ */
        border-radius: 4px; /* มุมมน */
        box-sizing: border-box; /* ป้องกันไม่ให้ขอบเกินขนาด */
        font-size: 16px; /* ขนาดฟอนต์ */
    }

    /* ปรับปรุงเพื่อจัดตำแหน่งข้อความในปุ่ม */
    input[type="submit"],
    .Register {
        background-color: #007BFF; /* สีพื้นหลังของปุ่ม */
        color: white; /* สีข้อความในปุ่ม */
        border: none; /* ไม่มีเส้นขอบ */
        cursor: pointer; /* เปลี่ยนเคอร์เซอร์เป็นมือ */
        transition: background-color 0.3s; /* การเปลี่ยนสีพื้นหลังเมื่อโฮเวอร์ */
        display: flex; /* ใช้ flexbox เพื่อจัดตำแหน่ง */
        justify-content: center; /* จัดให้อยู่กลางในแนวนอน */
        align-items: center; /* จัดให้อยู่กลางในแนวตั้ง */
        height: 50px; /* กำหนดความสูงของปุ่ม */
        text-decoration: none; /* ลบขีดใต้ล่าง */
    }

    /* ปรับปรุงให้ Register เป็นปุ่ม */
    .Register {
        background-color: #007BFF; /* สีพื้นหลังของปุ่ม */
    }

    input[type="submit"]:hover,
    .Register:hover {
        background-color: #0056b3; /* เปลี่ยนสีเมื่อโฮเวอร์ */
    }

    /* CSS สำหรับ OR */
    .or-container {
        display: flex; /* ใช้ flexbox เพื่อจัดการตำแหน่ง */
        align-items: center; /* จัดให้อยู่กลางในแนวนอน */
        justify-content: center; /* จัดให้อยู่กลางในแนวตั้ง */
        margin: 10px 0; /* ระยะห่างด้านบนและล่าง */
    }

    .or-container label {
        margin: 0 10px; /* ระยะห่างระหว่าง label และปุ่ม */
    }

    /* เพิ่มระยะห่างให้มีความเท่าเทียม */
    .or-container {
        padding: 10px 0; /* เพิ่ม padding ด้านบนและล่าง */
    }
    </style>
</head>
<body>
    <video class="video-background" autoplay loop muted>
        <source src="pic/bk.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
    <div class="container">
        <h1>Login</h1>
        <form action="" method="POST">
            <label for="user">Username</label>
            <input type="text" name="username" id="text" placeholder="Username" required>
    
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required>
    
            <input type="submit" value="Login">
    
            <label for="OR" class="or-container">OR</label>
            <a href="register.php" class="Register">Register</a>
        </form>
        <p>If you don't have an account yet, you can register to access it.</p>
        <?php if ($error_message): ?>
            <p style="color:red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
