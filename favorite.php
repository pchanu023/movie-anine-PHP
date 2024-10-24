<?php
// เปิดแสดงข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$db_username = "u299560388_651201"; // ชื่อผู้ใช้ของคุณ
$db_password = "UL2690Bg"; // รหัสผ่านของคุณ
$dbname = "u299560388_651201"; // ชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงชื่อผู้ใช้งานจากตาราง users โดยใช้ user_id
    $sql_username = "SELECT username FROM users WHERE id = ?"; // แก้ไขเป็น 'id' เพื่อให้ตรงกับตาราง
    $stmt_username = $conn->prepare($sql_username);
    $stmt_username->bind_param("i", $user_id); // ผูกพารามิเตอร์ user_id
    $stmt_username->execute();
    $stmt_username->store_result();
    $stmt_username->bind_result($username); // ผูกตัวแปร username
    $stmt_username->fetch(); // ดึงข้อมูลชื่อผู้ใช้
    $stmt_username->close();

    // ดึงข้อมูลรายการโปรดของผู้ใช้
    $sql = "SELECT favorites.anime_id, animedetails.id, animedetails.title, animedetails.synopsis,
                   animedetails.episode_count, animedetails.image, animedetails.price, animedetails.genreID
            FROM favorites 
            INNER JOIN animedetails ON favorites.anime_id = animedetails.id
            WHERE favorites.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // ผูกพารามิเตอร์ user_id
    $stmt->execute();
    $stmt->store_result(); // จัดเก็บผลลัพธ์
    $stmt->bind_result($favorite_anime_id, $anime_id, $title, $synopsis, $episode_count, $image, $price, $genreID); 

    // เก็บผลลัพธ์ในอาร์เรย์
    $anime_items = [];
    while ($stmt->fetch()) {
        $anime_items[] = [
            'id' => $anime_id,
            'title' => $title,
            'synopsis' => $synopsis,
            'episode_count' => $episode_count,
            'image' => $image,
            'price' => $price,
            'genreID' => $genreID
        ];
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorite Aninne</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    margin: 0;
    font-family: 'Helvetica Neue', Arial, sans-serif;
    background-color: #F5F5F5;
    color: #333;
}

.container {
    display: flex;
    position: relative;
}

.sidebar {
    width: 0;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background-color: #fff;
    overflow-x: hidden;
    transition: width 0.3s ease-in-out;
    padding-top: 60px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar-open {
    width: 250px;
}

.sidebar a {
    padding: 12px 20px;
    text-decoration: none;
    font-size: 18px;
    color: #555;
    display: block;
    transition: 0.3s;
}

.sidebar a:hover {
    background-color: #f1f1f1;
    color: #222;
}

.sidebar .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
    color: #333;
}

.main-content {
    margin-left: 0;
    padding: 20px;
    flex: 1;
    transition: margin-left 0.3s ease;
    overflow-y: auto;
    box-sizing: border-box;
}

.sidebar-open + .main-content {
    margin-left: 250px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
    padding: 15px 25px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.menu-icon {
    font-size: 30px;
    cursor: pointer;
    color: #333;
}

.search-bar {
    width: 300px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 25px;
    background-color: #fff;
    outline: none;
    font-size: 16px;
}

.search-bar:focus {
    border-color: #5B8C5A;
}

.right-icons {
    display: flex;
    align-items: center;
}

.right-icons a {
    margin-right: 20px;
    text-decoration: none;
    color: #5B8C5A;
    font-size: 16px;
    font-weight: bold;
}

.right-icons a:hover {
    color: #333;
}

.right-icons img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #ddd;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

        .anime-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr); /* แสดง 6 รายการต่อแถว */
            gap: 20px;

        }

        .anime-item {
            text-align: center;
            transition: transform 0.3s ease;
            margin: 5px;
        }

        .anime-item:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }

        .anime-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

.anime-item:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
}


.anime-title {
            margin-top: 10px;
            font-size: 16px;
            text-align: center;
            color: #333;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <a href="#" class="closebtn" onclick="toggleSidebar()">×</a>
            <a href="homepage.php">Home</a>
            <a href="Fantasy.php">Fantasy</a>
            <a href="Action.php">Action</a>
            <a href="Drama.php">Drama</a>
            <a href="Comedy.php">Comedy</a>
            <a href="Sport.php">Sport</a>
            <a href="Gourmet.php">Gourmet</a>
            <br><br><br><br><br>
            <a href="#">Favorite</a>
            <a href="Library.php">Library</a>
        </div>

        <div class="main-content">
            <div class="header">
                <span class="menu-icon" onclick="toggleSidebar()">&#9776;</span>
                <input type="text" class="search-bar" placeholder="search">
                <div class="right-icons">
                    <a href="loginn.php">Log out</a>
                    <a href="profile.php?username=<?php echo urlencode($username); ?>">
                        <span style="color: #0056B3; text-decoration: underline;"><?php echo htmlspecialchars($username); ?></span>
                    </a>
                </div>
            </div>

            <div class="anime-grid">
            <?php
                if (!empty($anime_items)) {
                    foreach ($anime_items as $item) {
                        echo '<div class="anime-item">';
                        echo '<a href="anime_detail.php?id=' . $item["id"] . '">
                                <img src="' . htmlspecialchars($item["image"]) . '" alt="' . htmlspecialchars($item["title"]) . '">
                              </a>';
                        echo '<div class="anime-title">' . htmlspecialchars($item["title"]) . '</div>'; // แก้ไขตรงนี้
                        echo '</div>';
                    }
                } else {
                    echo "<p>No favorite anime found for your account.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("sidebar-open");
        }
    </script>
</body>
</html>
