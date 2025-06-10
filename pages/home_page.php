<?php
require "connect.php";


// auth.php

// التحقق إذا كان المستخدم قد سجل دخوله
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['options'])) {
    // إذا لم يكن مسجلاً، قم بإعادة توجيه المستخدم إلى صفحة تسجيل الدخول
    header("Location: login.php");
    exit();
}

// الحصول على قيم الجلسة
$option = $_SESSION['options'];
$username = $_SESSION['username'];

// الصفحات المسموح بها لكل نوع من المستخدمين
$allowedPages = [
    'system' => ['page_system.php','home_page.php'],
    'work_field' => ['page_work_field.php','home_page.php'],
    'customer_service' => ['page_customer_service.php','home_page.php'],
    'accounts' => ['home_page.php','test.php','money.php'],
    'manager' => ['page_work_field.php', 'dashboard.php', 'page_system.php', 'page_customer_service.php', 'page_screen.php', 'notifications.php', 'measurement.php', 'page_report.php', 'delivery_Page.php', 'money.php','home_page.php' ,'test.php' ,'test1.php', 'inquiry.php','testt.php', 'admin.php'],
  'delivery' =>['delivery_Page.php','home_page.php', 'page_customer_service.php', 'inquiry.php'],
    'admin' => ['admin.php', 'page_work_field.php', 'dashboard.php', 'page_system.php', 'page_customer_service.php', 'page_screen.php', 'notifications.php', 'measurement.php', 'page_report.php', 'delivery_Page.php', 'money.php','home_page.php', 'inquiry.php', 'test.php']
];

// التحقق من الصفحة الحالية
$currentPage = basename($_SERVER['PHP_SELF']);
if (!in_array($currentPage, $allowedPages[$option])) {
    echo "<script>alert('عفواً، ليس لديك صلاحية للوصول إلى هذه الصفحة.'); window.location.href='login.php';</script>";
    exit();
}

function renderLink($url, $text, $option)
{
    global $allowedPages;

    if (isset($allowedPages[$option]) && in_array($url, $allowedPages[$option])) {
        echo "<a href=\"$url\">$text</a>";
    } else {
        echo "<a href=\"#\" onclick=\"alert('عفوا ليس لديك صلاحيه'); return false;\">$text</a>";
    }
}



if (isset($_POST['logout'])) {
    // تدمير الجلسة
    session_unset();
    session_destroy();

    // إعادة التوجيه إلى صفحة تسجيل الدخول
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="bootstrap.min.css">
    

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج الإدخال</title>


</head>

<body>
    
<header class="home-header">
    <h1 class="home-title">منظومه مركز معلومات شبكات المرافق بقنا</h1>
    <nav class="home-nav">
        <ul class="home-nav-list">
            <li class="home-nav-item"><?php renderLink("page_customer_service.php", "خدمة العملاء", $option); ?></li>
             <li class="home-nav-item"><?php renderLink("test.php", "الحسابات", $option); ?></li>
           
            <li class="home-nav-item"><?php renderLink("page_work_field.php", "العمل الميداني", $option); ?></li>
            <li class="home-nav-item"><?php renderLink("page_system.php", "النظم", $option); ?></li>
                   <li class="home-nav-item"><?php renderLink("delivery_Page.php", "التسليم", $option); ?></li>
            <li class="home-nav-item"><?php renderLink("dashboard.php", "لوحة التحكم", $option); ?></li>
            <li class="home-nav-item"><?php renderLink("page_report.php", "التقارير", $option); ?></li>
     
            <li class="home-nav-item"><?php renderLink("admin.php", "admin", $option); ?></li>
             <li class="home-nav-item"><?php renderLink("inquiry.php", "استعلام", $option); ?></li>
             <li class="home-nav-item"><?php renderLink("money.php", "old", $option); ?></li>


           
            
          

        </ul>
        <form method="post" action="" class="home-logout-form">
            <button type="submit" name="logout" class="home-logout-button">تسجيل الخروج</button>
        </form>
    </nav>

    
</header>


   

 


<div class="col-3 position-absolute end-0" style="margin-top: 35px;">
    <h6>مرحبًا بك، <?php echo htmlspecialchars($username); ?></h6>
</div>




    



</body>


</html>