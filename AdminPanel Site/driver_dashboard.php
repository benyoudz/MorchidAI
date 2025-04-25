<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'driver') {
    header("Location: login.php");
    exit();
}

// جلب بيانات السائق
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT users.*, driver_details.* FROM users 
                       LEFT JOIN driver_details ON users.id = driver_details.user_id 
                       WHERE users.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة سائق الأجرة - مورشد وهران</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-right: 250px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .welcome-message {
            background-color: #0d6efd;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .driver-status {
            font-size: 1.2rem;
        }
        .status-active {
            color: #28a745;
        }
        .status-inactive {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>مورشد وهران</h4>
            <p>لوحة سائق الأجرة</p>
        </div>
        <a href="driver_dashboard.php"><i class="bi bi-speedometer2"></i> لوحة التحكم</a>
        <a href="driver_rides.php"><i class="bi bi-list-check"></i> طلبات الركوب</a>
        <a href="driver_location.php"><i class="bi bi-geo-alt"></i> تحديث الموقع</a>
        <a href="driver_earnings.php"><i class="bi bi-cash-stack"></i> الأرباح</a>
        <a href="driver_profile.php"><i class="bi bi-person"></i> الملف الشخصي</a>
        <a href="logout.php"><i class="bi bi-box-arrow-left"></i> تسجيل الخروج</a>
    </div>

    <div class="main-content">
        <div class="welcome-message">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>مرحباً بك، <?php echo htmlspecialchars($driver['first_name'] . ' ' . htmlspecialchars($driver['last_name'])); ?></h4>
                    <p class="mb-0">سائق سيارة أجرة</p>
                </div>
                <div class="driver-status">
                    <span class="status-active"><i class="bi bi-check-circle"></i> نشط</span>
                    <button class="btn btn-sm btn-outline-light ms-2">تغيير الحالة</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people"></i> طلبات اليوم</h5>
                        <h2 class="text-primary">7</h2>
                        <p class="card-text">زيادة 20% عن الأمس</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cash-coin"></i> أرباح اليوم</h5>
                        <h2 class="text-success">3,500 د.ج</h2>
                        <p class="card-text">زيادة 15% عن الأمس</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-star"></i> التقييم</h5>
                        <h2 class="text-warning">4.8 <small>/5</small></h2>
                        <p class="card-text">من 125 تقييم</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> آخر الطلبات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>العميل</th>
                                <th>التاريخ</th>
                                <th>النقاط</th>
                                <th>المسافة</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>أحمد بن يوسف</td>
                                <td>اليوم 10:45 ص</td>
                                <td>السانية ← المدينة الجديدة</td>
                                <td>8.5 كم</td>
                                <td>500 د.ج</td>
                                <td><span class="badge bg-success">مكتمل</span></td>
                            </tr>
                            <tr>
                                <td>ليلى العربي</td>
                                <td>اليوم 09:30 ص</td>
                                <td>الباهية ← جامعة العلوم</td>
                                <td>6.2 كم</td>
                                <td>400 د.ج</td>
                                <td><span class="badge bg-success">مكتمل</span></td>
                            </tr>
                            <tr>
                                <td>محمد زكرياء</td>
                                <td>اليوم 08:15 ص</td>
                                <td>حي السلام ← إيسطو</td>
                                <td>12.3 كم</td>
                                <td>800 د.ج</td>
                                <td><span class="badge bg-warning">قيد التنفيذ</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-map"></i> الخريطة الحية</h5>
                <button class="btn btn-sm btn-primary">تحديث الموقع</button>
            </div>
            <div class="card-body">
                <div style="height: 300px; background-color: #eee; border-radius: 8px; display: flex; justify-content: center; align-items: center;">
                    <p class="text-muted">خريطة تفاعلية تظهر موقعك وطلبات الركوب القريبة</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
