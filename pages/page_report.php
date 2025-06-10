<?php
require "connect.php"; // ملف الاتصال بقاعدة البيانات
require "home_page.php";
// require 'vendor/autoload.php'; // تأكد من أن هذا المسار صحيح بالنسبة لمشروعك

// البحث وعرض البيانات
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $center = isset($_POST['center']) ? trim($_POST['center']) : '';
    $transaction_number = isset($_POST['transaction_number']) ? trim($_POST['transaction_number']) : '';

    try {
        // إعداد الاستعلام الأساسي
        $sql = "SELECT * FROM information WHERE entry_date BETWEEN :start_date AND :end_date";

        // قائمة المتغيرات التي سنقوم بربطها
        $params = [
            ':start_date' => $start_date,
            ':end_date' => $end_date,
        ];

        // إضافة شرط الحالة إذا كانت محددة
        if ($status !== '' && $status !== 'all') {
            $sql .= " AND status = :status";
            $params[':status'] = $status; // إضافة الحالة إلى قائمة المتغيرات
        }

        // إضافة شرط المركز إذا كان محددًا
        if ($center !== '' && $center !== 'all') {
            $sql .= " AND center = :center";
            $params[':center'] = $center; // إضافة المركز إلى قائمة المتغيرات
        }

        // إضافة شرط رقم المعاملة إذا كان محددًا
        if ($transaction_number !== '' && $transaction_number !== 'all') {
            if ($transaction_number === 'existing') {
                $sql .= " AND transaction_number IS NOT NULL"; // شرط للمعاملات المتاحة
            } elseif ($transaction_number === 'null') {
                $sql .= " AND transaction_number IS NULL"; // شرط للمعاملات التي قيمتها NULL
            } else {
                $sql .= " AND transaction_number = :transaction_number";
                $params[':transaction_number'] = $transaction_number; // إضافة رقم المعاملة إلى قائمة المتغيرات
            }
        }

        // تحضير الاستعلام
        $stmt = $conn->prepare($sql);

        // ربط المتغيرات ديناميكياً
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // تنفيذ الاستعلام
        $stmt->execute();
        $data_report = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['data_report'] = $data_report;

        if (!$data_report) {
            $noResultsMessage = "لا توجد نتائج تتطابق مع المدخلات.";
        }
    } catch (PDOException $e) {
        echo "خطأ في الاستعلام: " . $e->getMessage();
    }
}

// تحديث الحالة باستخدام AJAX
if (isset($_POST['update_id']) && isset($_POST['new_status'])) {
    $update_id = $_POST['update_id'];
    $new_status = $_POST['new_status'];

    try {
        $update_sql = "UPDATE information SET status = :new_status WHERE id = :update_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':new_status', $new_status, PDO::PARAM_INT);
        $update_stmt->bindParam(':update_id', $update_id, PDO::PARAM_INT);
        $update_stmt->execute();
        echo "تم تحديث الحالة بنجاح.";
        exit;
    } catch (PDOException $e) {
        echo "خطأ في التحديث: " . $e->getMessage();
        exit;
    }
}

// الكود الخاص بتصدير Excel
if (isset($_POST['export_excel'])) {
    if (!isset($_SESSION['data_report']) || empty($_SESSION['data_report'])) {
        echo "لا توجد بيانات لتصديرها.";
        exit;
    }

    // استرجاع البيانات التي سيتم تصديرها
    $data_report = $_SESSION['data_report'];
    $selected_columns = isset($_POST['columns']) ? $_POST['columns'] : [];

    if (empty($selected_columns)) {
        $selected_columns = ['id', 'customer_name', 'Applicant_name', 'his_description', 'request_type', 'address', 'national_number', 'phone_number', 'attachment_name', 'center', 'payment_method', 'payment_number', 'amount', 'entry_date', 'transaction_number', 'status'];
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // إعداد رؤوس الأعمدة
    $columnHeaders = [
        'id' => 'ID',
        'customer_name' => 'اسم العميل',
        'Applicant_name' => 'اسم مقدم الطلب',
        'his_description' => 'صفة مقدم الطلب',
        'request_type' => 'نوع الطلب',
        'address' => 'العنوان',
        'national_number' => 'الرقم القومي',
        'phone_number' => 'رقم الهاتف',
        'attachment_name' => 'اسم المرفق',
        'center' => 'المركز',
        'payment_method' => 'طريقة الدفع',
        'payment_number' => 'رقم الدفع',
        'amount' => 'المبلغ',
        'entry_date' => 'تاريخ الدفع',
        'transaction_number' => 'رقم المعاملة',
        'status' => 'الحالة',
    ];

    // إضافة رؤوس الأعمدة المحددة
    $columnIndex = 1;
    foreach ($selected_columns as $column) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex); // تحويل العمود إلى الحرف المناسب
        $sheet->setCellValue($columnLetter . '1', $columnHeaders[$column]); // تحديد الخلية باستخدام الحرف والصف
        $columnIndex++;
    }

    // إضافة البيانات بناءً على الأعمدة المختارة
    $row = 2;
    foreach ($data_report as $record) {
        $columnIndex = 1;
        foreach ($selected_columns as $column) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->setCellValue($columnLetter . $row, $record[$column]); // إضافة القيمة إلى الخلية المحددة
            $columnIndex++;
        }
        $row++;
    }

    // إعداد رؤوس الاستجابة لملف Excel
    ob_end_clean(); // تنظيف أي مخرجات سابقة
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="تقرير_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Expires: 0');
    header('Pragma: public');

    // كتابة الملف
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البحث عن معلومات</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h3>اختر تاريخ البدايه والنهايه</h3>
<form action="" method="POST">
    <label for="start_date">تاريخ البداية:</label>
    <input type="date" id="start_date" name="start_date" required>

    <br><br>

    <label for="end_date">تاريخ النهاية:</label>
    <input type="date" id="end_date" name="end_date" required>

    <br><br>

    <label for="status">اختر الاجراء:</label>
    <select id="status" name="status" required>
        <option value="all">عرض جميع الحالات</option>
        <option value="1">تم الادخال</option>
        <option value="2">تم التمرير الي العمل الميداني</option>
        <option value="3">تم الاستلام من خدمه العملاء</option>
        <option value="4">تم التمرير الي النظم</option>
        <option value="5">تم الاستلام بواسطه النظم</option>
        <option value="6">تم الانتهاء من النظم</option>
        <option value="7">تم التسليم</option>
    </select>

    <br><br>

    <label for="center">اختر المركز:</label>
    <select id="center" name="center" required>
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

    <br><br>

    <label for="transaction_number"> المعاملة:</label>
    <select id="transaction_number" name="transaction_number">
        <option value="all">عرض الكل</option>
        <option value="existing">عرض الخاص بالمركز التكنولوجي</option>
        <option value="null">عرض حالات مركز شبكات المرافق</option>
    </select>
    <br><br>

    <input type="submit" value="بحث">
</form>

<?php if (isset($data_report) && $data_report): ?>
  
  <h2>نتائج البحث</h2>
<form method="POST" id="columnsForm">
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" name="columns[]" value="id" checked> ID</th>
                <th><input type="checkbox" name="columns[]" value="customer_name" checked> اسم العميل</th>
                <th><input type="checkbox" name="columns[]" value="Applicant_name" checked> اسم مقدم الطلب</th>
                <th><input type="checkbox" name="columns[]" value="his_description" checked> صفة مقدم الطلب</th>
                <th><input type="checkbox" name="columns[]" value="request_type" checked> نوع الطلب</th>
                <th><input type="checkbox" name="columns[]" value="address" checked> العنوان</th>
                <th><input type="checkbox" name="columns[]" value="national_number" checked> الرقم القومي</th>
                <th><input type="checkbox" name="columns[]" value="phone_number" checked> رقم الهاتف</th>
                <th><input type="checkbox" name="columns[]" value="attachment_name" checked> اسم المرفق</th>
                <th><input type="checkbox" name="columns[]" value="center" checked> المركز</th>
                <th><input type="checkbox" name="columns[]" value="payment_method" checked> طريقة الدفع</th>
                <th><input type="checkbox" name="columns[]" value="payment_number" checked> رقم الدفع</th>
                <th><input type="checkbox" name="columns[]" value="amount" checked> المبلغ</th>
                <th><input type="checkbox" name="columns[]" value="entry_date" checked> تاريخ الدفع</th>
                <th><input type="checkbox" name="columns[]" value="transaction_number" checked> رقم المعاملة</th>
                <th><input type="checkbox" name="columns[]" value="status" checked> الحالة</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_report as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['id']); ?></td>
                    <td><?php echo htmlspecialchars($record['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['Applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['his_description']); ?></td>
                    <td><?php echo htmlspecialchars($record['request_type']); ?></td>
                    <td><?php echo htmlspecialchars($record['address']); ?></td>
                    <td><?php echo htmlspecialchars($record['national_number']); ?></td>
                    <td><?php echo htmlspecialchars($record['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($record['attachment_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['center']); ?></td>
                    <td><?php echo htmlspecialchars($record['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($record['payment_number']); ?></td>
                    <td><?php echo htmlspecialchars($record['amount']); ?></td>
                    <td><?php echo htmlspecialchars($record['entry_date']); ?></td>
                    <td><?php echo htmlspecialchars($record['transaction_number']); ?></td>
                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
<button type="button" id="exportButton">تصدير إلى Excel</button>
</form>

<?php endif; ?>

<script>
    document.getElementById("exportButton").addEventListener("click", function() {
        // إضافة "export_excel" كجزء من POST عند الضغط على زر التصدير
        const form = document.getElementById("columnsForm");
        const exportInput = document.createElement("input");
        exportInput.type = "hidden";
        exportInput.name = "export_excel";
        exportInput.value = "true";
        form.appendChild(exportInput);
        form.submit(); // إرسال النموذج
    });
</script>

</body>
</html>
