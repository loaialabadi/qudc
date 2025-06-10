<?php
require "connect.php";
require "home_page.php";


// تسجيل مستخدم جدبد
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $passwrd = $_POST['password'];
    $option = $_POST['options'];

    // تشفير كلمة المرور
    $hashed_password = password_hash($passwrd, PASSWORD_DEFAULT);

    // فحص وجود اسم المستخدم مسبقًا
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<div class='error'>اسم المستخدم موجود بالفعل.</div>";
    } else {
        // استخدام prepared statement لإضافة المستخدم
        $stmt = $conn->prepare("INSERT INTO users (username, password, option) VALUES (:username, :password, :option)");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":option", $option);

        if ($stmt->execute()) {
            echo "<div class='success'>تم تسجيل المستخدم بنجاح!</div>";
        } else {
            echo "<div class='error'>خطأ: " . $stmt->errorInfo()[2] . "</div>";
        }
    }
}



if (isset($_POST['d_u_b'])) {
    // تحقق من وجود اسم المستخدم أو المعرف في الطلب
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // استخدام prepared statement لحذف المستخدم
        $stmt = $conn->prepare("DELETE FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);

        if ($stmt->execute()) {
            echo "<div class='success'>تم حذف المستخدم بنجاح!</div>";
        } else {
            echo "<div class='error'>خطأ: " . $stmt->errorInfo()[2] . "</div>";
        }
    }
}

// استعلامات قاعدة البيانات
$users = [];
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$records = [];
$stmt = $conn->prepare("SELECT * FROM information");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


</head>

<body>
   


    <main>
        <h2>تسجيل المستخدم</h2>
        <form method="post" action="">
            اسم المستخدم: <input type="text" name="username" required>
            كلمة المرور: <input type="password" name="password" required>

            <select id="options" name="options">
                <option value="system">النظم</option>
                <option value="work_field">العمل الميداني</option>
                <option value="manager">المدير</option>
                <option value="customer_service">خدمه العملاء</option>
                <option value="accounts">الحسابات</option>
                <option value="delivery">التسليم</option>


                <option value="admin">admin</option>

            </select>

            <button type="submit" name="register">تسجيل</button>
        </form>





        <h2>المستخدمون</h2>
        <table>
            <thead>
                <tr>
                    <th>اسم المستخدم</th>
                    <th>الخيار</th>
                    <th>الاجراءت</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['option']); ?></td>
                        <td>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                                <button type="submit"name="d_u_b">حذف</button>
                            </form>


                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

   





    </main>

</body>

</html>