<?php
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

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT); // เข้ารหัสรหัสผ่าน

    // อัปโหลดไฟล์รูปภาพ
    $target_dir = "pic/"; // โฟลเดอร์เก็บรูปภาพใน FileZilla
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $profile_picture = $target_file;
    } else {
        $profile_picture = null;
    }

    // เพิ่มข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO users (first_name, last_name, email, username, password, profile_picture)
            VALUES ('$firstName', '$lastName', '$email', '$user', '$pass', '$profile_picture')";

    if ($conn->query($sql) === TRUE) {
        echo "สมัครสมาชิกสำเร็จ";
        header("Location: login.php"); // ไปที่หน้าเข้าสู่ระบบ
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Aninne</title>
    <style>
           * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
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
    width: 150vh;
    display: flex;

    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    gap: 20%; /* เพิ่มระยะห่างระหว่างสอง div */
}

.profile-pic {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 50px;
}

.profile-img {
    width: 300px;
    height: 400px;
    border: 2px solid #dddddd;
    /* ลบ border-radius เพื่อให้เป็นรูปสี่เหลี่ยม */
    object-fit: cover;
    margin-top: 8%;
    margin-left: 50%;
}

.upload-icon {
    position: relative;
    bottom: 0;
    right: 0;
}

.upload-icon input[type="file"] {
    display: none;
}

.upload-icon label {
    cursor: pointer;
}

.upload-icon img {
    width: 30px;
    height: 30px;
}

.form-container {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #dddddd;
    border-radius: 4px;
    font-size: 14px;
}

.register-btn {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    text-align: center;
    margin-top: 5%;
}

.register-btn:hover {
    background-color: #0056b3;
}
p {
    color: gray;
    margin-top: 3%;
}
    </style>
</head>
<body>
<video class="video-background" autoplay loop muted>
        <source src="pic/bk.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="container">
        <div class="profile-pic">
            <img id="profileImage" src="user-icon.png" alt="Profile" class="profile-img">
            <div class="upload-icon">
                <input type="file" id="fileUpload" accept="image/*" onchange="previewImage(event)">
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="firstName">First Name</label>
        <input type="text" name="firstName" placeholder="First Name">
    </div>
    <div class="form-group">
        <label for="lastName">Last Name</label>
        <input type="text" name="lastName" placeholder="Last Name">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email">
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Username">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password">
    </div>
    <input type="file" name="profile_picture" id="fileUpload" accept="image/*" onchange="previewImage(event)">
    <button type="submit" class="register-btn">Register</button>
    <p>If you already have an account. <a href="login.php">Login</a></p>
</form>
                
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const imageField = document.getElementById("profileImage");

            reader.onload = function() {
                if (reader.readyState === 2) {
                    imageField.src = reader.result; // ตั้งค่า src ของ <img> เป็นภาพที่อัปโหลด
                }
            }
            reader.readAsDataURL(event.target.files[0]); // อ่านไฟล์ภาพที่อัปโหลด
        }
    </script>
</body>
</html>
