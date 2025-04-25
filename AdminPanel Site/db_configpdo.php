<?php

// إعدادات قاعدة البيانات ب pdo 
$host = 'localhost';
$db   = 'morchid_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// إعدادات Telegram Bot
define('TELEGRAM_BOT_TOKEN', '7700399271:AAGjQwQQlG7nVv-QmTY6E2Y9_0Rseb1KhN4');
define('TELEGRAM_CHAT_ID', '2103225854');

function sendTelegramNotification($message) {
    $url = "https://api.telegram.org/bot".TELEGRAM_BOT_TOKEN."/sendMessage";
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>