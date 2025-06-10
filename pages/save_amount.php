<?php

require "connect.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST['amount'];
    $id = $_POST['id'];

    

    $sql = "UPDATE information SET amount = :amount WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // ربط المتغيرات بالـ SQL
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ربط ID العميل
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);  // ربط المساحة

    // تنفيذ الاستعلام
    $stmt->execute();

    
    echo "saved $amount";
}
?>
