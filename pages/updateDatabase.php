<?php
// تضمين الاتصال بقاعدة البيانات
require 'connect.php';

// استلام البيانات المرسلة عبر الـ POST (JSON)
$data = json_decode(file_get_contents("php://input"));

// التحقق من وجود البيانات المطلوبة
if (isset($data->id) && isset($data->result)) {
    $id = $data->id; // معرّف العميل
    $result = $data->result; // المبلغ المحسوب

    try {
        // استعلام لتحديث المبلغ في قاعدة البيانات
        $sql = "UPDATE information SET amount = :result WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // ربط المعاملات بالـ SQL
        $stmt->bindParam(':result', $result, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // تنفيذ الاستعلام
        if ($stmt->execute()) {
            // في حال نجاح التحديث
            echo json_encode(["success" => true]);
        } else {
            // في حال فشل التحديث
            echo json_encode(["success" => false, "message" => "Error updating record"]);
        }
    } catch (PDOException $e) {
        // في حال حدوث استثناء أثناء الاتصال بقاعدة البيانات
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    // في حال عدم وجود البيانات المطلوبة
    echo json_encode(["success" => false, "message" => "Missing data"]);
}

// غلق الاتصال بقاعدة البيانات
$conn = null;
?>
