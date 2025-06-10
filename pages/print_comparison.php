<?php
// التحقق مما إذا كانت البيانات موجودة في URL
if (!empty($_GET)) {
    $currentRecord = $_GET; // يمكن استخدام $_GET مباشرة كمصفوفة
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            padding: 20px;
            background: white;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            text-align: left;
        }

        .form-title {
            border: 1px solid #000;
            padding: 8px;
            margin: 10px 0;
            text-align: right;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            gap: 10px;
        }

        .form-group {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label {
            white-space: nowrap;
        }

        .form-input {
            flex: 1;
            border: 1px solid #000;
            padding: 5px;
            min-height: 30px;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
           
        }

        .requirements-list {
            padding-right: 20px;
            line-height: 1.5;
            border: 2px solid #4CAF50; /* إطار الجدول */
        }

        .footer {
            background: #e8f5e9;
            margin-top: 20px;
            padding: 10px;
            text-align: center;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 20px 0;
        }


        table {
            border-collapse: collapse; /* لإزالة الفواصل بين الخلايا */
            width: 100%;
        }
        th, td {
          
        }
    </style>
    <script>
        function printSection() {
            window.print(); // استدعاء نافذة الطباعة
        }
    </script>
</head>
<body>
    <div class="container">
        <header class="header">
            <img src="logo.png" alt="شعار" class="logo">
            <h2>مركز معلومات الشبكات لمحافظة قنا</h2>
        </header>

        <div class="form-title"> تقرير فنى رقم :
         <strong><?php echo isset($currentRecord['id']) ? htmlspecialchars($currentRecord['id']) : '000001'; ?></strong>  
        </div>

        <div class="form-row">
            <div class="form-group">
                <span class="form-label">تاريخ الإصدار :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['entry_date']) ? htmlspecialchars($currentRecord['entry_date']) : '[تاريخ السريان]'; ?></strong></div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <span class="form-label">رقم أمر التكليف:</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['id']) ? htmlspecialchars($currentRecord['id']) : '[رقم امر التكليف]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">نوع الطلب :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['request_type']) ? htmlspecialchars($currentRecord['request_type']) : '[ نوع الطلب]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">طريقة السداد:</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['payment_method']) ? htmlspecialchars($currentRecord['payment_method']) : '[ طريقه السداد]'; ?></strong></div>
            </div>
        </div>

        <div class="section-title">أولا : بيانات العميل :</div>

        <div class="form-row">
            <div class="form-group">
                <span class="form-label">اسم العميل :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['customer_name']) ? htmlspecialchars($currentRecord['customer_name']) : '[اسم العميل ]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">صفته :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['his_description']) ? htmlspecialchars($currentRecord['his_description']) : '[صفته]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">الرقم القومى :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['national_number']) ? htmlspecialchars($currentRecord['national_number']) : '[ الرقم القومي]'; ?></strong></div>
            </div>
        </div>

        <div class="section-title">عنوان منطقة العمل : <strong><?php echo isset($currentRecord['address']) ? htmlspecialchars($currentRecord['address']) : '[ العنوان ]'; ?></strong></div>

        <div class="form-row">
            <div class="form-group">
                <span class="form-label">اسم المرفق :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['attachment_name']) ? htmlspecialchars($currentRecord['attachment_name']) : '[ المرفق ]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">باجمالي أطوال طبقا للكروكي المقدم من العميل :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['his_description']) ? htmlspecialchars($currentRecord['his_description']) : '[صفته]'; ?></strong></div>
            </div>
        </div>

        <div class="section-title">ثانيا : بيانات الرأي الفني :</div>
        <div class="form-row">
            <div class="form-group">
                <span class="form-label">تاريخ الرأي الفني :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['entry_date']) ? htmlspecialchars($currentRecord['entry_date']) : '[ تاريخ الراء ]'; ?></strong></div>
            </div>
            <div class="form-group">
                <span class="form-label">الجهة المنفذة :</span>
                <div class="form-input"> <strong><?php echo isset($currentRecord['his_description']) ? htmlspecialchars($currentRecord['his_description']) : '[صفته]'; ?></strong></div>
            </div>
        </div>

        <div class="section-title">ثالثا الاشتراطات الواجب الاتزام بها :</div>
        <div class="requirements-list">
            <p>1. الموافقة على الحفر مسؤولية الوحدة المحلية.</p>
            <p>2. على الوحدة المحلية إلزام الجهة المنفذة للأعمال بإخطار المركز قبل بدء العمل بـ 48 ساعة على الأقل (كتابيا - فاكس - إيميل) على أن يرفق بالإخطار صورة من موافقة الوحدة المحلية على الأعمال.</p>
            <p>3. على الجهة المنفذة إخطار المركز بأي تغيير أو تعديل في البرنامج الزمني بمدة لا تقل عن 24 ساعة.</p>
            <p>4. في حالة وجود تعديل في مسار الحفر أو أطوال زائدة عن الأبعاد التي تم العمل عليها من قبل المركز، لا يتم العمل نهائياً ويتم الرجوع للمركز لعمل الإجراءات اللازمة.</p>
            <p>5. تلتزم الجهة المنفذة بإمداد المركز بالرسومات التنفيذية النهائية لما تم تنفيذه من أعمال، وفي حالة عدم التسليم لا تقع أي مسؤولية على المركز.</p>
        </div>
        <br>
        <br>
        <table>
    <tr>
        <th> المستلم:</th>
        <th> رقم البطاقه:</th>
        <th>صقته :</th>
        <th>ت الاستلام:</th>

    </tr>
 
</table>

        <div class="signatures">
            <div>
                <p>رئيس قسم خدمة العملاء</p>
                <div></div>
            </div>
            <div>
                <p>مدير المركز</p>
                <p>م/ محمد مصطفى ياسين</p>
            </div>
        </div>

        <footer class="footer">
            العنوان: قنا - ديوان عام المحافظة - مركز معلومات شبكة المرافق
            <br>
            تليفون: ........................... فاكس: ...........................
        </footer>
    </div>

    <button type="button" onclick="printSection()">طباعة</button>
</body>
</html>
