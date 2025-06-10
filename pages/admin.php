<?php
require "connect.php";
require "home_page.php";

$currentRecord = [];
$recordDisplayed = false; // متغير لتتبع ما إذا تم عرض السجل

// البحث عن السجل باستخدام ID
if (isset($_POST['search'])) {
    $id_to_search = filter_input(INPUT_POST, 'id_to_search', FILTER_VALIDATE_INT);

    if ($id_to_search) {
        // استعلام قاعدة البيانات لاسترجاع السجل بناءً على ID
        $stmt = $conn->prepare("SELECT * FROM information WHERE id = :id");
        $stmt->bindParam(':id', $id_to_search, PDO::PARAM_INT);
        $stmt->execute();
        
        $currentRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentRecord) {
            echo '<a href="measurement.php?' . http_build_query($currentRecord) . '" target="_blank" class="btn btn-success btn-lg">طباعة السجل</a>';
            echo "<br>";
            echo '<a href="print_comparison.php?' . http_build_query($currentRecord) . '" target="_blank" class="btn btn-success btn-lg">طباعة الراي الفني</a>';
        } else {
            echo "<div>لا يوجد سجل بهذا المعرف.</div>";
        }
    } else {
        echo "<div>الرجاء إدخال ID صحيح.</div>";
    }
}

// التعديل على السجل عند الإرسال
if (isset($_POST['send'])) {
    // استرجاع القيم المدخلة
    $id_to_update = $_POST['id']; // الـ id الذي سيتم تعديله
    $customer_name = $_POST['customer_name'];
    $Applicant_name = $_POST['Applicant_name'];
    $his_description = $_POST['his_description'];
    $request_type = $_POST['request_type'];
    $address = $_POST['address'];
    $national_number = $_POST['national_number'];
    $phone_number = $_POST['phone_number'];
    $attachment_name = $_POST['attachment_name'];
    $center = $_POST['center'];
    $payment_method = $_POST['payment_method'];
    $payment_number = $_POST['payment_number'];
    $amount = $_POST['amount'];
    $entry_date = $_POST['entry_date'];
    $customer_rating = $_POST['customer_rating'];
    $date_of_work = $_POST['date_of_work'];
    $status = $_POST['status'];
    $transaction_number = $_POST['transaction_number'];
    $the_space = $_POST['the_space'];
    $height = $_POST['height'];
    $the_actual_area = $_POST['the_actual_area'];
    $money_space = $_POST['money_space'];
    $inspector = $_POST['inspector'];
    $giswork = $_POST['giswork'];
    $dategis = $_POST['dategis'];
    $deliverydate = $_POST['deliverydate'];
    $chekdate = $_POST['chekdate'];
    $customermer = $_POST['customermer'];

    // استعلام لتحديث السجل في قاعدة البيانات
    $updateQuery = "
        UPDATE information 
        SET 
            customer_name = :customer_name,
            Applicant_name = :Applicant_name,
            his_description = :his_description,
            request_type = :request_type,
            address = :address,
            national_number = :national_number,
            phone_number = :phone_number,
            attachment_name = :attachment_name,
            center = :center,
            payment_method = :payment_method,
            payment_number = :payment_number,
            amount = :amount,
            entry_date = :entry_date,
            customer_rating = :customer_rating,
            date_of_work = :date_of_work,
            status = :status,
            transaction_number = :transaction_number,
            the_space = :the_space,
            height = :height,
            the_actual_area = :the_actual_area,
            money_space = :money_space,
            inspector = :inspector,
            giswork = :giswork,
            dategis = :dategis,
            deliverydate = :deliverydate,
            chekdate = :chekdate,
            customermer = :customermer
        WHERE id = :id_to_update
    ";

    $stmt = $conn->prepare($updateQuery);
    // ربط المعاملات
    $stmt->bindParam(':customer_name', $customer_name);
    $stmt->bindParam(':Applicant_name', $Applicant_name);
    $stmt->bindParam(':his_description', $his_description);
    $stmt->bindParam(':request_type', $request_type);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':national_number', $national_number);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':attachment_name', $attachment_name);
    $stmt->bindParam(':center', $center);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':payment_number', $payment_number);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':entry_date', $entry_date);
    $stmt->bindParam(':customer_rating', $customer_rating);
    $stmt->bindParam(':date_of_work', $date_of_work);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':transaction_number', $transaction_number);
    $stmt->bindParam(':the_space', $the_space);
    $stmt->bindParam(':height', $height);
    $stmt->bindParam(':the_actual_area', $the_actual_area);
    $stmt->bindParam(':money_space', $money_space);
    $stmt->bindParam(':inspector', $inspector);
    $stmt->bindParam(':giswork', $giswork);
    $stmt->bindParam(':dategis', $dategis);
    $stmt->bindParam(':deliverydate', $deliverydate);
    $stmt->bindParam(':chekdate', $chekdate);
    $stmt->bindParam(':customermer', $customermer);
    $stmt->bindParam(':id_to_update', $id_to_update);

    // تنفيذ التحديث
    if ($stmt->execute()) {
        echo "<div>تم تعديل السجل بنجاح.</div>";
    } else {
        echo "<div>حدث خطأ أثناء تعديل السجل.</div>";
    }
}

// الحذف بناءً على ID
if (isset($_POST['delete'])) {
    // الحصول على الـ ID الذي سيتم حذفه
    $id_to_delete = filter_input(INPUT_POST, 'delete', FILTER_VALIDATE_INT);

    if ($id_to_delete) {
        // استعلام لحذف السجل من قاعدة البيانات
        $deleteQuery = "DELETE FROM information WHERE id = :id_to_delete";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bindParam(':id_to_delete', $id_to_delete, PDO::PARAM_INT);
        
        // تنفيذ عملية الحذف
        if ($stmt->execute()) {
            echo "<div>تم حذف السجل بنجاح.</div>";
            // يمكنك إعادة توجيه المستخدم إلى صفحة أخرى أو تحديث الصفحة الحالية بعد الحذف
            header("Location: " . $_SERVER['PHP_SELF']); // إعادة تحميل الصفحة لتحديث العرض
            exit();
        } else {
            echo "<div>حدث خطأ أثناء حذف السجل.</div>";
        }
    } else {
        echo "<div>الرجاء إدخال ID صحيح.</div>";
    }
}

// النسخة الاحتياطية
if (isset($_POST['backup'])) {
    $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    $command = "$mysqldumpPath --opt -h $host -u $user -p$pass $db > $backupFile";

    exec($command, $output, $resultCode);

    if ($resultCode !== 0) {
        echo "حدث خطأ أثناء أخذ النسخة الاحتياطية.";
        echo "<pre>" . print_r($output, true) . "</pre>";
    } else {
        echo "تم أخذ النسخة الاحتياطية بنجاح. الملف: $backupFile";
        exit(); // إيقاف التنفيذ بعد النسخ الاحتياطي
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة البيانات</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- ربط ملف CSS الخارجي -->
</head>
<body>
<h3 class="page-title">إضافة بيانات</h3>

<div class="customer-form-container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
        <!-- إضافة ID المخفي لتحديد السجل الذي سيتم تعديله -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($currentRecord['id'] ?? ''); ?>">

        <!-- صف 1: رقم الطلب واسم العميل واسم مقدم الطلب -->
        <div class="form-row">
            <div class="form-group">
                <label>اسم العميل:</label>
                <input class="form-input" type="text" name="customer_name" value="<?php echo htmlspecialchars($currentRecord['customer_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>اسم مقدم الطلب:</label>
                <input class="form-input" type="text" name="Applicant_name" value="<?php echo htmlspecialchars($currentRecord['Applicant_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>صفة مقدم الطلب:</label>
                <input class="form-input" type="text" name="his_description" value="<?php echo htmlspecialchars($currentRecord['his_description'] ?? ''); ?>">
            </div>
        </div>

        <!-- صف 2: نوع الطلب والعنوان والرقم القومي ورقم الهاتف -->
        <div class="form-row">
            <div class="form-group">
                <label>نوع الطلب:</label>
                <input class="form-input" type="text" name="request_type" value="<?php echo htmlspecialchars($currentRecord['request_type'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>عنوان العمل:</label>
                <input class="form-input" type="text" name="address" value="<?php echo htmlspecialchars($currentRecord['address'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>الرقم القومي:</label>
                <input class="form-input" type="text" name="national_number" value="<?php echo htmlspecialchars($currentRecord['national_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>رقم الهاتف:</label>
                <input class="form-input" type="text" name="phone_number" value="<?php echo htmlspecialchars($currentRecord['phone_number'] ?? ''); ?>">
            </div>
        </div>

        <!-- صف 3: باقي الحقول -->
        <div class="form-row">
            <div class="form-group">
                <label>اسم المرفق:</label>
                <input class="form-input" type="text" name="attachment_name" value="<?php echo htmlspecialchars($currentRecord['attachment_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>المركز:</label>
                <input class="form-input" type="text" name="center" value="<?php echo htmlspecialchars($currentRecord['center'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>طريقة الدفع:</label>
                <input class="form-input" type="text" name="payment_method" value="<?php echo htmlspecialchars($currentRecord['payment_method'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>رقم الدفع:</label>
                <input class="form-input" type="text" name="payment_number" value="<?php echo htmlspecialchars($currentRecord['payment_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>المبلغ:</label>
                <input class="form-input" type="text" name="amount" value="<?php echo htmlspecialchars($currentRecord['amount'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تاريخ الدخول:</label>
                <input class="form-input" type="date" name="entry_date" value="<?php echo htmlspecialchars($currentRecord['entry_date'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تصنيف العميل:</label>
                <input class="form-input" type="text" name="customer_rating" value="<?php echo htmlspecialchars($currentRecord['customer_rating'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تاريخ العمل:</label>
                <input class="form-input" type="date" name="date_of_work" value="<?php echo htmlspecialchars($currentRecord['date_of_work'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>الحالة:</label>
                <input class="form-input" type="text" name="status" value="<?php echo htmlspecialchars($currentRecord['status'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>رقم المعاملة:</label>
                <input class="form-input" type="text" name="transaction_number" value="<?php echo htmlspecialchars($currentRecord['transaction_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>المساحة:</label>
                <input class="form-input" type="text" name="the_space" value="<?php echo htmlspecialchars($currentRecord['the_space'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>الطول:</label>
                <input class="form-input" type="text" name="height" value="<?php echo htmlspecialchars($currentRecord['height'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>المساحة الفعلية:</label>
                <input class="form-input" type="text" name="the_actual_area" value="<?php echo htmlspecialchars($currentRecord['the_actual_area'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>مساحة المال:</label>
                <input class="form-input" type="text" name="money_space" value="<?php echo htmlspecialchars($currentRecord['money_space'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>القائم بالمعاينه:</label>
                <input class="form-input" type="text" name="inspector" value="<?php echo htmlspecialchars($currentRecord['inspector'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>GIS القائم بعمل:</label>
                <input class="form-input" type="text" name="giswork" value="<?php echo htmlspecialchars($currentRecord['giswork'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تاريخ GIS:</label>
                <input class="form-input" type="date" name="dategis" value="<?php echo htmlspecialchars($currentRecord['dategis'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تاريخ التسليم:</label>
                <input class="form-input" type="date" name="deliverydate" value="<?php echo htmlspecialchars($currentRecord['deliverydate'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>تاريخ ادخال الشيك:</label>
                <input class="form-input" type="date" name="chekdate" value="<?php echo htmlspecialchars($currentRecord['chekdate'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>اسم المدخل:</label>
                <input class="form-input" type="text" name="customermer" value="<?php echo htmlspecialchars($currentRecord['customermer'] ?? ''); ?>">
            </div>
        </div>

        <!-- زر إرسال لتعديل البيانات -->
    <!-- زر تعديل السجل -->
    <div class="form-row">
        <div class="form-group">
            <button name="send" type="submit" class="form-input">تعديل</button>
        </div>
    </div>

    <!-- زر حذف السجل -->
    <div class="form-row">
        <div class="form-group">
            <!-- إضافة نموذج خاص للحذف باستخدام الـ ID -->
            <button type="submit" name="delete" value="<?php echo htmlspecialchars($currentRecord['id'] ?? ''); ?>" class="btn btn-danger">حذف السجل</button>
        </div>
    </div>
</form>
</div>

<div class="form-row">
    <h5>ابحث برقم الطلب</h5>
    <form action="" method="post">
        <div class="form-group">
            <label>أدخل رقم المعرف:</label>
            <input type="text" name="id_to_search">
        </div>
        <button name="search" type="submit" class="btn-submit">بحث</button>
    </form>
</div>

<!-- زر النسخة الاحتياطية -->
<form method="post" action="">
    <button type="submit" name="backup" class="btn btn-primary">أخذ نسخة احتياطية</button>
</form>

</body>
</html>
