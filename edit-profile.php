<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "u299560388_651201";
$password = "UL2690Bg";
$dbname = "u299560388_651201";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_logged_in = $_SESSION['username'];
$sql = "SELECT first_name, last_name, email, username, profile_picture FROM users WHERE username = '$user_logged_in'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    if ($_FILES['profile_picture']['name']) {
        $profile_picture = 'pic/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);

        $sql_update = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', profile_picture='$profile_picture' WHERE username='$user_logged_in'";
    } else {
        $sql_update = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$user_logged_in'";
    }

    if ($conn->query($sql_update) === TRUE) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            display: block;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="button"]{
            background-color: red;
            display: block;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="button"]:hover{
            background-color: #5d0404;
        }
        input[type="submit"]:hover{
            background-color: #0056b3;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 75px;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px; /* เพิ่มระยะห่างด้านบนของปุ่ม */
        }

        .button-container input {
            margin-left: 10px; /* ระยะห่างระหว่างปุ่ม */
        }
    </style>
</head>
<body>

<div class="container">
    <h1>แก้ไขโปรไฟล์</h1>
    <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
        <label for="first_name">ชื่อ:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label for="last_name">นามสกุล:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label for="email">อีเมล:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="profile_picture">เลือกรูปโปรไฟล์ใหม่ (ถ้าต้องการเปลี่ยน):</label>
        <input type="file" name="profile_picture" accept="image/*">

        <div class="button-container">
            <input type="submit" value="บันทึกการเปลี่ยนแปลง">
            <input type="button" value="ย้อนกลับ" onclick="window.location.href='profile.php';"> <!-- เปลี่ยนเป็นปุ่มเพื่อกลับไปหน้าโปรไฟล์ -->
        </div>
    </form>
</div>

</body>
</html>
