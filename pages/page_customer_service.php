<?php

require "connect.php";
require "home_page.php";

$uploadsDir = "uploads/";
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

$username = $_SESSION['username'];

// إدخال البيانات
if (isset($_POST['send'])) {
    // استلام البيانات من النموذج
    $customer_name = htmlspecialchars(trim($_POST['customer_name'] ?? ''));
    $Applicant_name = htmlspecialchars(trim($_POST['Applicant_name'] ?? ''));
    $his_description = htmlspecialchars(trim($_POST['his_description'] ?? ''));
    $request_type = htmlspecialchars(trim($_POST['request_type'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $national_number = htmlspecialchars(trim($_POST['national_number'] ?? ''));
    $phone_number = htmlspecialchars(trim($_POST['phone_number'] ?? ''));
    $attachment_name = htmlspecialchars(trim($_POST['attachment_name'] ?? ''));
    $center = htmlspecialchars(trim($_POST['center'] ?? ''));
    $payment_method = htmlspecialchars(trim($_POST['payment_method'] ?? ''));
    $entry_date = htmlspecialchars(trim($_POST['entry_date'] ?? ''));
    $customer_rating = htmlspecialchars(trim($_POST['customer_rating'] ?? ''));
    $status = htmlspecialchars(trim($_POST['status'] ?? ''));
    $transaction_number = htmlspecialchars(trim($_POST['transaction_number'] ?? ''));
    $the_space = htmlspecialchars(trim($_POST['the_space'] ?? ''));
    $height = htmlspecialchars(trim($_POST['height'] ?? ''));


      if (empty($customer_name)) {
        echo "<script>alert('يرجى إدخال اسم العميل.');</script>";
        exit; 
    }

    if (!preg_match('/^\d{14}$/', $national_number)) {
        echo "<script>alert('الرقم القومي غير صحيح.');</script>";
        exit; 
    }

    if (!preg_match('/^\d{11}$/', $phone_number)) {
        echo "<script>alert('رقم الهاتف غير صحيح.');</script>";
        exit; // إيقاف التنفيذ إذا كان رقم الهاتف غير صحيح
    }

    // إضافة التحقق لبقية الحقول
    if (empty($center)) {
        echo "<script>alert('يرجى إدخال المركز.');</script>";
        exit; // إيقاف التنفيذ إذا كان المركز فارغ
    }

    if (empty($payment_method)) {
        echo "<script>alert('يرجى إدخال طريقة الدفع.');</script>";
        exit; // إيقاف التنفيذ إذا كانت طريقة الدفع فارغة
    }

    if (empty($request_type)) {
        echo "<script>alert('يرجى إدخال نوع الطلب.');</script>";
        exit; // إيقاف التنفيذ إذا كان نوع الطلب فارغ
    }

    if (empty($his_description)) {
        echo "<script>alert('يرجى إدخال الوصف.');</script>";
        exit; // إيقاف التنفيذ إذا كان الوصف فارغ
    }
    // إدخال البيانات في قاعدة البيانات
    $add = $conn->prepare("INSERT INTO information (customermer, customer_name, Applicant_name, his_description, request_type, address, national_number, phone_number, attachment_name, center, payment_method, entry_date, customer_rating, transaction_number, the_space, height) VALUES (:customermer, :customer_name, :Applicant_name, :his_description, :request_type, :address, :national_number, :phone_number, :attachment_name, :center, :payment_method, :entry_date, :customer_rating, :transaction_number, :the_space, :height)");

    // ربط القيم
    $add->bindValue(':customermer', $username);
    $add->bindValue(':customer_name', $customer_name);
    $add->bindValue(':Applicant_name', $Applicant_name);
    $add->bindValue(':his_description', $his_description);
    $add->bindValue(':request_type', $request_type);
    $add->bindValue(':address', $address);
    $add->bindValue(':national_number', $national_number);
    $add->bindValue(':phone_number', $phone_number);
    $add->bindValue(':attachment_name', $attachment_name);
    $add->bindValue(':center', $center);
    $add->bindValue(':payment_method', $payment_method);
    $add->bindValue(':entry_date', $entry_date);
    $add->bindValue(':customer_rating', $customer_rating);
    $add->bindValue(':transaction_number', $transaction_number);
    $add->bindValue(':the_space', $the_space);
    $add->bindValue(':height', $height);

    // تنفيذ الاستعلام
    if ($add->execute()) {
        $userId = $conn->lastInsertId();  // الحصول على الـ ID الذي تم إدخاله في قاعدة البيانات
        // إنشاء المجلد الخاص بالـ ID
        $userDir = $uploadsDir . $userId . "_" . $customer_name . "/";
        if (!is_dir($userDir)) {
            if (!mkdir($userDir, 0777, true)) {
                echo "<div>فشل إنشاء المجلد.</div>";
                exit;
            }
        }

        // دالة إنشاء ملف Excel
        function createExcel($conn, $userId, $imagePath = '', $userDir)
        {
            require_once 'vendor/autoload.php';  // تأكد من تضمين مكتبة PhpSpreadsheet

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // إعداد عناوين الأعمدة
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'اسم العميل');
            $sheet->setCellValue('C1', 'اسم المتقدم');
            $sheet->setCellValue('D1', 'الوصف');
            $sheet->setCellValue('E1', 'نوع الطلب');
            $sheet->setCellValue('F1', 'العنوان');
            $sheet->setCellValue('G1', 'الرقم الوطني');
            $sheet->setCellValue('H1', 'رقم الهاتف');
            $sheet->setCellValue('I1', 'اسم المرفق');
            $sheet->setCellValue('J1', 'المركز');
            $sheet->setCellValue('K1', 'طريقة الدفع');
            $sheet->setCellValue('N1', 'تاريخ الشيك');
            $sheet->setCellValue('O1', 'تقييم العميل');
            $sheet->setCellValue('P1', 'تاريخ العمل');
            $sheet->setCellValue('Q1', 'حالة');
            $sheet->setCellValue('R1', 'صورة');
            $sheet->setCellValue('S1', 'رقم المعامله');
            $sheet->setCellValue('T1', 'المساحه');
            $sheet->setCellValue('V1', 'الطول');

            // الحصول على بيانات المستخدم
            $stmt = $conn->prepare("SELECT * FROM information WHERE id = ?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // إضافة بيانات المستخدم إلى ملف Excel
            if ($data) {
                $sheet->setCellValue('A2', $data['id']);
                $sheet->setCellValue('B2', $data['customer_name']);
                $sheet->setCellValue('C2', $data['Applicant_name']);
                $sheet->setCellValue('D2', $data['his_description']);
                $sheet->setCellValue('E2', $data['request_type']);
                $sheet->setCellValue('F2', $data['address']);
                $sheet->setCellValue('G2', $data['national_number']);
                $sheet->setCellValue('H2', $data['phone_number']);
                $sheet->setCellValue('I2', $data['attachment_name']);
                $sheet->setCellValue('J2', $data['center']);
                $sheet->setCellValue('K2', $data['payment_method']);
                $sheet->setCellValue('N2', $data['entry_date']);
                $sheet->setCellValue('O2', $data['customer_rating']);
                $sheet->setCellValue('Q2', $data['status']);
                $sheet->setCellValue('S2', $data['transaction_number']);
                $sheet->setCellValue('T2', $data['the_space']);
                $sheet->setCellValue('V2', $data['height']);

                // إضافة الصورة إذا كانت موجودة

            }

            // حفظ ملف Excel في المجلد الخاص بالـ ID
            $excel_file = $userDir . "user_data_$userId.xlsx";
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($excel_file);
        }

        // التحقق من رفع الصور
        if (isset($_FILES['images'])) {
            $images = $_FILES['images'];
            if (is_array($images['tmp_name'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $imageUploaded = false;
                foreach ($images['tmp_name'] as $key => $tmpName) {
                    if ($images['error'][$key] == UPLOAD_ERR_OK) {
                        if (in_array($images['type'][$key], $allowedTypes)) {
                            $file_name = basename($images['name'][$key]);
                            $target_file = $userDir . $file_name;
                            if (move_uploaded_file($tmpName, $target_file)) {
                                $imageUploaded = true;
                                createExcel($conn, $userId, $target_file, $userDir);
                            } else {
                                echo "<div>فشل رفع الصورة.</div>";
                            }
                        } else {
                            echo "<div>يرجى تحميل صورة بتنسيق JPEG أو PNG أو GIF.</div>";
                        }
                    } else {
                        echo "<div>حدث خطأ أثناء رفع الصورة: " . $images['error'][$key] . "</div>";
                    }
                }
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<script>alert('فشل إدخال البيانات');</script>";
        exit;
    }

 
}







// جلب البيانات من جدول information التي لم يتم تمريرها
$stmt =$conn->prepare("SELECT * FROM information WHERE status = 1 ORDER BY id");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إذا تم الضغط على زر تغيير الحالة
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_to_update = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id_to_update) {
        // جلب القيمة الحالية
        $stmt = $conn->prepare("SELECT status FROM information WHERE id = :id");
        $stmt->bindParam(':id', $id_to_update);
        $stmt->execute();
        $current_status = $stmt->fetchColumn();

        // تحديث القيمة (0-5)
        $new_status = ($current_status + 1);

        // تحديث العمود status في جدول information
        $update = $conn->prepare("UPDATE information SET status = :new_status WHERE id = :id");
        $update->bindValue(':new_status', $new_status);
        $update->bindValue(':id', $id_to_update);
        $update->execute();

        // إعادة توجيه بعد التحديث
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}






$currentRecord = [];
$recordDisplayed = false; // متغير لتتبع ما إذا تم عرض السجل

if (isset($_POST['search'])) {
    $id_to_search = filter_input(INPUT_POST, 'id_to_search', FILTER_VALIDATE_INT);

    if ($id_to_search) {
        // استعلام قاعدة البيانات لاسترجاع السجل بناءً على ID
        $stmt = $conn->prepare("SELECT * FROM information WHERE id = :id");
        $stmt->bindParam(':id', $id_to_search);
        $stmt->execute();
        
        $currentRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentRecord) {
            // إنشاء رابط لتمرير البيانات إلى صفحة جديدة
            $query = http_build_query($currentRecord); // تحويل المصفوفة إلى سلسلة استعلام
            echo '<a href="measurement.php?' . $query . '" target="_blank" class="btn btn-success btn-lg" >طباعة السجل</a>';
    echo "<br>";
            echo '<a href="print_comparison.php?' . $query . '" target="_blank" class="btn btn-success btn-lg" >طباعة الراي الفني</a>';

        } else {
            echo "<div>لا يوجد سجل بهذا المعرف.</div>";
        }
    } else {
        echo "<div>الرجاء إدخال ID صحيح.</div>";
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
                <select class="form-select" name="request_type">
                    <option value="">....اختر نوع الطلب</option>
                    <option value="رفع مساحي" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "رفع مساحي") echo 'selected'; ?>>رفع مساحي</option>
                    <option value="كشف مرافق" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "كشف مرافق") echo 'selected'; ?>>كشف مرافق</option>
                    <option value="تحديد حيز عمراني" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "تحديد حيز عمراني") echo 'selected'; ?>>تحديد حيز عمراني</option>
                    <option value="تحديد منسوب" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "تحديد منسوب") echo 'selected'; ?>>تحديد منسوب</option>
                    <option value="اعاده طباعه" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "اعاده طباعه") echo 'selected'; ?>>اعاده طباعه</option>
                    <option value="فرق توريد" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "فرق توريد") echo 'selected'; ?>>فرق توريد</option>
                    <option value="تتبع زمني" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "تتبع زمني") echo 'selected'; ?>>تتبع زمني</option>
                    <option value="تتبع زمني وتحديد منسوب" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "تتبع زمني وتحديدمنسوب") echo 'selected'; ?>>تتبع زمني وتحديد منسوب</option>

                    <option value="اخري" <?php if (isset($currentRecord['request_type']) && $currentRecord['request_type'] == "اخري") echo 'selected'; ?>>اخري</option>


                </select>
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

        <!-- صف 3: اسم المرفق والمركز وطريقة الدفع ورقم الدفع -->
        <div class="form-row">
            <div class="form-group">
                <label>اسم المرفق:</label>
                <input class="form-input" type="text" name="attachment_name" value="<?php echo htmlspecialchars($currentRecord['attachment_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>المركز:</label>
                <select class="form-select" name="center">
                    <option value="">اختر المركز</option>
                    <option value="قنا" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "قنا") echo 'selected'; ?>>قنا</option>
                    <option value="دشنا" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "دشنا") echo 'selected'; ?>>دشنا</option>
                    <option value="نجع حمادي" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "نجع حمادي") echo 'selected'; ?>>نجع حمادي</option>
                    <option value="قوص" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "قوص") echo 'selected'; ?>>قوص</option>
                    <option value="قفط" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "قفط") echo 'selected'; ?>>قفط</option>
                    <option value="ابوتشت" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "ابوتشت") echo 'selected'; ?>>ابوتشت</option>
                    <option value="فرشوط" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "فرشوط") echo 'selected'; ?>>فرشوط</option>
                    <option value="نقاده" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "نقاده") echo 'selected'; ?>>نقاده</option>
                    <option value="الوقف" <?php if (isset($currentRecord['center']) && $currentRecord['center'] == "الوقف") echo 'selected'; ?>>الوقف</option>
                </select>
            </div>

            <div class="form-group">
                <label>طريقة الدفع:</label>
                <select class="form-select" name="payment_method">
                    <option value="">اختر نوع الدفع</option>
                    <option value="pos" <?php if (isset($currentRecord['payment_method']) && $currentRecord['payment_method'] == "pos") echo 'selected'; ?>>pos</option>
                    <option value="شيك" <?php if (isset($currentRecord['payment_method']) && $currentRecord['payment_method'] == "شيك") echo 'selected'; ?>>شيك</option>
                    <option value="امردفع" <?php if (isset($currentRecord['payment_method']) && $currentRecord['payment_method'] == "امردفع") echo 'selected'; ?>>امردفع</option>
                </select>
            </div>

            <div class="form-group">
                <label>تصنيف العميل:</label>
                <select class="form-select" name="customer_rating">
                    <option value="">تصنيف العميل</option>
                    <option value="جهه حكوميه" <?php if (isset($currentRecord['customer_rating']) && $currentRecord['customer_rating'] == "جهه حكوميه") echo 'selected'; ?>>جهه حكوميه</option>
                    <option value="اهالي" <?php if (isset($currentRecord['customer_rating']) && $currentRecord['customer_rating'] == "اهالي") echo 'selected'; ?>>اهالي</option>
                    <option value="شركات" <?php if (isset($currentRecord['customer_rating']) && $currentRecord['customer_rating'] == "شركات") echo 'selected'; ?>>شركات</option>
                </select>
            </div>
            
            <div class="form-group">
              
                <input class="form-input" type="hidden" name="entry_date" value="<?php echo htmlspecialchars(date('Y-m-d')); ?>">
                </div>

        </div>

        <!-- صف 5: رقم المعاملة والمساحة والطول -->
        <div class="form-row">
            <div class="form-group">
                <label>رقم معامله المركز التكنولجي:</label>
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
        </div>

     <div class="form-row">
    <div class="form-group">
        <label for="image">اختر صورة:</label>
        <input class="form-input" type="file" name="images[]" id="image" multiple>
    </div>
</div>

<!-- زر إرسال -->
<div class="form-row">
    <div class="form-group">
        <button name="send" type="submit" class="btn-submit">إضافة البيانات</button>
    </div>
</div>
</form>
</div>

<h5>ابحث برقم الطلب</h5>
<form action="" method="post">
    <input type="number" name="id_to_search" placeholder="ابحث باستخدام ID">
    <input class="btn-search" type="submit" name="search" value="بحث">
</form>

<h5>الطلبات التي لم تمرر</h5>
<?php if (count($records) > 0): ?>
    <table class="table-responsive">
        <tr>
            <th>رقم الطلب</th>
            <th>اسم العميل</th>
            <th>رقم التليفون</th>
            <th>المركز</th>
            <th>العنوان</th>
            <th>الحالة</th>
            <th>المساحه</th>
            <th>تغيير الحالة</th>
        </tr>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['id']); ?></td>
                <td><?php echo htmlspecialchars($record['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($record['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($record['center']); ?></td>
                <td><?php echo htmlspecialchars($record['address']); ?></td>
                <td><?php echo htmlspecialchars($record['status']); ?></td>
                <td><?php echo htmlspecialchars($record['the_space']); ?></td>
                <td>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                        <input class="btn-action" type="submit" value="تمرير الي الحسابات">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div>لا توجد سجلات غير ممررة.</div>
<?php endif; ?>
</body>




    <!-- <script>
        function printSection() {
            var printContents = document.body.innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script> -->



</body>

</html>