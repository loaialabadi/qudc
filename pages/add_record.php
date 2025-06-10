<?php
session_start();
include "connect.php";

// إدخال البيانات
if (isset($_POST['id'], $_POST['name'], $_POST['username'], $_POST['address'], $_POST['date'])) {
    $idu = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nameu = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $usernameu = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $adreussu = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $thedate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);

    // إدخال السجل في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO information (id, name, username, address, date) VALUES (:id, :name, :username, :address, :date)");
    $stmt->bindParam(':id', $idu);
    $stmt->bindParam(':name', $nameu);
    $stmt->bindParam(':username', $usernameu);
    $stmt->bindParam(':address', $adreussu);
    $stmt->bindParam(':date', $thedate);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div>فشل إدخال البيانات.</div>";
    }
}
