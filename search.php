<?php
// search.php

// ตรวจสอบว่ามีการส่งคำค้นหามาหรือไม่
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // เชื่อมต่อกับฐานข้อมูล
    $conn = new mysqli("localhost", "username", "password", "animeDB");

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL สำหรับค้นหาชื่ออนิเมะที่มีคำค้นหา
    $sql = "SELECT title, image FROM anime WHERE title LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();

    // รับผลลัพธ์จากฐานข้อมูล
    $result = $stmt->get_result();
    $animeList = [];

    while ($row = $result->fetch_assoc()) {
        $animeList[] = $row;
    }

    // ส่งข้อมูลกลับไปเป็น JSON
    echo json_encode($animeList);

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
}
?>
