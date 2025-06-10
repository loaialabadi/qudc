<?php
require "connect.php";
session_start();


// تحقق مما إذا كان المستخدم قد سجل الدخول بالفعل
if (isset($_SESSION['username'])) {
    header("Location: home_page.php");
    exit();
}


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $passwrd = $_POST['password'];
    $option = $_POST['options'];

    // استخدام prepared statement للتحقق من اسم المستخدم وكلمة المرور
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username AND option = :option");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":option", $option);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $stmt->bindColumn(1, $stored_password);
        $stmt->fetch(PDO::FETCH_BOUND);

        // التحقق من كلمة المرور
        if (password_verify($passwrd, $stored_password)) {
            // تخزين اسم المستخدم في الجلسة
            $_SESSION['username'] = $username;
            $_SESSION['options'] = $option;

            header("Location: home_page.php");
            exit(); // تأكد من إنهاء تنفيذ السكربت بعد التوجيه
        } else {
            echo "<div class='error'>كلمة المرور غير صحيحة.</div>";
        }
    } else {
        echo "<div class='error'>اسم المستخدم غير موجود.</div>";
    }
}


?>

<!-- <link rel="stylesheet" href="styles.css"> -->

<link rel="stylesheet" href="bootstrap.min.css">

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منظومة شبكات ومعلومات المرافق بقنا</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color:rgb(37, 66, 95);
            color: #495057;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4CAF50;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin-top: 50px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 15px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.4);
        }
        .btn-success {
            background-color: #4CAF50;
            border-color: #4CAF50;
            font-size: 1.2rem;
            padding: 15px;
            width: 100%;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .logo {
            display: block;
            margin: 20px auto;
            max-width: 150px; /* تعديل حجم الشعار */
        }
        .text-center h2 {
            font-size: 2.5rem;
            color: #4CAF50;
        }
        .form-group select {
            border-radius: 8px;
            padding: 15px;
            font-size: 1rem;
            border: 1px solid #ced4da;
        }
        .form-group select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.4);
        }
        @media (max-width: 768px) {
            .container {
                width: 90%;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <img src="logo.png" alt="شعار المنظومة" class="logo"> <!-- الشعار هنا -->
        <h2>مرحبا بك في منظومه شبكات ومعلومات المرافق بقنا</h2>
    </div>
    <div class="container">
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">اسم المستخدم:</label>
                <input class="form-control" type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input class="form-control" type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="options">الخيارات:</label>
                <select class="form-control" id="options" name="options" required>
                    <option value="system">النظم</option>
                    <option value="work_field">العمل الميداني</option>
                    <option value="manager">المدير</option>
                    <option value="customer_service">خدمه العملاء</option>
                    <option value="accounts">الحسابات</option>
                    <option value="delivery">التسليم</option>
                    <option value="admin"></option>
                </select>
            </div>
            <button class="btn btn-success" type="submit" name="login">تسجيل الدخول</button>
        </form>
        <div class="footer-text">
            <p>جميع الحقوق محفوظة لدي--م\لؤي حمدون</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
