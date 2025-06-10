<?php
require "connect.php";
require "home_page.php";

// استرجاع البيانات من قاعدة البيانات
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM information WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt_works = $conn->prepare("SELECT * FROM information WHERE id = :id");
    $stmt_works->bindParam(':id', $id);
    $stmt_works->execute();
    $works = $stmt_works->fetchAll(PDO::FETCH_ASSOC);
} else {
    $record = [];
    $works = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مقايسة أعمال - محافظة قنا</title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .document {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 1cm;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            border: 1px solid #000;
            padding: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: right;
        }

        .notes {
            margin-top: 20px;
        }

        .notes ol {
            padding-right: 20px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="document" id="printArea">
        <div class="header">
            <img src="path_to_logo.png" alt="شعار" class="logo">
            <div>
                <div>محافظة قنا</div>
                <div>مركز معلومات شبكات مرافق قنا</div>
            </div>
            <div>0000001</div>
        </div>

        <div class="title">مقايسة أعمال رقم: <span id="estimateNumber"></span></div>

        <div class="info-row">
            <div>نوع الطلب: <span id="requestType"></span></div>
            <div>بتاريخ: <span id="date"></span></div>
        </div>
        <div class="info-row">
            <div>سارية حتى: <span id="validUntil"></span></div>
        </div>
        <div class="info-row">
            <div>السادة: <span id="clientName"></span></div>
        </div>
        <div>بناء على طلبكم المقدم بخصوص: <span id="requestDetails"></span></div>
        <div>بالعنوان الآتي: <span id="address"></span></div>

        <div>بيان الأعمال المطلوبة طبقا للكروكي المقدم من سيادتكم كالتالي:</div>
        <div>إجمالي الأطوال:</div>

        <table>
            <thead>
                <tr>
                    <th>الأعمال المطلوبة</th>
                    <th>التكلفة</th>
                </tr>
            </thead>
            <tbody id="workTable">
                <!-- الصفوف ستضاف ديناميكيًا هنا -->
            </tbody>
        </table>

        <div>التكلفة: <span id="totalCost"></span> جنيها ( <span id="totalCostInWords"></span> فقط )</div>

        <div class="notes">
            <strong>مع مراعاة الآتي:</strong>
            <ol>
                <li>المركز جهة حكومية تابعة لمحافظة قنا ولا يخضع لأي ضرائب أو رسوم أو نفقات.</li>
                <li>يتم الحساب النهائي للتكلفة بعد الانتهاء من الأعمال وقياس الأطوال بكل دقة وذلك طبقاً للكروكي المستلم من سيادتكم.</li>
                <li>في حالة العمل في مسار مختلف غير محدد بالكروكي أو وجود أطوال زائدة عن الطول المصدر بالمقايسة يتم الرجوع للمركز أولاً قبل التنفيذ لاستخراج مقايسة جديدة بالكروكي المعدل وإنتاج الخرائط المطلوبة للمسار.</li>
                <li>ضرورة تقديم جدول زمني للمشروعات أكبر من 1000 متر موضح بها أولويات التنفيذ قبل استلام الرأي الفني.</li>
                <li>في حالة وجود أعمال دفع نقدي يتم الالتزام بتسليم المركز أسطوانة مدمجة لمخطط الدفع النقدي وأماكن تنفيذه ونقطة الدخول والخروج.</li>
            </ol>
        </div>

        <div class="footer">
            <div>
                <div>المستلم:</div>
                <div>الجهة:</div>
                <div>التليفون:</div>
                <div>الرقم القومي:</div>
                <div>الصفة:</div>
            </div>
            <div>
                <div>رئيس قسم خدمة العملاء</div>
                <div>مدير المركز</div>
            </div>
        </div>
    </div>

    <button onclick="printDocument()" class="no-print">طباعة</button>

    <body>
    <div class="document" id="printArea">
        <div class="header">
            <div>محافظة قنا</div>
            <div>مركز معلومات شبكات مرافق قنا</div>
            <div>رقم المعرف: <?php echo htmlspecialchars($record['id']); ?></div>
        </div>

        <div class="title">بيانات الطلب</div>

        <div class="info-row">
            <div>اسم العميل: <?php echo htmlspecialchars($record['customer_name']); ?></div>
            <div>نوع الطلب: <?php echo htmlspecialchars($record['request_type']); ?></div>
        </div>
        <div class="info-row">
            <div>تاريخ الطلب: <?php echo isset($record['date']) ? date('d/m/Y', strtotime($record['date'])) : ''; ?></div>
            <div>الصفة: <?php echo htmlspecialchars($record['his_description']); ?></div>
        </div>

        <button onclick="printDocument()" class="no-print">طباعة</button>
    </div>

    <script>
        function printDocument() {
            window.print();
        }
    </script>
</body>

</html>
