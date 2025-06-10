<?php
// require 'vendor/autoload.php';
require "connect.php";
require "home_page.php";

// بداية الكود المبدئي للبحث
$search_value = '';
$data = null;

if (isset($_POST['search'])) {
    $search_value = $_POST['search_value'];
    $search_value_with_wildcards = "%" . $search_value . "%";  // إضافة البدل للبحث الجزئي

    // استعلام لجلب البيانات بناءً على id أو payment_number أو customer_name
    try {
        $sql = "SELECT * FROM information WHERE id = :id_value OR payment_number = :payment_value OR customer_name LIKE :customer_value";
        $stmt = $conn->prepare($sql);

        // ربط القيم بالـ PDO
        // ربط id بالقيمة
        $stmt->bindParam(':id_value', $search_value, PDO::PARAM_STR);
        // ربط payment_number بالقيمة
        $stmt->bindParam(':payment_value', $search_value, PDO::PARAM_STR);
        // ربط customer_name بالقيمة مع البدل
        $stmt->bindParam(':customer_value', $search_value_with_wildcards, PDO::PARAM_STR);

        $stmt->execute();

        // جلب جميع النتائج
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // التحقق من وجود البيانات
        if (empty($data)) {
            echo "لا توجد نتائج تتطابق مع المدخلات.";
        }
    } catch (PDOException $e) {
        echo "خطأ في الاستعلام: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بحث الطلب</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <!-- نموذج البحث الذي يظهر دائما -->
    <div class="inquiry col-12">
        <form method="POST" action="">
            <label for="search_value">أدخل رقم الطلب :</label>
            <input type="text" id="search_value" name="search_value" value="<?php echo htmlspecialchars($search_value); ?>" required>
            <input type="submit" name="search" value="بحث" class="btn btn-success btn-lg">
        </form>
    </div>

    <div class="work table-container">
        <?php if ($data): ?>
            <h1>نتائج البحث</h1>
            <table class="display-status">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>نوع الطلب</th>
                        <th>اسم العميل</th>
                        <th>ع-gis</th>
                        <th>الرقم القومي</th>
                        <th>ت-gis</th>
                        <th>العنوان</th>
                        <th>ت-المعاينة</th>
                        <th>قائم بالمعاينة</th>
                        <th>وصف الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['giswork']); ?></td>
                            <td><?php echo htmlspecialchars($row['national_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['dategis']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_work']); ?></td>
                            <td><?php echo htmlspecialchars($row['inspector']); ?></td>
                            <td>
                                <?php
                                switch ($row['status']) {
                                    case 1:
                                        echo "تم الإدخال من خدمة العملاء";
                                        break;
                                    case 2:
                                        echo "تم التمرير إلى  الحسابات";
                                        break;
                                    case 3:
                                        echo "تم الاستلام بواسطة  الحسابات";
                                        break;
                                    case 4:
                                        echo "تم التمرير إلى العمل الميداني";
                                        break;
                                    case 5:
                                        echo "تم استلام العمل الميداني من  الحسابات";
                                        break;
                                    case 6:
                                        echo "تم التسليم الي النظم";
                                        break;
                                    case 7:
                                        echo "تم الاستلام من النظم";
                                        break;
                                    case 8:
                                        echo "تم التسليم الي خدمه العملاء";
                                        break;
                                    case 9:
                                        echo "تم التسليم الي  العميل";
                                        break;
                                    default:
                                        echo "تم التسليم للعميل";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا توجد نتائج تتطابق مع المدخلات.</p>
        <?php endif; ?>
    </div>


</body>

</html>