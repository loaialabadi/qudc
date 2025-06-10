<?php
session_start();
include "connect.php";

// تأكد من تسجيل الدخول
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// حذف البيانات
if (isset($_POST['id'])) {
    $id_to_delete = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id_to_delete) {
        $del_f_db = $conn->prepare("DELETE FROM information WHERE id = :id");
        $del_f_db->bindParam(':id', $id_to_delete);

        if ($del_f_db->execute()) {
            echo "<div class='success'>تم تسجيل المستخدم بنجاح!</div>";
          header("location: dashboard.php");
            exit();
        } else {
            echo "<div>فشل الحذف</div>";
        }
    } else {
        echo "<div>الرجاء إدخال ID صحيح.</div>";
    }
}


?>