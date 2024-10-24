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

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง users
$sql = "SELECT id, first_name, last_name, email, username FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <style>
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-group a {
            padding: 5px 10px;
            text-decoration: none;
            margin-right: 5px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
        }

        .button-group a.delete-btn {
            background-color: #dc3545;
        }

        .button-group a:hover {
            background-color: #0056b3;
        }

        .button-group a.delete-btn:hover {
            background-color: #c82333;
        }
        
        
/* ปุ่มย้อนกลับ */
.back-button {
    display: inline-block;
    padding: 5px 10px;
    margin-left: 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.back-button:hover {
    background-color: #0056b3;
}


        
    </style>
</head>
<body>

<div class="container">
    <h2>ข้อมูลรายชื่อผู้ใช้
    <a href="admin.php" class="back-button">ย้อนกลับ</a>
    </h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>เพิ่มเติม</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($user = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['first_name'] . " " . $user['last_name']) . "</td>";
                    echo "<td class='button-group'>
                            <a href='view.php?id=" . htmlspecialchars($user['id']) . "'>View</a>
                            <a href='delete.php?id=" . htmlspecialchars($user['id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>ไม่พบข้อมูลผู้ใช้</td></tr>";
            }
            // ปิดการเชื่อมต่อ
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
