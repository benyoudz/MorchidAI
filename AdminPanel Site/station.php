<?php
$host = 'localhost';
$db   = 'morchid_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// الاتصال بقاعدة البيانات
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "فشل الاتصال بقاعدة البيانات: " . $e->getMessage();
    exit;
}

// التحقق من وجود ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "معرف المحطة غير صالح.";
    exit;
}

$id = (int)$_GET['id'];

// جلب بيانات المحطة
$stmt = $pdo->prepare("SELECT * FROM transport_nodes WHERE id = ?");
$stmt->execute([$id]);
$station = $stmt->fetch();

if (!$station) {
    echo "لم يتم العثور على المحطة.";
    exit;
}

// توليد كود المحطة
$station_code = strtoupper($station['line_code']) . '-' . str_pad($station['stop_order'], 3, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرشدك | <?= htmlspecialchars($station['name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8fDCB2;
            --secondary-color: #5a9d7b;
            --text-color: #2d3748;
            --light-text: #4a5568;
            --border-color: #e2e8f0;
            --button-color: #38a169;
            --train-color: #4299e1;
            --background: #f7fafc;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: var(--background);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .app-header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 15px 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .app-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .app-title::before {
            content: "🚏";
            font-size: 1.8rem;
        }
        
        .container {
            flex: 1;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .station-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .station-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .station-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 25px 20px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .station-header h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .station-meta {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .meta-badge {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            backdrop-filter: blur(5px);
        }
        
        .station-body {
            padding: 25px;
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-item {
            margin-bottom: 12px;
        }
        
        .detail-label {
            font-size: 0.9rem;
            color: var(--light-text);
            margin-bottom: 5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .detail-value {
            font-size: 1.05rem;
            font-weight: 600;
            word-break: break-word;
        }
        
        .location-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .coordinates {
            font-family: 'Courier New', monospace;
            background-color: #edf2f7;
            padding: 8px 12px;
            border-radius: 8px;
            margin: 10px 0;
            display: inline-block;
            font-size: 0.95rem;
        }
        
        .map-button {
            background-color: var(--button-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .map-button:hover {
            background-color: #2f855a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* أنماط جديدة لجدول القطارات */
        .trains-section {
            margin-top: 25px;
        }
        
        .trains-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .trains-table th {
            background-color: var(--train-color);
            color: white;
            padding: 12px;
            text-align: right;
        }
        
        .trains-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }
        
        .trains-table tr:last-child td {
            border-bottom: none;
        }
        
        .train-time {
            font-weight: 700;
            color: var(--train-color);
            font-size: 1.1rem;
        }
        
        .refresh-button {
            background-color: var(--train-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-top: 15px;
        }
        
        .refresh-button:hover {
            background-color: #3182ce;
            transform: translateY(-2px);
        }
        
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 1.2rem;
            color: var(--text-color);
            font-weight: 500;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            
            .station-header {
                padding: 20px 15px;
            }
            
            .station-header h1 {
                font-size: 1.5rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .meta-badge {
                font-size: 0.85rem;
                padding: 5px 12px;
            }
            
            .trains-table th, 
            .trains-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .train-time {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- شاشة التحميل -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-spinner"></div>
        <div class="loading-text">جاري تحميل البيانات...</div>
    </div>

    <!-- محتوى الصفحة -->
    <div class="app-content" style="display: none;">
        <header class="app-header">
            <h1 class="app-title">مرشدك</h1>
        </header>
        
        <div class="container">
            <div class="station-card">
                <div class="station-header">
                    <h1><?= htmlspecialchars($station['name']) ?></h1>
                    <div class="station-meta">
                        <div class="meta-badge">
                            <span>🚇</span>
                            <span><?= $station['transport_type'] ?></span>
                        </div>
                        <div class="meta-badge">
                            <span>🔢</span>
                            <span>ترتيب المحطة: <?= $station['stop_order'] ?></span>
                        </div>
                        <div class="meta-badge">
                            <span>🆔</span>
                            <span><?= $station_code ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="station-body">
                    <div class="detail-section">
                        <h2 class="section-title">
                            <span>📋</span>
                            معلومات المحطة
                        </h2>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <span>🚏</span>
                                    خط النقل
                                </div>
                                <div class="detail-value"><?= $station['line_code'] ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">
                                    <span>💰</span>
                                    سعر التذكرة
                                </div>
                                <div class="detail-value"><?= $station['fare'] ?> د.ج</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- قسم أوقات القطارات الجديد -->
                    <div class="detail-section trains-section">
                        <h2 class="section-title">
                            <span>🚆</span>
                            مواعيد القطارات القادمة
                        </h2>
                        <table class="trains-table">
                            <thead>
                                <tr>
                                    <th>القطار</th>
                                    <th>الوقت المتبقي</th>
                                </tr>
                            </thead>
                            <tbody id="trainsSchedule">
                                <!-- سيتم ملؤه بواسطة الجافاسكربت -->
                            </tbody>
                        </table>
                        <button class="refresh-button" onclick="generateRandomTimes()">
                            <span>🔄</span>
                            تحديث المواعيد
                        </button>
                    </div>
                    
                    <div class="detail-section">
                        <h2 class="section-title">
                            <span>📍</span>
                            الموقع الجغرافي
                        </h2>
                        <div class="location-container">
                            <div class="detail-label">
                                <span>🌐</span>
                                الإحداثيات
                            </div>
                            <div class="coordinates">
                                <?= number_format($station['latitude'], 6) ?> N, 
                                <?= number_format($station['longitude'], 6) ?> E
                            </div>
                            <button onclick="openMap(<?= $station['latitude'] ?>, <?= $station['longitude'] ?>)" 
                                    class="map-button">
                                <span>🗺️</span>
                                عرض على الخريطة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // إخفاء شاشة التحميل بعد تحميل الصفحة
    window.addEventListener('load', function() {
        setTimeout(function() {
            document.getElementById('loadingScreen').style.opacity = '0';
            document.querySelector('.app-content').style.display = 'block';
            
            setTimeout(function() {
                document.getElementById('loadingScreen').style.display = 'none';
                // توليد أوقات عشوائية للقطارات عند التحميل
                generateRandomTimes();
            }, 500);
        }, 1000);
    });
    
    function openMap(lat, lng) {
        // فتح في تطبيق الخرائط المناسب حسب الجهاز
        if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
            window.location.href = `geo:${lat},${lng}?q=${lat},${lng}`;
        } else {
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        }
    }
    
    // دالة لتوليد أوقات عشوائية للقطارات
    function generateRandomTimes() {
        const trainsSchedule = document.getElementById('trainsSchedule');
        trainsSchedule.innerHTML = '';
        
        // توليد وقت عشوائي بين 3 و 9 دقائق للقطار الأول
        const firstTrainTime = Math.floor(Math.random() * 7) + 3;
        // القطار الثاني بعد 6 دقائق من الأول
        const secondTrainTime = firstTrainTime + 6;
        
        // إضافة القطارات إلى الجدول
        const trains = [
            { name: 'القطار الأول', time: firstTrainTime },
            { name: 'القطار الثاني', time: secondTrainTime }
        ];
        
        trains.forEach(train => {
            const row = document.createElement('tr');
            
            const nameCell = document.createElement('td');
            nameCell.textContent = train.name;
            
            const timeCell = document.createElement('td');
            timeCell.innerHTML = `<span class="train-time">${train.time}</span> دقيقة`;
            
            row.appendChild(nameCell);
            row.appendChild(timeCell);
            trainsSchedule.appendChild(row);
        });
        
        // إضافة رسالة توضيحية
        const infoRow = document.createElement('tr');
        const infoCell = document.createElement('td');
        infoCell.colSpan = 2;
        infoCell.style.textAlign = 'center';
        infoCell.style.fontSize = '0.85rem';
        infoCell.style.color = 'var(--light-text)';
        infoCell.textContent = 'المواعيد يتم تحديثها كل دقيقة تلقائياً';
        infoRow.appendChild(infoCell);
        trainsSchedule.appendChild(infoRow);
    }
    
    // تحديث المواعيد كل دقيقة تلقائياً
    setInterval(generateRandomTimes, 60000);
    </script>
</body>
</html>