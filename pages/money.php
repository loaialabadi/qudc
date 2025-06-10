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

    <script>
        function calculateValue() {
            const areaInput = document.getElementById('areaInput').value;
            const priceInput = document.querySelector('input[name="price"]');
            const resultInput = document.querySelector('.result');
            let calculatedValue;

            // تحقق مما إذا كان هناك إدخال في حقل القيمة
            if (priceInput.value) {
                calculatedValue = parseFloat(priceInput.value);
            } else {
                calculatedValue = parseFloat(areaInput) * 5;
            }

            // إذا كان المدخل رقمًا، يظهر النتيجة
            if (!isNaN(calculatedValue)) {
                priceInput.value = calculatedValue.toFixed(2);
                calculateTotal(calculatedValue);
            } else {
                priceInput.value = '';
                resultInput.value = '';
            }
        }

        function calculateTotal(price) {
            const taxPercentage = 0.14;
            const additionalCharges = 6 + 5; // 2 + 7

            const tax = price * taxPercentage;
            const total = price + tax + additionalCharges;

            document.querySelector('.result').value = tax.toFixed(2);
            document.querySelector('.total-result').value = total.toFixed(2);


            // تحويل الإجمالي إلى كلمات وعرضه في الخانة المناسبة
            const totalInWords = convertNumberToWords(total); // تحويل الإجمالي إلى كلمات
            document.getElementById('totalInWords').value = totalInWords; // عرض الإجمالي بالكلمات في الحقل
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






            // التحقق من حالة الـ checkbox "فرق التوريد"
            const isRemoveFeesChecked = document.getElementById('removeFees').checked;

            // إذا كان الـ checkbox مفعلًا، لا نعرض طابع شهيد ورسم تنمية
            let martyrStampRow = '';
            let developmentFeeRow = '';

            if (!isRemoveFeesChecked) {
                martyrStampRow = `
            <tr>
                <td>${arabicNumber('5.00')}</td>
                <td>طابع شهيد</td>
            </tr>
            <tr>
                <td>${arabicNumber('6.00')}</td>
                <td>رسم تنميه</td>
            </tr>
        `;
            }





            const printWindow = window.open('', '', 'width=800,height=600');
            const content = `
        <div class="print-section">
            <div class="print-content">
                <div class="header-group">
                    <h1>محافظة قنا</h1>
                    <h2>مركز معلومات شبكات  مرافق قنا</h2>
                </div>

                <div class="info-line">
                    <p>السيد / مدير الحسابات بديوان عام محافظة قنا</p>
                    <p>بعد التحية ،،،،</p>
                    <p>برجاء قبول المبالغ الموضحة بعد من السيد / <strong>${nameInput}</strong></p>
                    <p>قيمه اعمال: <strong>${services}</strong> &nbsp;&nbsp; المساحة: <strong>${arabicNumber(areaInput)} م</strong></p>
                    <p>طبقا لطلب المواطن وذلك لحساب مركز معلومات شبكات المرافق علي حساب رقم: ٩/٤٥٠/٧٧٩٨٢/٧</p>
                </div>

                <table>
                    <tr>
                        <th>القيمه</th>
                        <th>البيان</th>
                    </tr>
                    <tr>
                        <td>${arabicNumber(priceInput)}</td>
                        <th>مبلغ قيمه ${services}</th>
                    </tr>
                    <tr>
                        <td>${arabicNumber(taxValue)}</td>
                        <td>قيمه 14% ضريبه</td>
                    </tr>
                    ${martyrStampRow}
                    <tr>
                        <td><strong>${arabicNumber(totalInput)}</strong></td>
                        <td>الاجمالي</td>
                    </tr>
                </table>

                <p>والمورد بالقسيمه مجموعه ٣٣  ع.ح رقم:________________بتاريخ: <strong>${formattedDate}</strong></p>
                <p>تحرير في <strong>${formattedDate}</strong></p>
                <div class="signature-section">
                    <span>مندوب الصرف</span>
                    <span>مدير المركز</span>
                </div>
            </div>
        </div>
    `;

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
            const arabicWeekdays = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
            const day = arabicNumber(date.getDate());
            const month = arabicMonths[date.getMonth()];
            const year = arabicNumber(date.getFullYear());
            const weekday = arabicWeekdays[date.getDay()];
            return `${weekday} - الموافق ${day} ${month} ${year}`;
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

            // التعامل مع الأجزاء الصحيحة
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

            // التعامل مع الكسور
            if (number % 1 !== 0) {
                const fractionalPart = (number % 1).toFixed(2).split('.')[1]; // الحصول على الأجزاء العشرية
                words += " و";
                words += convertFractionalToWords(fractionalPart); // تحويل الجزء الكسري إلى كلمات
            }

            return words.trim();
        }

        // دالة لتحويل الجزء الكسري إلى كلمات
        function convertFractionalToWords(fractionalPart) {
            const fractionUnits = {
                "50": "نصف",
                "25": "ربع",
                "75": "ثلاثة أرباع",
                "33": "ثلث",
                "66": "ثلاثة أثلاث",
                "10": "عشر",



                "5": "خمسة من عشرة",
                "20": "عشرون من مئة"
            };

            if (fractionUnits[fractionalPart]) {
                return fractionUnits[fractionalPart];
            }

            // إذا كانت الأجزاء العشرية ليست كسوراً معروفة (مثل 0.75، 0.25)، نقرأها كأرقام.
            let words = '';
            for (let i = 0; i < fractionalPart.length; i++) {
                words += " " + convertNumberToWords(parseInt(fractionalPart.charAt(i)));
            }
            return words.trim();
        }




        function showTotalInWords() {
            const number = document.getElementById("inputNumber").value;
            const totalInWords = convertNumberToWords(parseInt(number));
            document.getElementById("totalInWords").textContent = totalInWords;
        }






        function toggleFees() {
            const isChecked = document.getElementById('removeFees').checked;

            const rows = document.querySelectorAll('table tr');
            // تحديد الصفوف التي تحتوي على "طابع شهيد" و "رسم تنمية"
            const martyrStampRow = rows[3]; // الصف الذي يحتوي على "طابع شهيد"
            const developmentFeeRow = rows[4]; // الصف الذي يحتوي على "رسم تنمية"

            // إذا تم تفعيل الـ checkbox، إخفاء هذه الصفوف
            if (isChecked) {
                martyrStampRow.style.display = 'none';
                developmentFeeRow.style.display = 'none';
            } else {
                martyrStampRow.style.display = '';
                developmentFeeRow.style.display = '';
            }

            // إعادة حساب الإجمالي بناءً على الحذف
            calculateTotalWithoutFees();
        }

        function calculateTotalWithoutFees() {
            const priceInput = document.querySelector('input[name="price"]').value;
            const taxValue = document.querySelector('.result').value;

            // إذا كان الـ checkbox مفعلًا، لا نضيف الطابع والرسم في الحساب
            const taxPercentage = 0.14;
            const additionalCharges = 2 + 5; // 2 + 7

            let total = parseFloat(priceInput) + parseFloat(taxValue);

            // إضافة الضرائب فقط، إذا لم يكن الـ checkbox مفعلًا
            if (!document.getElementById('removeFees').checked) {
                total += additionalCharges; // إضافة الطابع والرسم
            }

            // عرض الإجمالي النهائي
            document.querySelector('.total-result').value = total.toFixed(2);
            document.querySelector('.totalInWords').value = convertNumberToWords(total); // تحويل الإجمالي إلى كلمات
        }
    </script>
</head>

<body>
    <div class="container pt-5">
        <form id="data-form">
            <div class="row">

                <div class="col-4">
                    <label for="nameInput">اسم العميل:</label>
                    <input type="text" id="nameInput"><br><br>
                </div>

                <div class="col-4">
                    <label for="areaInput">المساحة:</label>
                    <input type="text" id="areaInput" onchange="calculateValue()"><br><br>


                </div>


            </div>





            <label> قيمه اعمال:</label>
            <input type="checkbox" name="service" value="الرفع المساحي"> الرفع المساحي
            <input type="checkbox" name="service" value="كشف المرافق">كشف المرافق<br>
            <!-- إضافة checkbox جديد -->
            <label for="removeFees">فرق التوريد:</label>
            <input type="checkbox" id="removeFees" onchange="toggleFees()"> فرق التوريد<br><br>




            <h3>طبقا لطلب المواطن وذلك لحساب مركز معلومات شبكات المرافق علي حساب رقم: ٩/٤٥٠/٧٧٩٨٢/٧</h3>

            <table>
                <tr>
                    <th>القيمه</th>
                    <th>البيان</th>
                </tr>
                <tr>
                    <th><input type="text" name="price" onchange="calculateValue(this)"></td>
                    <th> مبلغ قيمه الكشف </td>
                </tr>
                <tr>
                    <th><input type="text" class="result" readonly placeholder="النتيجة هنا"></td>
                    <th>قيمه 14% ضريبه</td>
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
                    <th><input type="text" class="total-result" readonly></th>
                    <th>الاجمالي</th>
                </tr>
                <tr>
                    <th colspan="2">
                        <div class="total-words"></div>
                    </th>
                </tr>
            </table>


            <p> والمورد بالقسيمه مجموعه 33 ع.ح رقم:________________بتاريخ: </p>
            <p>تحرير في </p>
            <input type="date" name="date"><br><br>
            <p>

                <span>مدير المركز</span>
                <span>مدير المركز</span>

            </p>

            <button type="button" class="print-button" onclick="printContent()">طباعة</button>




        </form>

    </div>

</body>

</html>