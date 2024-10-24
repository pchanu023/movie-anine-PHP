<?php
$servername = "localhost";
$username = "u299560388_651201";
$password = "UL2690Bg";
$dbname = "u299560388_651201";
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งค่ามาแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $synopsis = $_POST['synopsis'];
    $episode_count = $_POST['episode_count'];
    $price = $_POST['price'];
    $clip_url = $_POST['clip_url']; // รับค่าจากฟิลด์ clip_url

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if ($_FILES['image']['name']) {
        $image = 'pic/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);

        // อัปเดตรูปภาพในตาราง anime และ animedetails
        $sql = "UPDATE anime SET title='$title', genreID='$genre', image='$image' WHERE id='$id'";
        $sql_details = "UPDATE animedetails SET synopsis='$synopsis', episode_count='$episode_count', price='$price', image='$image' WHERE anime_id='$id'";
    } else {
        // อัปเดตข้อมูลโดยไม่เปลี่ยนรูปภาพ
        $sql = "UPDATE anime SET title='$title', genreID='$genre' WHERE id='$id'";
        $sql_details = "UPDATE animedetails SET synopsis='$synopsis', episode_count='$episode_count', price='$price' WHERE anime_id='$id'";
    }

    // อัปเดต clip_url ใน anime_trailers
    $sql_trailer = "UPDATE anime_trailers SET clip_url='$clip_url' WHERE anime_id='$id'";

    // รันคำสั่ง SQL
    if ($conn->query($sql) === TRUE && $conn->query($sql_details) === TRUE && $conn->query($sql_trailer) === TRUE) {
        echo "Record updated successfully";
        header("Location: admin.php"); // กลับไปหน้าหลัก
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // ดึงข้อมูลอนิเมะที่ต้องการแก้ไข
    $id = $_GET['id'];
    $sql = "SELECT anime.id, anime.title, anime.image, animedetails.synopsis, animedetails.episode_count, animedetails.price, animedetails.image AS detail_image, anime.genreID, t.clip_url 
            FROM anime 
            JOIN animedetails ON anime.id = animedetails.anime_id 
            LEFT JOIN anime_trailers t ON anime.id = t.anime_id 
            WHERE anime.id='$id'";
    $result = $conn->query($sql);
    $anime = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anime</title>
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end; /* Aligns buttons to the right */
            gap: 10px; /* Space between buttons */
        }

        input[type="submit"], .back-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover, .back-button:hover {
            background-color: #218838;
        }

        img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .back-button {
            background-color: #007bff;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>แก้ไขอนิเมะ</h1>
    <form action="editanime.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($anime['id']); ?>">

        <label for="title">ชื่ออนิเมะ:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($anime['title']); ?>" required>

        <label for="genre">ประเภทอนิเมะ:</label>
        <select name="genre" required>
            <option value="1" <?php if ($anime['genreID'] == 1) echo 'selected'; ?>>Fantasy</option>
            <option value="2" <?php if ($anime['genreID'] == 2) echo 'selected'; ?>>Action</option>
            <option value="3" <?php if ($anime['genreID'] == 3) echo 'selected'; ?>>Drama</option>
            <option value="4" <?php if ($anime['genreID'] == 4) echo 'selected'; ?>>Comedy</option>
            <option value="5" <?php if ($anime['genreID'] == 5) echo 'selected'; ?>>Sport</option>
            <option value="6" <?php if ($anime['genreID'] == 6) echo 'selected'; ?>>Gourmet</option>
        </select>

        <label for="image">เลือกรูปภาพใหม่ (หากต้องการเปลี่ยน):</label>
        <input type="file" name="image" accept="image/*">
        <img src="<?php echo htmlspecialchars($anime['detail_image']); ?>" alt="Current Anime Image">

        <label for="synopsis">เรื่องย่อ:</label>
        <textarea name="synopsis" required><?php echo htmlspecialchars($anime['synopsis']); ?></textarea>

        <label for="episode_count">จำนวนตอน:</label>
        <input type="number" name="episode_count" value="<?php echo htmlspecialchars($anime['episode_count']); ?>" required>

        <label for="price">ราคา:</label>
        <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($anime['price']); ?>" required>

        <label for="clip_url">URL คลิปตัวอย่าง:</label>
        <input type="text" name="clip_url" value="<?php echo htmlspecialchars($anime['clip_url']); ?>" required>

        <div class="button-container">
            <input type="submit" value="บันทึกการเปลี่ยนแปลง">
            <a href="admin.php" class="back-button">ย้อนกลับ</a>
        </div>
    </form>
</div>

</body>
</html>
