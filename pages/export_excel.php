<?php
require 'vendor/autoload.php';
require "connect.php"; // لإتصال قاعدة البيانات إذا كنت تحتاجه

// التحقق من وجود البيانات
if (isset($_POST['export_data'])) {
    try {
        // فك تشفير البيانات
        $data = unserialize(base64_decode($_POST['export_data']));
        
        if (!$data) {
            throw new Exception('لا توجد بيانات للتصدير');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // تعيين اتجاه الورقة من اليمين إلى اليسار
        $sheet->setRightToLeft(true);
        
        // تعيين الخط
        $sheet->getStyle('A1:P1')->getFont()->setName('Arial');
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        
        // العناوين
        $headers = [
            'ID', 'اسم العميل', 'اسم مقدم الطلب', 'صفة مقدم الطلب',
            'نوع الطلب', 'العنوان', 'الرقم القومي', 'رقم الهاتف',
            'اسم المرفق', 'المركز', 'طريقة الدفع', 'رقم الدفع',
            'المبلغ', 'تاريخ الدفع', 'تصنيف العميل', 'الحالة'
        ];
        
        // إضافة العناوين
        foreach (range('A', 'P') as $key => $column) {
            $sheet->setCellValue($column . '1', $headers[$key]);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // تنسيق العناوين
        $sheet->getStyle('A1:P1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E2E2']
            ]
        ]);
        
        // إضافة البيانات
        $row = 2;
        foreach ($data as $record) {
            // تحويل الحالة إلى نص
            $status_text = match ((int)$record['status']) {
                1 => 'تم الادخال',
                2 => 'تم التمرير الي العمل الميداني',
                3 => 'تم الاستلام من خدمه العملاء',
                4 => 'تم التمرير الي النظم',
                5 => 'تم الاستلام بواسطه النظم',
                6 => 'تم الانتهاء من النظم',
                7 => 'تم التسليم',
                default => 'غير معروف'
            };
            
            $rowData = [
                $record['id'],
                $record['customer_name'],
                $record['Applicant_name'],
                $record['his_description'],
                $record['request_type'],
                $record['address'],
                $record['national_number'],
                $record['phone_number'],
                $record['attachment_name'],
                $record['center'],
                $record['payment_method'],
                $record['payment_number'],
                $record['amount'],
                $record['entry_date'],
                $record['customer_rating'],
                $status_text
            ];
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }
        
        // تعيين headers التنزيل
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="تقرير_' . date('Y-m-d_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        // إنشاء الملف وحفظه
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_end_clean(); // تنظيف أي مخرجات سابقة
        $writer->save('php://output');
        exit;
        
    } catch (Exception $e) {
        error_log('Excel Export Error: ' . $e->getMessage());
        echo "<script>alert('حدث خطأ أثناء تصدير البيانات: " . 
             addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('لم يتم تحديد بيانات للتصدير'); window.history.back();</script>";
}