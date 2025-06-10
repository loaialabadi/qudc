<?php
session_start();
include "connect.php";

// تأكد من تسجيل الدخول
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// تحديث البيانات
if (isset($_POST['id'])) {
    $idu = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nameu = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $usernameu = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $adreussu = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $thedate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);

    $update = $conn->prepare("UPDATE information SET name = :name, username = :username, address = :address, date = :date WHERE id = :id");
    $update->bindParam(':id', $idu);
    $update->bindParam(':name', $nameu);
    $update->bindParam(':username', $usernameu);
    $update->bindParam(':address', $adreussu);
    $update->bindParam(':date', $thedate);

    if ($update->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<div>فشل تحديث البيانات.</div>";
    }
}
