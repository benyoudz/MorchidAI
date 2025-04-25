<?php
include 'db_configpdo.php';

header('Content-Type: application/json');

$message = $_POST['message'] ?? '';

if (!empty($message)) {
    // إرسال الإشعار عبر Telegram
    sendTelegramNotification($message);
    
    // حفظ الإشعار في قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO notifications (message, created_at) VALUES (?, NOW())");
    $stmt->execute([$message]);
    
    echo json_encode(['status' => 'success', 'message' => 'تم إرسال الإشعار بنجاح']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'الرسالة فارغة']);
}
?> 