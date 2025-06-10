<?php
require "connect.php";
require "home_page.php";

// استعلام لاسترجاع البيانات
$sql = "SELECT id, customer_name, request_type, status, the_space, address, phone_number, center, payment_number, amount, date_of_work FROM information";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['update_data'])) {
    $id = $_POST['id'];
    $amount = $_POST['amount']; // الحصول على المبلغ من النموذج
    $payment_number = $_POST['payment_number']; // الحصول على رقم الدفع كـ نص
    $checkdate = $_POST['checkdate']; // الحصول على تاريخ اليوم من الحقل المخفي

    // استعلام للتحقق من القيم في قاعدة البيانات
    $sql_check = "SELECT amount, payment_number FROM information WHERE id = :id";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // إذا كانت القيم في قاعدة البيانات تساوي 0، يمكن التحديث أو الإدخال
    if ($result['amount'] == "0" && $result['payment_number'] == "0") {
        // استعلام تحديث البيانات في قاعدة البيانات، مع إضافة التاريخ إلى العمود chekdate
        $sql = "UPDATE information SET amount = :amount, payment_number = :payment_number, chekdate = :checkdate WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':payment_number', $payment_number); // ربط رقم الدفع
        $stmt->bindParam(':checkdate', $checkdate); // ربط تاريخ اليوم مع العمود chekdate
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('تم تحديث الحالة بنجاح');
                    setTimeout(function() {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    }, 2000);
                  </script>";
        } else {
            // في حالة حدوث خطأ أثناء التحديث
            echo "<script>alert('خطأ في تحديث الحالة');</script>";
        }
    } else {
        // إذا كانت القيم في قاعدة البيانات أكبر من 0، لا يمكن التحديث
            echo "<script>alert('هااااخطأ في تحديث الحالة');</script>";
    }
} else {
    // إذا لم يتم العثور على السجل، يمكن إضافة البيانات
    echo "السجل غير موجود في قاعدة البيانات.";
}
}




if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $new_status = $_POST['new_status'];

    // إضافة التحقق من قيم amount و payment_number
    $sql_check = "SELECT amount, payment_number FROM information WHERE id = :id";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();
    $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // التحقق من القيمتين فقط عند تسليم للعمل الميداني (status == 3)
    if ($row_check['amount'] != 0 && $row_check['payment_number'] != 0 ) {
        // القيمتين غير صفر، يتم التمرير
        $sql = "UPDATE information SET status = :new_status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':new_status', $new_status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
                echo "<script>alert('تم تحديث الحالة بنجاح');</script>";

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
    echo "<script>alert('خطأ في تحديث الحالة');</script>";
        }
    } else {
        // لا يتم التمرير إذا كانت إحدى القيمتين صفر أو إذا لم تكن الحالة هي "تسليم للعمل الميداني"
    echo "<script>alert('خطأ في تحديث الحالة');</script>";
    }
}
// متغير لتخزين اسم العميل
$customer_name = '';
$id = ''; // متغير لتخزين id العميل
$height = '';
$the_space = '';
$request_type = '';

// التحقق من وجود قيمة البحث
if (isset($_POST['search']) && !empty($_POST['search_value'])) {
    $search_value = $_POST['search_value']; // القيمة المدخلة في حقل البحث

    // استعلام للبحث عن العميل بناءً على الرقم المدخل
    $sql = "SELECT id, customer_name, request_type, status, the_space, address, phone_number, center, amount, date_of_work, height
            FROM information WHERE id = :id OR phone_number = :id"; // بحث بالـ ID أو رقم الهاتف
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $search_value, PDO::PARAM_STR);
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // إذا تم العثور على نتيجة، نعرض اسم العميل
    if ($searchResults) {
        $customer_name = $searchResults[0]['customer_name'];
        $customer_id = $searchResults[0]['id']; // تخزين id العميل
        $the_space = $searchResults[0]['the_space'];
        $height = $searchResults[0]['height'];
        $id = $searchResults[0]['id'];
        $request_type = $searchResults[0]['request_type'];
        $amount = $searchResults[0]['amount'];
    } else {
        $customer_name = 'لا توجد بيانات لهذا الرقم'; // في حال لم يتم العثور على العميل
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محافظة قنا</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="col-12 account">
        <div class="container pt-5">
            <h3>البحث عن الطلب</h3>
            <div class="col-4 account">
                <form method="POST" action="">
                    <?php if ($customer_name): ?>
                        <h3>اسم العميل: <?php echo htmlspecialchars($customer_name); ?></h3>
                        <input type="hidden" name="id" value="<?php echo $customer_id; ?>">
                    <?php endif; ?>
                    <div class="row account">
                        <div class="col-4 account">
                            <label for="search_value">أدخل رقم الطلب أو رقم المعامله:</label>
                            <input type="text" id="search_value" name="search_value"><br><br>
                        </div>
                    </div>
                    <input type="submit" name="search" value="بحث" class="btn btn-success btn-lg">
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 account">
        <div class="container pt-5">
            <form id="data-form" class="account">
                <div class="row account">
                    <div class="col-3 account">
                        <?php if ($customer_name): ?>
                            <h3>اسم العميل:</h3>
                            <div id="nameInput" class="display-only account" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($customer_name); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3 account">
                        <?php if ($the_space): ?>
                            <h3>المساحه في العقد:</h3>
                            <div class="display-only account" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($the_space); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3 account">
                        <?php if ($height): ?>
                            <h3>طول المرفق:</h3>
                            <div class="display-only account" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($height); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3 account">
                        <?php if ($id): ?>
                            <h3>رقم الطلب:</h3>
                            <div class="display-only request-number account" id="id" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($id); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3 account">
                        <?php if ($request_type): ?>
                            <h3>نوع الطلب:</h3>
                            <div class="display-only request-number account" id="id" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($request_type); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row account">
                    <div class="col-4 account">
                        <label for="areaInput">المساحة:</label>
                        <input type="text" name="money_space" id="areaInput" onchange="calculateValue()" autocomplete="off"><br><br>
                    </div>
                    <div class="col-3 account">
                        <?php if (isset($amount)): ?>
                            <h3>المبلغ المدفوع:</h3>
                            <div class="display-only account" style="border: 1px solid #ccc; padding: 5px;">
                                <?php echo htmlspecialchars($amount); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="services account">
                    <label>قيمه اعمال:</label>
                    <div class="service-options account">
                        <input type="checkbox" name="service" value="الرفع المساحي"> الرفع المساحي
                        <input type="checkbox" name="service" value="كشف المرافق"> كشف المرافق<br>
                        <input type="checkbox" name="service" value="تحديد حيز عمراني"> تحديد حيز عمراني<br>
                        <input type="checkbox" name="service" value="اعاده طباعه"> اعاده طباعه<br>
                        <input type="checkbox" name="service" value="تحديد منسوب"> تحديد منسوب<br>
                        <input type="checkbox" name="service" value="تتبع زمني">تتبع زمني<br>
                        <input type="checkbox" name="service" value="تتبع زمني وتحديد منسوب">تتبع زمني وتحديدمنسوب<br>

                        <input type="checkbox" id="removeFees" onchange="toggleFees()" name="service" value="فرق التوريد"> فرق التوريد<br><br>
                    </div>
                </div>

                <div class="col-12 account">
                    <table class="account">
                        <tr>
                            <th>القيمه</th>
                            <th>البيان</th>
                        </tr>
                        <tr>
                            <th><input type="text" name="price" onchange="calculateValue(this)"></th>
                            <th>قيمه المبلغ  </th>
                        </tr>
                        <tr>
                            <th><input type="text" class="result account" readonly placeholder="النتيجة هنا"></th>
                            <th>قيمه 14% ضريبه</th>
                        </tr>
                        <tr>
                            <th>٥٫٠٠</th>
                            <th>طابع شهيد</th>
                        </tr>
                        <tr>
                            <th>٦٫٠٠</th>
                            <th>رسم تنميه</th>
                        </tr>
                        <tr>
                            <th><input type="text" class="total-result account" readonly></th>
                            <th>الاجمالي</th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <div class="total-words account"></div>
                            </th>
                        </tr>
                    </table>
                    <input type="hidden" name="number"><br><br>
                   
                    <input type="hidden" name="date" id="dateInputt"><br><br>
                    <button type="button" class="print-button account" onclick="printContent()">طباعة</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 account">
        <div class="table-container account">
            <table border="1" class="account">
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم العميل</th>
                    <th>مركز مدينه</th>
                    <th>العنوان</th>
                    <th>نوع الطلب</th>
                    <th>الحالة</th>
                    <th>المساحه</th>
                    <th>رقم الدفع</th>
                    <th>اضافه المبلغ</th>
                    <th>تحديث البيانات</th>
                    <th>تغيير الحالة</th>
                </tr>
                <?php
                if (count($results) > 0) {
                    foreach ($results as $row) {
                        if ($row["status"] == 2 || $row["status"] == 3) {
                            echo "<tr class='account'>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["customer_name"] . "</td>";
                            echo "<td>" . $row["center"] . "</td>";
                            echo "<td>" . $row["address"] . "</td>";
                            echo "<td>" . $row["request_type"] . "</td>";
                            echo "<td>" . $row["status"] . "</td>";
                            echo "<td>" . $row["the_space"] . "</td>";
                            echo '<form method="POST" id="aaf" class="account">';
                            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">';
                            echo '<input type="hidden" name="checkdate" value="' . date('Y-m-d') . '">';

                            echo "<td><input type='text' name='payment_number' id='numberq' value='' required ></td>";
                            echo "<td><input type='text' name='amount' value='" . htmlspecialchars($row["amount"]) . "' readonly class='account'></td>";
                            echo '<td><button type="submit" name="update_data" class="account">تحديث البيانات</button></td>';
                            echo '</form>';
echo "<td>";

if ($row["status"] == 2) {
    echo '<form method="POST" class="account">';
    echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
    echo '<input type="hidden" name="new_status" value="3">';
    // زر استلام من خدمة العملاء بلون مخصص
    echo '<input type="submit" name="update_status" value="استلام من خدمه العملاء" class="btn status-2">';
    echo '</form>';
} elseif ($row["status"] == 3) {
    echo '<form method="POST" class="account">';
    echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
    echo '<input type="hidden" name="new_status" value="4">';
    // زر تسليم للعمل الميداني بلون مخصص
    echo '<input type="submit" name="update_status" value="تسليم للعمل الميداني" class="btn status-3">';
    echo '</form>';
}

echo "</td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='11' class='account'>لا توجد بيانات.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
    // العثور على جميع الأزرار التي تحتوي على الكلاس "btn"
    const buttons = document.querySelectorAll(".btn");

    buttons.forEach(button => {
        button.addEventListener("click", function () {
            // إزالة الكلاس 'clicked' من جميع الأزرار الأخرى
            buttons.forEach(btn => btn.classList.remove("clicked"));

            // إضافة الكلاس 'clicked' للزر الذي تم الضغط عليه
            this.classList.add("clicked");
        });
    });
});

        // حفظ موضع التمرير في LocalStorage
        function saveScrollPosition() {
            localStorage.setItem('scrollPosition', window.scrollY);
        }

        // استعادة موضع التمرير عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            const scrollPosition = localStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
                localStorage.removeItem('scrollPosition'); // إزالة الموضع بعد استعادته
            }
        });

        // تعيين تاريخ اليوم في الحقل المخفي
        document.getElementById('dateInputt').value = new Date().toISOString().split('T')[0];
    </script>

    <script src="js/script.js"></script>
</body>


</html>
