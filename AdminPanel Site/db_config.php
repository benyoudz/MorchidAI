<?php 
   
//الخصائص الخاصة بقاعدة البيانات عاديا


$host = 'localhost';
$dbUser = 'root';          // ← تأكد من اسم المستخدم الصحيح
$dbPassword = '';          // ← اتركها فارغة إذا لم تضع كلمة مرور
$dbName = 'morchid_db';

$conn = new mysqli($host, $dbUser, $dbPassword, $dbName);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
?>
