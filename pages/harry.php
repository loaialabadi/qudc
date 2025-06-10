<?php


require "connect.php";
require "home_page.php";





$currentRecord = [];
$searchResult = ''; // تأكد من تهيئة المتغير هنا
if (isset($_POST['search'])) {
    $id_to_search = filter_input(INPUT_POST, 'id_to_search', FILTER_VALIDATE_INT);

    if ($id_to_search) {
        $stmt = $conn->prepare("SELECT customer_name, Applicant_name FROM information WHERE id = :id");
        $stmt->bindParam(':id', $id_to_search);
        $stmt->execute();
        
        $currentRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentRecord) {
            $searchResult = "<div>اسم العميل: " . htmlspecialchars($currentRecord['customer_name']) . "</div>";
            $searchResult .= "<div>المساحة: " . htmlspecialchars($currentRecord['Applicant_name']) . "</div>";
        } else {
            $searchResult = "<div>لا يوجد سجل بهذا المعرف.</div>";
        }
    } else {
        $searchResult = "<div>الرجاء إدخال ID صحيح.</div>";
    }
}



        


?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محافظة قنا</title>
    <style>
        body {
            font-family: Arial, sans-serif; 
            margin: 20px;
            line-height: 1.6;
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .header {
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"] {
            width: 90%;
            padding: 5px;
            margin: 5px 0;
        }

        .result {
            background-color: #f0f0f0;
            border: none;
            width: 90%;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .logo {
            position: absolute; 
            top: 20px; 
            left: 100px; 
            width: 100px; 
            height: auto; 
        }

        .header {
            display: flex;
            justify-content: space-between;
        }
        .span{
            display: flex; justify-content: space-between;

        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-section {
                page-break-inside: avoid;
                height: 31vh; /* تقليل الارتفاع */
                padding: 3mm; /* تقليل الهوامش */
                box-sizing: border-box;
                margin-bottom: 2mm;
            }

            .print-content {
                border: 1px solid #ccc;
                padding: 3mm;
                height: 100%;
            }

            table {
                font-size: 8px; /* تصغير حجم خط الجدول */
                margin: 2mm 0;
            }

            th, td {
                padding: 1mm;
            }

            h1 {
                font-size: 12px;
                margin: 2mm 0;
            }

            h2 {
                font-size: 10px;
                margin: 2mm 0;
            }

            h3 {
                font-size: 8px;
                margin: 1mm 0;
            }

            p {
                font-size: 8px;
                margin: 1mm 0;
                line-height: 1.2;
            }

            .signature-section {
                margin-top: 2mm;
                font-size: 8px;
            }
        }


    </style>
    <script>
        function calculateValue(input) {
            const row = input.closest('tr');
            const price = parseFloat(input.value) || 0;
            const percentage = 0.14;
            const tax = price * percentage;
            const stamp = 5.00; 
            const extraStamp = 2.00; 
            const total = price + tax + stamp + extraStamp; 
            
            row.nextElementSibling.querySelector('.result').value = arabicNumber(tax.toFixed(2));
            document.querySelector('.total-result').value = arabicNumber(total.toFixed(2));
            document.querySelector('.total-words').innerText = convertNumberToWords(Math.round(total));
        }







        function printContent() {
            const nameInput = document.getElementById('nameInput').value;
            const areaInput = document.getElementById('areaInput').value;
            const dateInput = document.querySelector('input[name="date"]').value;
            const formattedDate = formatArabicDate(dateInput);
            const totalInput = document.querySelector('.total-result').value;
            const services = Array.from(document.querySelectorAll('input[name="service"]:checked'))
                .map(checkbox => checkbox.value).join(', ');
            const priceInput = document.querySelector('input[name="price"]').value;
            const taxValue = document.querySelector('.result').value;

            const printWindow = window.open('', '', 'width=800,height=600');
            const content = `
                <div class="print-section">
                    <div class="print-content">
                    <div class="header-group">>
                        <h1>محافظة قنا</h1>
                        <h2>مركز شبكات معلومات مرافق قنا</h2>
                        </div>

                        <div  class="info-line">
                        <p>السيد / مدير الحسابات بديوان عام محافظة قنا</p>
                        <p>بعد التحية ،،،،</p>
                        <p>برجاء قبول المبالغ الموضحة بعد من السيد / <strong>${nameInput}</strong></p>
                        <p>قيمه اعمال: <strong>${services}</strong> &nbsp;&nbsp; المساحة: <strong>${arabicNumber(areaInput)}</strong></p>
                        <p>طبقا لطلب المواطن وذلك لحساب مركز معلومات شبكات المرافق علي حساب رقم: ٩/٤٥٠/٧٧٩٨٢/٧</p>

                        </div>
                        <table>
                            <tr>
                                <th>القيمه</th>
                                <th>البيان</th>
                            </tr>
                            <tr>
                                <td>${arabicNumber(priceInput)}</td>
                                <th>مبلغ قيمه الكشف</th>
                            </tr>
                            <tr>
                                <td>${arabicNumber(taxValue)}</td>
                                <td>قيمه 14% ضريبه</td>
                            </tr>
                            <tr>
                                <td>${arabicNumber('5.00')}</td>
                                <td>طابع شهيد</td>
                            </tr>
                            <tr>
                                <td>${arabicNumber('2.00')}</td>
                                <td>رسم تنميه</td>
                            </tr>
                            <tr>
                                <td><strong>${arabicNumber(totalInput)}</strong></td>
                                <td>الاجمالي</td>
                            </tr>
                        </table>
                        <p>والمورد بالقسيمه مجموعه 33 ع.ح رقم:________________بتاريخ: <strong>${formattedDate}</strong></p>
                        <p>تحرير في <strong>${formattedDate}</strong></p>
                        <div class="signature-section">
                            <span>مندوب الصرف</span>
                            <span>مدير المركز</span>
                        </div>
                    </div>
                </div>`;

            printWindow.document.write(`
                <html lang="ar" dir="rtl">
                    <head>
                        <meta charset="UTF-8">
                        <title>طباعة</title>
                        <style>
                            @page {
                                size: A4;
                                margin: 0;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                                font-family: Arial, sans-serif;
                            }
                            .print-section {
                                height: 31vh;
                                padding: 3mm;
                                box-sizing: border-box;
                                margin-bottom: 2mm;
                                page-break-inside: avoid;
                            }
                            .print-content {
                                border: 1px solid #ccc;
                                padding: 3mm;
                                height: 100%;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin: 2mm 0;
                                font-size: 14px;
                            }
                            th, td {
                                border: 1px solid #000;
                                padding: 1mm;
                                text-align: center;
                            }
                            h1 { font-size: 12px; margin: 2mm 0; text-align: center; }
                            h2 { font-size: 10px; margin: 2mm 0; text-align: center; }
                            h3 { font-size: 8px; margin: 1mm 0; }
                            p { font-size: 12px; margin: 1mm 0; line-height: 1.2; }
                            .signature-section {
                                margin-top: 2mm;
                                display: flex;
                                justify-content: space-between;
                                font-size: 8px;
                            }

                            .header-group, .info-line {
    display: flex;
    flex-wrap: wrap; 
    gap: 10px;
}

.header-group h1, .header-group h2, .info-line p {
    margin: 0; 
    font-size: 16px;
}
    .signature-section{

    font-size: 12px;
    
    }


                        </style>
                    </head>
                    <body>
                        ${content}
                        ${content}
                        ${content}
                    </body>
                </html>
            `);
            printWindow.document.close();
            setTimeout(() => printWindow.print(), 500);
        }



        function arabicNumber(num) {
            const arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return num.toString().replace(/\d/g, d => arabicDigits[d]);
        }

        function formatArabicDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            const arabicMonths = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
            const arabicWeekdays = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "سبت"];
            const day = arabicNumber(date.getDate());
            const month = arabicMonths[date.getMonth()];
            const year = arabicNumber(date.getFullYear());
            const weekday = arabicWeekdays[date.getDay()];
            return `${weekday}، ${day} ${month} ${year}`;
        }

        function convertNumberToWords(number) {
            const units = [
                '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 
                'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 
                'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 
                'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 
                'تسعة عشر'
            ];
            const tens = [
                '', '', 'عشرون', 'ثلاثون', 'أربعون', 
                'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
            ];
            const hundreds = [
                '', 'مئة', 'مئتان', 'ثلاثمئة', 'أربعمئة', 
                'خمسمئة', 'ستمئة', 'سبعمئة', 'ثمانمئة', 'تسعمئة'
            ];
            const thousands = [
                '', 'ألف', 'ألفان', 'ثلاثة آلاف', 'أربعة آلاف', 
                'خمسة آلاف', 'ستة آلاف', 'سبعة آلاف', 'ثمانية آلاف', 'تسعة آلاف'
            ];

            if (number < 0) return "سالب " + convertNumberToWords(-number);
            if (number === 0) return "صفر";

            let words = '';

            if (number >= 1000) {
                words += thousands[Math.floor(number / 1000)];
                number %= 1000;
            }

            if (number >= 100) {
                words += " " + hundreds[Math.floor(number / 100)];
                number %= 100;
            }

            if (number >= 20) {
                words += " " + tens[Math.floor(number / 10)];
                number %= 10;
            }

            if (number > 0) {
                words += " " + units[number];
            }

            return words.trim();
        }
    </script>
</head>
<body>



<form  method="post">

        <h3>ابحث عن سجل باستخدام ID</h3>
        <input type="number" name="id_to_search" placeholder="ابحث باستخدام ID">
        <input type="submit" name="search" value="بحث">
        <div class="search-results">
            <?php echo $searchResult; ?>
        </div>


    </form>



    <button type="button" class="print-button" onclick="printContent()">طباعة</button>

    <div class="header">
        <img src="logo.png" alt="شعار" class="logo">
        <h1>محافظة قنا</h1>
        <h2>مركز شبكات معلومات مرافق قنا</h2>
    </div>
    <form id="data-form">
        <label for="nameInput">اسم العميل:</label>
        <input type="text" id="nameInput" ><br><br>
        <label for="areaInput">المساحة:</label>
        <input type="text" id="areaInput" ><br><br>
       
        <label> قيمه اعمال:</label>
        <input type="checkbox" name="service" value="رفع مساحي "> رفع مساحي
        <input type="checkbox" name="service" value="كشف مرافق ">  كشف مرافق<br>
        <h3>طبقا لطلب المواطن وذلك لحساب مركز معلومات شبكات المرافق علي حساب رقم: ٩/٤٥٠/٧٧٩٨٢/٧</h3>

        <table>
            <tr>
                <th>القيمه</th>
                <th>البيان</th>
            </tr>
            <tr>
                <td><input type="text" name="price" onchange="calculateValue(this)"></td>
                <th> مبلغ قيمه الكشف </th>
            </tr>
            <tr>
                <td><input type="text" class="result" readonly></td>
                <td>قيمه 14% ضريبه</td>
            </tr>
            <tr>
                <td>٥٫٠٠</td>
                <td>طابع شهيد</td>
            </tr>
            <tr>
                <td>٢٫٠٠</td>
                <td>رسم تنميه</td>
            </tr>
            <tr>
               <td><input type="text" class="total-result" readonly></td>
               <td>الإجمالي</td>
            </tr>
            <tr>
               <td colspan="2"><div class="total-words"></div></td>
            </tr>
        </table>
       

        <p> والمورد بالقسيمه مجموعه 33 ع.ح رقم:________________بتاريخ: </p>
        <p>تحرير في </p>
        <input type="date" name="date" ><br><br>

        <p>
    
    
    <span>مدير المركز</span>
    <span>مدير المركز</span>

</p>

       



   




 
    </form>
</body>
</html>
