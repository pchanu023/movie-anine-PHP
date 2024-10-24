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

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง anime และ animedetails
$sql = "SELECT anime.id, anime.title, anime.image, animedetails.synopsis 
        FROM anime 
        JOIN animedetails ON anime.id = animedetails.anime_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime List</title>
    <style>
        .container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
}

/* เส้นใต้ th */
th {
    background-color: #f2f2f2;
    border-bottom: 3px solid black; /* เส้นแนวนอนใต้ th */
}

/* เส้นแนวตั้งระหว่างคอลัมน์ */
td, th {
    border-left: 2px solid black; /* เส้นแนวตั้ง */
}

/* ยกเว้นเส้นแนวตั้งที่ขอบสุด */
td:first-child, th:first-child {
    border-left: none;
}

/* จัดการกับข้อความที่ยาวเกินไป */
td {
    word-wrap: break-word;
    word-break: break-all;
    white-space: normal; /* อนุญาตให้ข้อความตัดบรรทัด */
}

/* ปุ่มใน button-group */
.button-group {
    display: flex;
    gap: 10px; /* เพิ่มช่องว่างระหว่างปุ่ม 10px */
}

.button-group a {
    padding: 10px 20px; /* เพิ่มขนาดของปุ่ม */
    text-align: center; /* จัดข้อความให้อยู่กึ่งกลางในปุ่ม */
    text-decoration: none;
    border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    transition: background-color 0.3s;
    white-space: nowrap; /* ป้องกันข้อความตัดบรรทัด */
    flex-grow: 0; /* ไม่ยืดตามพื้นที่ */
}

.button-group a.edit-btn {
    background-color: #ffc107; /* สีเหลือง */
    color: black; /* สีดำ */
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

.button-group a.edit-btn:hover {
    background-color: #e0a800; /* สีเหลืองเข้ม */
}

/* จัดการภาพ */
img {
    max-width: 100px;
    height: auto;
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

/* จำกัดความกว้างของ synopsis */
td.synopsis {
    max-width: 250px;  /* กำหนดความกว้างสูงสุดของคอลัมน์ */
    word-wrap: break-word;  /* อนุญาตให้ตัดบรรทัด */
    white-space: normal;  /* อนุญาตให้ข้อความตัดบรรทัด */
    overflow: hidden;  /* ซ่อนข้อความส่วนที่เกิน */
    text-overflow: ellipsis;  /* แสดง ... เมื่อข้อความถูกตัด */
}


    </style>
</head>
<body style="background-color: #f9f9f9;">

<div class="container">
    <h2>
        ข้อมูลอนิเมะ
        <a href="admin.php" class="back-button">ย้อนกลับ</a>
    </h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Synopsis</th> <!-- เพิ่มคอลัมน์ Synopsis -->
                <th>Image</th>
                <th>เพิ่มเติม</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($anime = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($anime['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($anime['synopsis']) . "</td>"; // แสดง synopsis
                    echo "<td><img src='" . htmlspecialchars($anime['image']) . "' alt='" . htmlspecialchars($anime['title']) . "'></td>";
                    echo "<td class='button-group'>
                            <a href='editanime.php?id=" . htmlspecialchars($anime['id']) . "' class='edit-btn'>Edit</a>
                            <a href='deleteanime.php?id=" . htmlspecialchars($anime['id']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this anime?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>ไม่พบข้อมูลอนิเมะ</td></tr>"; // ปรับให้คอลัมน์รวมเป็น 4
            }
            // ปิดการเชื่อมต่อ
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
