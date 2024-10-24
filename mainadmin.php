<?php
session_start();
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

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ถ้าล็อกอินสำเร็จ
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_type'] = $row['user_type']; // เก็บ user_type ใน session

        // ถ้าเป็น admin ให้ไปหน้า admin_dashboard.php
        if ($row['user_type'] == 'admin') {
            header("Location: mainadmin.php");
        } else {
            // ถ้าไม่ใช่ admin ให้ไปหน้า home.php หรือหน้าอื่นๆ
            header("Location: homepage.php");
        }
        exit();
    } else {
        echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aninne Management</title>
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
        .anime-feature {
    position: relative;
    width: 90%;
    max-width: 1200px; /* สามารถปรับขนาดได้ตามความต้องการ */
    margin: auto;
    overflow: hidden;
    margin-top: 3%;
}

.anime-feature img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.anime-info {
    position: absolute;
    bottom: 20px;
    left: 20px;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    max-width: 60%; /* จำกัดขนาดของกล่องข้อมูล */
}

 h2,h3 {
    font-size: 30px;
    margin: 0;
    margin-top: 3%;
    margin-left: 3%;
}
p {
    font-size: 24px;
}

h3 {
    width: 95%;
    height: 50px;
    background-color: white;
    display: flex;
    align-items: center; /* จัดข้อความให้อยู่ตรงกลางในแนวตั้ง */
    padding-left: 10px; /* เพิ่มระยะห่างทางซ้ายถ้าต้องการ */
    text-align: left; /* จัดข้อความให้อยู่ทางด้านซ้าย */
}

.anime-info p {
    margin-top: 5px;
    font-size: 16px;
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

        /* CSS ที่ปรับให้เข้ากับมือถือ */
@media (max-width: 1200px) {
    .anime-grid {
        grid-template-columns: repeat(4, 1fr); /* แสดง 4 รายการต่อแถว */
    }
}

@media (max-width: 900px) {
    .anime-grid {
        grid-template-columns: repeat(3, 1fr); /* แสดง 3 รายการต่อแถว */
    }
}

@media (max-width: 600px) {
    .anime-grid {
        grid-template-columns: repeat(2, 1fr); /* แสดง 2 รายการต่อแถว */
    }

    .search-bar {
        width: 100%; /* ให้ช่องค้นหามีความกว้างเต็ม */
    }

    .right-icons {
        flex-direction: column; /* เปลี่ยนการจัดเรียงให้เป็นแนวตั้ง */
    }
}

@media (max-width: 400px) {
    .anime-grid {
        grid-template-columns: 1fr; /* แสดง 1 รายการต่อแถว */
    }

    h2, h3 {
        font-size: 24px; /* ปรับขนาดตัวอักษรให้เล็กลง */
    }

    p {
        font-size: 16px; /* ปรับขนาดตัวอักษรให้เล็กลง */
    }

    .header {
        flex-direction: column; /* จัดให้ header อยู่ในแนวตั้ง */
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <a href="#" class="closebtn" onclick="toggleSidebar()">×</a>
            <a href="mainadmin.php">Home</a>
            <a href="admin.php">Admin</a>
            <a href="add.php">Addd</a>
            <a href="users.php">Manage Users</a>
            <a href="viewanime.php">Site Settings</a>

        </div>

        <div class="main-content">
            <div class="header">
                <span class="menu-icon" onclick="toggleSidebar()">&#9776;</span>
                <input type="text" class="search-bar" placeholder="search">
                <div class="right-icons">
                    <a href="login.php">Log out |</a>
                    <a href="profile.php?username=<?php echo urlencode($username); ?>">
    <span style="color: #0056B3; text-decoration: underline;"><?php echo htmlspecialchars($username); ?></span>
</a>

                </div>
            </div>

            <div class="anime-grid">
            <?php
                // ดึงข้อมูลจาก MySQL
                $sql = "SELECT id, title, image FROM anime"; // ตรวจสอบให้แน่ใจว่าเลือก 'id' สำหรับลิงก์
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="anime-item">';
                        echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>'; // ลิงก์ไปยังหน้าแสดงรายละเอียด
                        echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "ไม่มีข้อมูลอนิเมะประเภท Fantasy";
                }

                ?>
            </div>
            <h2>Comimg soon</h2>
            <div class="anime-feature">
                <img src="pic/rezero.jpg" alt="Anime Title">
                <div class="anime-info">
                    <h2>Re:ZERO</h2>
                    <p>เเรื่องราวของ Natsuki Subaru เด็กหนุ่มที่กำลังเดินทางกลับบ้าน หลังจากแวะซื้อของที่ร้านสะดวกซื้อ 
                        แต่กลับถูกเรียกไปยังต่างโลกแบบกระทันหัน ในต่างโลก Subaru มีพลังในการย้อนเวลาด้วยการตาย 
                        เขาจึงใช้พลังนี้ให้เกิดประโยชน์เพื่อปกป้องคนที่รักและเผชิญหน้ากับโชคชะตาที่สิ้นหวัง
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Fantasy</h3>
    <br><br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Fantasy";
        }
        ?>
        </div>
        </div>

        <h2>Comimg soon</h2>
        <div class="anime-feature">
                <img src="pic/dadadan.jpg" alt="Anime Title">
                <div class="anime-info">
                    <h2>Dandadan</h2>
                    <p>โมโมะ อายาเสะ เด็กสาวมัธยมปลายที่เชื่อมั่นในผีอย่างแน่วแน่ แต่ปฏิเสธการมีอยู่ของมนุษย์ต่างดาว 
                        วันหนึ่งเธอได้พบกับเด็กชายคนหนึ่งที่เชื่อตรงกันข้าม เขาหลงใหลในมนุษย์ต่างดาวแต่ปฏิเสธว่าผีนั้นมีจริง 
                        ทั้งสองคนมีการโต้เถียงกันอย่างดุเดือด ในที่สุดก็ทำการเดิมพัน โดยแต่ละคนจะไปเยี่ยมสถานที่ที่เกี่ยวข้องกับความเชื่อของอีกฝ่าย 
                        อายาเสะ ไปยังที่มีมนุษย์ต่างดาว และเด็กชายอีกคนไปยังสถานที่ผีสิง พวกเขาต้องตกใจเมื่อพบว่าทั้งมนุษย์ต่างดาวและผีนั้นมีอยู่จริง 
                        จนกลายเป็นจุดเริ่มต้นของการผจญภัยที่แปลกประหลาดและอันตรายของพวกเขา
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Action</h3>
    <br><br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 2";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Action";
        }
        ?>
        </div>
        </div>

        <h2>Comimg soon</h2>
        <div class="anime-feature">
                <img src="pic/Nega.jpg" alt="Anime Title">
                <div class="anime-info">
                    <h2>NegaPosi Angler</h2>
                    <p>เรื่องราวของสึเนะฮิโระ ซาซากิ นักศึกษาที่เพิ่งได้รับการวินิจฉัยว่าป่วยด้วยโรคร้ายแรง และมีเวลาเหลืออีกเพียง 2 ปีที่จะมีชีวิตอยู่ได้ 
                        หลังจากตกลงไปในทะเลโดยไม่ได้ตั้งใจ เขาก็ได้ผูกมิตรกับฮานะ อายุกาวะ ผู้ช่วยชีวิตเขาไว้ เขาเริ่มสนใจการตกปลาร่วมกับทากาอากิ 
                        สึตสึจิโมริ เพื่อนของเธอ
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Drama</h3>
    <br><br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Drama";
        }
        ?>
        </div>
        </div>

        <h2>Comimg soon</h2>
        <div class="anime-feature">
                <img src="pic/Katsute.jpg" alt="Anime Title">
                <div class="anime-info">
                    <h2>Katsute Mahou Shoujo to Aku wa Tekitai Shite Ita</h2>
                    <p>มิระ (มิลเลอร์) ปีศาจระดับสูงระดับมือขวาของหัวหน้าองค์กรชั่วร้ายที่หวังทำลายทุกสิ่ง 
                    จนได้พบกับสาวน้อยเวทมนตร์ มิโมริ เบียคุยะ ศัตรูขององค์กร สาวที่ทำให้เขาตกหลุมรักตั้งแต่แรกเห็น 
                    โดยซ่อนความรู้สึกนั้นไว้ไม่ให้คนอื่นเห็น
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Comedy</h3>
    <br><br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 4";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Comedy";
        }
        ?>
        </div>
        </div>

        <h2>Comimg soon</h2>
        <div class="anime-feature">
                <img src="pic/B.jpg" alt="Anime Title">
                <div class="anime-info">
                    <h2>Blue Lock</h2>
                    <p>เรื่องราวของ โยอิจิ อิซางิ นักฟุตบอลดาวรุ่งที่มีความฝันอันยิ่งใหญ่ หลังจากความพ่ายแพ้อันน่าหัวใจสลาย
                    ทำให้ความหวังในการแข่งขันฟุตบอลโลกของญี่ปุ่นพังทลาย สมาคมฟุตบอลญี่ปุ่นได้ทำการเปิดตัวโครงการสุดระห่ำที่เรียกว่า Blue Lock นำโดย เอโงะ จินปาจิ
                    กองหน้าระดับท็อป 300 คนของญี่ปุ่น มาดวลกันในการแข่งขันที่เข้มข้น เดิมพันสูง ณ สถานฝึกอบรมอันเงียบสงบแห่งนี้
                    การแข่งขันอันดุเดือด ผู้แพ้จะถูกคัดออก เหลือเพียงคนเดียวที่จะกลายเป็นกองหน้าที่ญี่ปุ่นต้องการเพื่อคว้าแชมป์ฟุตบอลโลก
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Sport</h3>
    <br>
    <br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 5";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Sport";
        }
        ?>
        </div>
        </div>

        <h2>Comimg soon</h2>
        <div class="anime-feature">
                <img src="pic/Himesama.avif" alt="Anime Title">
                <div class="anime-info">
                    <h2>Okashi na Tensei</h2>
                    <p>ภายใต้ความขัดแย้งระหว่างทัพราชอาณาจักรและทัพจอมมารที่เริ่มต้นขึ้นเมื่อนานแสนนาน 
                        องค์หญิงที่สืบสายเลือดจากองค์ราชาและเป็น หัวหน้ากองอัศวินของกองอัศวินที่ 3 แห่งกองทัพราชอาณาจักรถูกทัพจอมมารจับตัวได้เสียแล้ว!
                        และสิ่งที่รอคอยองค์หญิงอยู่ก็คือ การทรมานที่เหนือความคาดหมาย นับไม่ถ้วนงั้นหรือ เวลาแห่งการทรมานอันแสนโหดเหี้ยมได้เริ่มต้นขึ้นแล้ว!! 
                    </p>
                </div>
            </div>

            
            <div class="anime-section">
    <h3>Gourmet</h3>
    <br>
    <br>
    <div class="anime-grid">
        <?php
        // ดึงข้อมูลจาก MySQL สำหรับ anime ที่เป็น Fantasy (genreID = 1)
        $sql = "SELECT id, title, image FROM anime WHERE genreID = 6";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="anime-item">';
                echo '<a href="detail.php?id=' . $row["id"] . '"><img src="' . $row["image"] . '" alt="' . $row["title"] . '"></a>';
                echo '<div class="anime-title">' . htmlspecialchars($row["title"]) . '</div>';
                echo '</div>';
            }
        } else {
            echo "ไม่มีข้อมูลอนิเมะประเภท Gourmet";
        }
        ?>
        </div>
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
