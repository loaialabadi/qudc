<?php
require "connect.php"; // ملف الاتصال بقاعدة البيانات
require "home_page.php";

// متغيرات البحث
$status_filter = isset($_POST['status']) ? $_POST['status'] : 'all';
$center_filter = isset($_POST['center']) ? $_POST['center'] : '';

// بناء الاستعلام الأساسي لاختيار جميع الأعمدة
$sql = "SELECT * FROM information";  // حذف WHERE 1 لزيادة المرونة

$params = [];  // مصفوفة لتخزين قيم الفلاتر

// إضافة الفلاتر بناءً على المدخلات
$conditions = []; // مصفوفة لتخزين الشروط

// تحقق من الفلتر الحالة
if ($status_filter !== 'all' && $status_filter !== '') {
    $conditions[] = "status = :status";
    $params[':status'] = $status_filter;
}

// تحقق من فلتر المركز
if ($center_filter !== '') {
    $conditions[] = "center = :center";
    $params[':center'] = $center_filter;
}



// إذا كانت هناك شروط مضافة، نقوم بضمها إلى الاستعلام
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// تحضير الاستعلام
$stmt = $conn->prepare($sql);

// ربط المتغيرات في الاستعلام إذا كانت موجودة
foreach ($params as $key => $value) {
    $stmt->bindParam($key, $value);
}

// تنفيذ الاستعلام
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إذا تم الضغط على زر "تسليم"
if (isset($_POST['deliver'])) {
    $id = $_POST['id']; // الحصول على رقم الطلب
    $new_status = 9; // تحديد حالة جديدة (تسليم)
    $delivery_date = date('Y-m-d H:i:s'); // الحصول على التاريخ والوقت الحالي

    // تحديث الحالة و إضافة التاريخ في قاعدة البيانات
    $update_sql = "UPDATE information SET status = :status, deliverydate = :deliverydate WHERE id = :id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':deliverydate', $delivery_date);
    $update_stmt->bindParam(':id', $id);
    $update_stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']); // إعادة تحميل الصفحة بعد التحديث
    exit();
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البحث عن معلومات</title>
        <link rel="stylesheet" href="css/styles.css"> <!-- ربط ملف CSS الخارجي -->


</head>
<body>

<form action="" method="POST" class="delivery-form">
    <h2>بحث المتطلبات</h2>

    <!-- فلتر المركز
    <div class="delivery-filter">
        <label for="center">اختر المركز:</label>
        <select id="center" name="center">
            <option value="">اختر المركز</option>
            <option value="قنا" <?php if ($center_filter == 'قنا') echo 'selected'; ?>>قنا</option>
            <option value="دشنا" <?php if ($center_filter == 'دشنا') echo 'selected'; ?>>دشنا</option>
            <option value="نجع حمادي" <?php if ($center_filter == 'نجع حمادي') echo 'selected'; ?>>نجع حمادي</option>
            <option value="قوص" <?php if ($center_filter == 'قوص') echo 'selected'; ?>>قوص</option>
            <option value="قفط" <?php if ($center_filter == 'قفط') echo 'selected'; ?>>قفط</option>
            <option value="ابوتشت" <?php if ($center_filter == 'ابوتشت') echo 'selected'; ?>>ابوتشت</option>
            <option value="فرشوط" <?php if ($center_filter == 'فرشوط') echo 'selected'; ?>>فرشوط</option>
            <option value="نقاده" <?php if ($center_filter == 'نقاده') echo 'selected'; ?>>نقاده</option>
            <option value="الوقف" <?php if ($center_filter == 'الوقف') echo 'selected'; ?>>الوقف</option>
        </select>
    </div> -->

    <!-- فلتر حالة الطلب -->
    <div class="delivery-filter">
        <label for="status">اختر الحالة:</label>
        <select id="status" name="status" required>
            <option value="8" <?php if ($status_filter == '8') echo 'selected'; ?>>تم الانتهاء</option>
        </select>
    </div>

    <input type="submit" value="بحث">
</form>

<h5>الطلبات التي تم العثور عليها</h5>
<?php if (count($records) > 0): ?>
    <table class="delivery-table">
        <tr>
            <th>رقم الطلب</th>
            <th>اسم العميل</th>
            <th>نوع الطلب</th>
            <th>المبلغ المورد</th>
            <th>المساحه المورد عبها</th>
            <th>المساحه الفعليه</th>
            <th>العنوان</th>
            <th>الطول</th>

            <th>الحالة</th>
            <th>تسليم</th>
        </tr>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['id']); ?></td>
                <td><?php echo htmlspecialchars($record['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($record['request_type']); ?></td>
                <td><?php echo htmlspecialchars($record['amount']); ?></td>
                <td><?php echo htmlspecialchars($record['the_space']); ?></td>
                <td><?php echo htmlspecialchars($record['the_actual_area']); ?></td>
                <td><?php echo htmlspecialchars($record['address']); ?></td>
                <td><?php echo htmlspecialchars($record['height']); ?></td>

                <td><?php echo htmlspecialchars($record['status']); ?></td>
                <td>
                    <?php if ($record['status'] == 8): ?>
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                            <button type="submit" name="deliver" class="delivery-button">تسليم</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="delivery-message">لا توجد سجلات مطابقة.</div>
<?php endif; ?>

</body>
</html>
