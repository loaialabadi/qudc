<?php

require "connect.php";  // ربط قاعدة البيانات
require "home_page.php";  // ربط صفحة البداية أو الصفحة الرئيسية

// تحديث الحالة
if (isset($_POST['update_status'])) {
    if (isset($_POST['new_status'])) {
        $id = $_POST['id'];
        $new_status = $_POST['new_status'];

        $sql = "UPDATE information SET status = :new_status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':new_status', $new_status, PDO::PARAM_INT); // تحديد النوع كـ INTEGER
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // تحديد النوع كـ INTEGER

        if ($stmt->execute()) {
            echo "تم تحديث الحالة بنجاح!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "خطأ في تحديث الحالة.";
        }
    }
}

// تحديث المساحة الفعلية
if (isset($_POST['update_area'])) {
    $id = $_POST['id'];
    $the_actual_area = $_POST['the_actual_area'];
    $dategis = $_POST['dategis']; // تاريخ اليوم
    $giswork = $_POST['giswork']; // اسم المستخدم من الجلسة

    $query = "UPDATE information SET the_actual_area = :the_actual_area, dategis = :dategis, giswork = :giswork WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':the_actual_area', $the_actual_area, PDO::PARAM_STR); // تحديد النوع كـ STRING
    $stmt->bindValue(':dategis', $dategis, PDO::PARAM_STR); // تحديد النوع كـ STRING
    $stmt->bindValue(':giswork', $giswork, PDO::PARAM_STR); // تحديد النوع كـ STRING
    $stmt->bindValue(':id', $id, PDO::PARAM_INT); // تحديد النوع كـ INTEGER

    if ($stmt->execute()) {
        echo "تم تحديث المساحة بنجاح.";
    } else {
        echo "حدث خطأ أثناء تحديث المساحة.";
    }
}

// استعلام لاسترجاع البيانات
$sql = "SELECT id, customer_name, request_type, phone_number, national_number, inspector, center, address, status, date_of_work, the_space, the_actual_area, giswork, dategis FROM information";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- عرض البيانات في جدول HTML -->
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة البيانات</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- ربط ملف CSS الخارجي -->
</head>
<body>

<table class="page-system" border="1">
    <tr>
        <th>رقم الطلب</th>
        <th>نوع الطلب</th>
        <th>اسم العميل</th>
        <th>رقم التليفون</th>
        <th>القائم</th>
        <th>المركز</th>
        <th>العنوان</th>
        <th>رقم البطاقه</th>
        <th>تاريخ المعاينة</th>
        <th>المساحة المورد عليها</th>
        <th>المساحة الفعلية</th>
        <th>تغيير الحالة</th>
    </tr>

    <?php
    // عرض السجلات
    if (count($results) > 0) {
        foreach ($results as $row) {
            // عرض الصفوف بناءً على الحالة
            if ($row["status"] == 6 || $row["status"] == 7) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["request_type"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["customer_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["phone_number"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["inspector"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["center"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["national_number"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date_of_work"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["the_space"]) . "</td>";

                // حقل إدخال المساحة الفعلية
                echo "<td>";
                echo '<form method="POST">';
                echo '<input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">';
                echo '<input type="text" name="the_actual_area" value="' . htmlspecialchars($row["the_actual_area"]) . '" required>';
                echo '<input type="hidden" name="dategis" value="' . date("Y-m-d") . '">';  // إضافة تاريخ اليوم
                echo '<input type="hidden" name="giswork" value="' . htmlspecialchars($_SESSION['username']) . '">';  // إضافة اسم المستخدم من الجلسة
                echo '<input type="submit" name="update_area" value="تحديث المساحة الفعلية">';
                echo '</form>';
                echo "</td>";

                // تغيير الحالة
                echo "<td>";
                if ($row["status"] == 6) {
                    echo '<form method="POST">
                            <input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">
                            <input type="hidden" name="new_status" value="7">
                            <input type="submit" name="update_status" value="استلام من العمل الميداني">
                          </form>';
                } elseif ($row["status"] == 7) {
                    echo '<form method="POST">
                            <input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">
                            <input type="hidden" name="new_status" value="8">
                            <input type="submit" name="update_status" value="اكتمال العملية">
                          </form>';
                }
                echo "</td>";
                echo "</tr>";
            }
        }
    } else {
        echo "<tr><td colspan='11' class='no-data'>لا توجد بيانات لعرضها.</td></tr>";
    }
    ?>
</table>

<script>
    function printSection() {
        var printContents = document.body.innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

</body>
</html>
