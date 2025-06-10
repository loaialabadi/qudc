<?php

require "connect.php";
require "home_page.php";
// require 'vendor/autoload.php'; // تأكد من أن هذا المسار صحيح بالنسبة لمشروعك








// / البحث وعرض البيانات
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $center = isset($_POST['center']) ? trim($_POST['center']) : '';

    try {
        // إعداد الاستعلام الأساسي
        $sql = "SELECT * FROM information WHERE entry_date BETWEEN :start_date AND :end_date";

        // إضافة شرط الحالة إذا كانت محددة
        if ($status !== '' && $status !== 'all') {
            $sql .= " AND status = :status";
        }

        // إضافة شرط المركز إذا كان محددًا
        if ($center !== '' && $center !== 'all') {
            $sql .= " AND center = :center";
        }

        // تحضير الاستعلام
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);

        // ربط قيمة `status` إذا كانت محددة
        if ($status !== '' && $status !== 'all') {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }

        // ربط قيمة `center` إذا كانت محددة
        if ($center !== '' && $center !== 'all') {
            $stmt->bindParam(':center', $center, PDO::PARAM_STR);
        }

        // تنفيذ الاستعلام
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['data'] = $data;

        // التحقق إذا لم يتم العثور على نتائج
        if (!$data) {
            $noResultsMessage = "لا توجد نتائج تتطابق مع المدخلات.";
        }
    } catch (PDOException $e) {
        echo "خطأ في الاستعلام: " . $e->getMessage();
    }
}








// دالة لتصدير البيانات إلى Excel
if (isset($_POST['export_excel'])) {
    // var_dump("asasas");
    $data = $_SESSION['data'];

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // إضافة عنوان الأعمدة
    $sheet->setCellValue('A1', 'ID')
        ->setCellValue('B1', 'اسم العميل')
        ->setCellValue('C1', 'اسم مقدم الطلب')
        ->setCellValue('D1', 'صفة مقدم الطلب')
        ->setCellValue('E1', 'نوع الطلب')
        ->setCellValue('F1', 'العنوان')
        ->setCellValue('G1', 'رقم الهاتف')
        ->setCellValue('H1', 'اسم المرفق')
        ->setCellValue('I1', 'المركز');


    // إضافة البيانات
    $row = 2; // بدء الصف من 2 بعد العناوين
    foreach ($data as $record) {
        $sheet->setCellValue('A' . $row, $record['id'])
            ->setCellValue('B' . $row, $record['customer_name'])
            ->setCellValue('C' . $row, $record['Applicant_name'])
            ->setCellValue('D' . $row, $record['his_description'])
            ->setCellValue('E' . $row, $record['request_type'])
            ->setCellValue('F' . $row, $record['address'])
            ->setCellValue('G' . $row, $record['phone_number'])
            ->setCellValue('H' . $row, $record['attachment_name'])
            ->setCellValue('i' . $row, $record['center']);


        $row++;
    }

    // إعداد ملف Excel للتنزيل
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="تقرير_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Expires: 0');
    header('Pragma: public');

    // إنشاء الملف وحفظه
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    ob_end_clean(); // تنظيف أي مخرجات سابقة
    $writer->save('php://output');
    exit; // تأكد من الخروج بعد التحميل لتجنب ظهور أي بيانات إضافية
}





// التحقق إذا تم إرسال تحديث للحالة
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $new_status = $_POST['new_status'];

    // تحديث حالة السجل في قاعدة البيانات
    $sql = "UPDATE information SET status = :new_status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':new_status', $new_status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "تم تحديث الحالة بنجاح";
    } else {
        echo "خطأ في تحديث الحالة";
    }
}



// تنفيذ استعلام التحديث
if (isset($_POST['ubdate_date_of_work'])) {
    $id = $_POST['id'];
    $date_of_work = $_POST['date_of_work']; // الحصول على تاريخ العمل من النموذج

    // التحقق من وجود اسم المستخدم في الجلسة
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'مجهول'; // افتراض اسم المستخدم إن لم يكن موجودًا

    // استعلام تحديث التاريخ مع حفظ اسم المستخدم في العمود "inspector"
    $sql = "UPDATE information SET date_of_work = :date_of_work, inspector = :inspector WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':date_of_work', $date_of_work);
    $stmt->bindParam(':inspector', $username); // حفظ اسم المستخدم في العمود "inspector"
    $stmt->bindParam(':id', $id);

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        echo "تم تحديث التاريخ الفعلي بنجاح!";
    } else {
        echo "خطأ في تحديث التاريخ الفعلي.";
    }
}



// استعلام لاسترجاع البيانات
$sql = "SELECT id, customer_name, status, the_space, address, phone_number, center, date_of_work, inspector, request_type  FROM information";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>










<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>إدارة البيانات</title>
        <link rel="stylesheet" href="css/styles.css"> <!-- ربط ملف CSS الخارجي -->

</head>

<body>
<div class="work col-12"></div>

    <div class="work table-container">
        <table border="1">
            <tr>
                <th>رقم الطلب</th>
                <th>اسم العميل</th>
                <th>مركز مدينه</th>
                <th>العنوان</th>
                <th>رقم التليفون</th>
                <th>الحالة</th>
                <th>نوع الطلب</th>
                <th>اضافه تاريخ رفع العمل الميداني</th>
                <th>تغيير الحالة</th>
            </tr>
            <?php
            // عرض السجلات
            if (count($results) > 1) {
                foreach ($results as $row) {
                    // عرض الصفوف فقط عندما تكون الحالة بين 2 و 4، واستثناء 3
                    if ($row["status"] == 4 || $row["status"] == 5) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["customer_name"] . "</td>";
                        echo "<td>" . $row["center"] . "</td>";
                        echo "<td>" . $row["address"] . "</td>";
                        echo "<td>" . $row["phone_number"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["request_type"] . "</td>";

                   
                        echo "<td>";
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">';
                        echo '<input type="date" name="date_of_work" value="' . htmlspecialchars($row["date_of_work"]) . '" required>';
                        echo '<input type="submit" name="ubdate_date_of_work" value="اضافه تاريخ الرفع">';
                        echo '</form>';
                        echo "</td>";

                        echo "<td>";    // زر تغيير الحالة بناءً على الحالة الحالية
                        if ($row["status"] == 4) {
                            echo '<form method="POST">
                                <input type="hidden" name="id" value="' . $row["id"] . '">
                                <input type="hidden" name="new_status" value="5">
                                <input type="submit" name="update_status" value="استلام من الحسابات">
                              </form>';
                        } elseif ($row["status"] == 5) {
                            echo '<form method="POST">
                                <input type="hidden" name="id" value="' . $row["id"] . '">
                                <input type="hidden" name="new_status" value="6">
                                <input type="submit" name="update_status" value="تسليم للنظم">
                              </form>';
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='4'>لا توجد بيانات.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="work col-12"></div>
    <h3 class="work text-primary">اختر تواريخ البداية والنهاية</h3>

    <div class="work container">
    <form action="" method="POST">

        <div class="work row">
            <div class="work col-12 col-md-4 mb-3">
                <label for="start_date">تاريخ البداية:</label>
                <input type="date" id="start_date" name="start_date" required class="form-control">
            </div>

            <div class="work col-12 col-md-4 mb-3">
                <label for="status">اختر الاجراء:</label>
                <select id="status" name="status" required class="form-control">
                    <option value="5">تم الاستلام من الحسابات </option>
                </select>
            </div>
        </div>

        <div class="work row">
            <div class="work col-12 col-md-4 mb-3">
                <label for="end_date">تاريخ النهاية:</label>
                <input type="date" id="end_date" name="end_date" required class="form-control">
            </div>

            <div class="work col-12 col-md-4 mb-3">
                <label for="center">اختر المركز:</label>
                <select id="center" name="center" required class="form-control">
                    <option value="all">عرض جميع المراكز</option>
                    <option value="قنا">قنا</option>
                    <option value="دشنا">دشنا</option>
                    <option value="نجع حمادي">نجع حمادي</option>
                    <option value="قوص">قوص</option>
                    <option value="قفط">قفط</option>
                    <option value="ابوتشت">ابوتشت</option>
                    <option value="فرشوط">فرشوط</option>
                    <option value="نقاده">نقاده</option>
                    <option value="الوقف">الوقف</option>
                </select>
            </div>
        </div>

        <div class="work row">
            <div class="work col-12 col-md-2 mb-3">
                <input class="btn btn-success btn-lg btn-block" type="submit" value="بحث">
            </div>
        </div>

    </form>
</div>

    <?php if (isset($data) && $data): ?>
        <h2 class="work">نتائج البحث</h2>
        <table class="work">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>اسم العميل</th>
                    <th>اسم مقدم الطلب</th>
                    <th>صفة مقدم الطلب</th>
                    <th>نوع الطلب</th>
                    <th>العنوان</th>
                    <th>رقم الهاتف</th>
                    <th>اسم المرفق</th>
                    <th>المركز</th>
                    <th>تاريخ الدفع</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $record): ?>
                    <tr id="row-<?php echo $record['id']; ?>">
                        <td><?php echo htmlspecialchars($record['id']); ?></td>
                        <td><?php echo htmlspecialchars($record['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['Applicant_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['his_description']); ?></td>
                        <td><?php echo htmlspecialchars($record['request_type']); ?></td>
                        <td><?php echo htmlspecialchars($record['address']); ?></td>
                        <td><?php echo htmlspecialchars($record['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($record['attachment_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['center']); ?></td>
                        <td><?php echo htmlspecialchars($record['entry_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- نموذج تصدير منفصل -->
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="exportForm">
            <?php
            // تخزين البيانات في حقل مخفي
            $exportData = base64_encode(serialize($data));
            ?>
            <input type="hidden" name="export_excel" value="<?php echo htmlspecialchars($exportData); ?>">
            <button type="submit" class="work export-btn">تحميل كملف Excel</button>
        </form>

    <?php elseif (isset($noResultsMessage)): ?>
        <p class="work"><?php echo $noResultsMessage; ?></p>
    <?php endif; ?>

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