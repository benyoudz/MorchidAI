<?php include 'db_configpdo.php'; 

// بداية الجلسة
session_start();

// التحقق من تسجيل الدخول
/*if(!isset($_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}
*/

// معالجة عمليات الإضافة والتعديل لتفاصيل السائقين
if(isset($_POST['save_driver_details'])) {
  $id = $_POST['id'] ?? null;
  $data = [
      'user_id' => $_POST['user_id'],
      'residence' => $_POST['residence'],
      'safety_certificate_number' => $_POST['safety_certificate_number'],
      'certificate_expiry' => $_POST['certificate_expiry'],
      'car_color' => $_POST['car_color'],
      'car_acquisition_date' => $_POST['car_acquisition_date'],
      'car_expiry_date' => $_POST['car_expiry_date']
  ];
  
  // معالجة رفع الملفات
  $uploadDir = 'uploads/drivers/';
  
  if(!empty($_FILES['safety_certificate_image']['name'])) {
      $certImage = basename($_FILES['safety_certificate_image']['name']);
      $targetFile = $uploadDir . uniqid() . '_' . $certImage;
      if(move_uploaded_file($_FILES['safety_certificate_image']['tmp_name'], $targetFile)) {
          $data['safety_certificate_image'] = $targetFile;
      }
  }
  
  if(!empty($_FILES['car_document']['name'])) {
      $carDoc = basename($_FILES['car_document']['name']);
      $targetFile = $uploadDir . uniqid() . '_' . $carDoc;
      if(move_uploaded_file($_FILES['car_document']['tmp_name'], $targetFile)) {
          $data['car_document'] = $targetFile;
      }
  }
  
  try {
      if($id) {
          // عملية التعديل
          $sql = "UPDATE driver_details SET 
                 user_id = :user_id, 
                 residence = :residence, 
                 safety_certificate_number = :safety_certificate_number, ";
          
          if(isset($data['safety_certificate_image'])) {
              $sql .= "safety_certificate_image = :safety_certificate_image, ";
          }
          
          $sql .= "certificate_expiry = :certificate_expiry, 
                 car_color = :car_color, 
                 car_acquisition_date = :car_acquisition_date, ";
          
          if(isset($data['car_document'])) {
              $sql .= "car_document = :car_document, ";
          }
          
          $sql .= "car_expiry_date = :car_expiry_date 
                 WHERE id = $id";
          
          $stmt = $pdo->prepare($sql);
          $success_msg = "تم تعديل تفاصيل السائق بنجاح";
      } else {
          // عملية الإضافة
          $stmt = $pdo->prepare("INSERT INTO driver_details 
                               (user_id, residence, safety_certificate_number, safety_certificate_image, 
                                certificate_expiry, car_color, car_acquisition_date, car_expiry_date, car_document) 
                               VALUES 
                               (:user_id, :residence, :safety_certificate_number, :safety_certificate_image, 
                                :certificate_expiry, :car_color, :car_acquisition_date, :car_expiry_date, :car_document)");
          $success_msg = "تم إضافة تفاصيل السائق بنجاح";
      }
      $stmt->execute($data);
  } catch(PDOException $e) {
      $error_msg = "خطأ في العملية: " . $e->getMessage();
  }
}
if(isset($_POST['save_user'])) {
  $id = $_POST['id'] ?? null;
  $data = [
      'first_name' => $_POST['first_name'],
      'last_name' => $_POST['last_name'],
      'phone' => $_POST['phone'],
      'user_type' => $_POST['user_type']
  ];
  
  // إذا كانت كلمة السر غير فارغة، نقوم بتحديثها
  if(!empty($_POST['password'])) {
      $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
  }
  
  try {
      if($id) {
          // عملية التعديل
          if(isset($data['password'])) {
              $stmt = $pdo->prepare("UPDATE users SET 
                                   first_name = :first_name, 
                                   last_name = :last_name, 
                                   phone = :phone, 
                                   user_type = :user_type,
                                   password = :password 
                                   WHERE id = $id");
          } else {
              $stmt = $pdo->prepare("UPDATE users SET 
                                   first_name = :first_name, 
                                   last_name = :last_name, 
                                   phone = :phone, 
                                   user_type = :user_type 
                                   WHERE id = $id");
          }
          $success_msg = "تم تعديل المستخدم بنجاح";
      } else {
          // عملية الإضافة
          $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
          $stmt = $pdo->prepare("INSERT INTO users 
                               (first_name, last_name, phone, password, user_type) 
                               VALUES 
                               (:first_name, :last_name, :phone, :password, :user_type)");
          $success_msg = "تم إضافة المستخدم بنجاح";
      }
      $stmt->execute($data);
  } catch(PDOException $e) {
      $error_msg = "خطأ في العملية: " . $e->getMessage();
  }
}

// معالجة عمليات الإضافة والتعديل للتقييمات
if(isset($_POST['save_rating'])) {
  $id = $_POST['id'] ?? null;
  $data = [
      'user_id' => $_POST['user_id'],
      'driver_id' => $_POST['driver_id'],
      'rating' => $_POST['rating'],
      'comment' => $_POST['comment']
  ];
  
  try {
      if($id) {
          // عملية التعديل
          $stmt = $pdo->prepare("UPDATE ratings SET 
                               user_id = :user_id, 
                               driver_id = :driver_id, 
                               rating = :rating, 
                               comment = :comment 
                               WHERE id = $id");
          $success_msg = "تم تعديل التقييم بنجاح";
      } else {
          // عملية الإضافة
          $stmt = $pdo->prepare("INSERT INTO ratings 
                               (user_id, driver_id, rating, comment) 
                               VALUES 
                               (:user_id, :driver_id, :rating, :comment)");
          $success_msg = "تم إضافة التقييم بنجاح";
      }
      $stmt->execute($data);
  } catch(PDOException $e) {
      $error_msg = "خطأ في العملية: " . $e->getMessage();
  }
}


if(isset($_POST['delete_item'])) {
    $table = $_POST['table'];
    $id = $_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = "تم الحذف بنجاح";
    } catch(PDOException $e) {
        $error_msg = "خطأ في الحذف: " . $e->getMessage();
    }
}

// معالجة عمليات الإضافة والتعديل للخطوط
if(isset($_POST['save_line'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        'line_name' => $_POST['line_name'],
        'transport_type' => $_POST['transport_type'],
        'start_name' => $_POST['start_name'],
        'end_name' => $_POST['end_name'],
        'fare' => $_POST['fare']
    ];
    
    try {
        if($id) {
            // عملية التعديل
            $stmt = $pdo->prepare("UPDATE bus_lines SET 
                                 line_name = :line_name, 
                                 transport_type = :transport_type, 
                                 start_name = :start_name, 
                                 end_name = :end_name, 
                                 fare = :fare 
                                 WHERE id = $id");
            $success_msg = "تم تعديل الخط بنجاح";
        } else {
            // عملية الإضافة
            $stmt = $pdo->prepare("INSERT INTO bus_lines 
                                 (line_name, transport_type, start_name, end_name, fare) 
                                 VALUES 
                                 (:line_name, :transport_type, :start_name, :end_name, :fare)");
            $success_msg = "تم إضافة الخط بنجاح";
        }
        $stmt->execute($data);
    } catch(PDOException $e) {
        $error_msg = "خطأ في العملية: " . $e->getMessage();
    }
}

// معالجة عمليات الإضافة والتعديل للطرق
if(isset($_POST['save_road'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        'start_point' => $_POST['start_point'],
        'end_point' => $_POST['end_point'],
        'road_type' => $_POST['road_type'],
        'distance_km' => $_POST['distance_km']
    ];
    
    try {
        if($id) {
            $stmt = $pdo->prepare("UPDATE road_network SET 
                                 start_point = :start_point, 
                                 end_point = :end_point, 
                                 road_type = :road_type, 
                                 distance_km = :distance_km 
                                 WHERE id = $id");
            $success_msg = "تم تعديل الطريق بنجاح";
        } else {
            $stmt = $pdo->prepare("INSERT INTO road_network 
                                 (start_point, end_point, road_type, distance_km) 
                                 VALUES 
                                 (:start_point, :end_point, :road_type, :distance_km)");
            $success_msg = "تم إضافة الطريق بنجاح";
        }
        $stmt->execute($data);
    } catch(PDOException $e) {
        $error_msg = "خطأ في العملية: " . $e->getMessage();
    }
}
if(isset($_POST['save_taxi'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        'driver_name' => $_POST['driver_name'],
        'car_color' => $_POST['car_color'],
        'car_number' => $_POST['car_number'],
        'phone_number' => $_POST['phone_number'],
        'current_lat' => $_POST['current_lat'],
        'current_lng' => $_POST['current_lng']
    ];
    try {
        if($id) {
            $stmt = $pdo->prepare("UPDATE taxis SET 
                                 driver_name = :driver_name, 
                                 car_color = :car_color, 
                                 car_number = :car_number, 
                                 phone_number = :phone_number, 
                                 current_lat = :current_lat, 
                                 current_lng = :current_lng 
                                 WHERE id = $id");
            $success_msg = "تم تعديل بيانات الطاكسي بنجاح";
        } else {
            $stmt = $pdo->prepare("INSERT INTO taxis 
                                 (driver_name, car_color, car_number, phone_number, current_lat, current_lng) 
                                 VALUES 
                                 (:driver_name, :car_color, :car_number, :phone_number, :current_lat, :current_lng)");
            $success_msg = "تم إضافة طاكسي جديد بنجاح";
        }
        $stmt->execute($data);
    } catch(PDOException $e) {
        $error_msg = "خطأ في العملية: " . $e->getMessage();
    }
}

// معالجة عمليات الإضافة والتعديل للمحطات
if(isset($_POST['save_node'])) {
    $id = $_POST['id'] ?? null;
    $data = [
        'name' => $_POST['name'],
        'line_code' => $_POST['line_code'],
        'transport_type' => $_POST['transport_type'],
        'fare' => $_POST['fare'],
        'stop_order' => $_POST['stop_order']
    ];
    
    try {
        if($id) {
            $stmt = $pdo->prepare("UPDATE transport_nodes SET 
                                 name = :name, 
                                 line_code = :line_code, 
                                 transport_type = :transport_type, 
                                 fare = :fare, 
                                 stop_order = :stop_order 
                                 WHERE id = $id");
            $success_msg = "تم تعديل المحطة بنجاح";
        } else {
            $stmt = $pdo->prepare("INSERT INTO transport_nodes 
                                 (name, line_code, transport_type, fare, stop_order) 
                                 VALUES 
                                 (:name, :line_code, :transport_type, :fare, :stop_order)");
            $success_msg = "تم إضافة محطة جديدة بنجاح";
        }
        $stmt->execute($data);
    } catch(PDOException $e) {
        $error_msg = "خطأ في العملية: " . $e->getMessage();
    }
}

// جلب بيانات العنصر للتعديل
$edit_item = null;
if(isset($_GET['edit'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $edit_item = $stmt->fetch();
    } catch(PDOException $e) {
        $error_msg = "خطأ في جلب البيانات: " . $e->getMessage();
    }
}
// دالة للتحقق من وثائق السائقين
function checkDriverDocuments() {
  global $pdo;
  
  // تاريخ اليوم
  $today = new DateTime();
  $todayStr = $today->format('Y-m-d');
  
  // تحقق من الوثائق المنتهية أو التي ستنتهي خلال 30 يوم
  $stmt = $pdo->query("
      SELECT u.first_name, u.last_name, u.phone, d.* 
      FROM driver_details d
      JOIN users u ON d.user_id = u.id
      WHERE 
          DATEDIFF(d.certificate_expiry, '$todayStr') <= 30 OR
          DATEDIFF(d.car_expiry_date, '$todayStr') <= 30
  ");
  
  $expiredDocs = [];
  while ($driver = $stmt->fetch()) {
      $messages = [];
      
      // التحقق من رخصة السلامة المهنية
      $certExpiry = new DateTime($driver['certificate_expiry']);
      $daysLeft = $today->diff($certExpiry)->days;
      
      if ($certExpiry < $today) {
          $messages[] = "انتهت صلاحية رخصة السلامة المهنية للسائق {$driver['first_name']} {$driver['last_name']} (هاتف: {$driver['phone']})";
      } elseif ($daysLeft <= 30) {
          $messages[] = "رخصة السلامة المهنية للسائق {$driver['first_name']} {$driver['last_name']} ستنتهي خلال $daysLeft يوم";
      }
      
      // التحقق من وثيقة السيارة
      $carExpiry = new DateTime($driver['car_expiry_date']);
      $daysLeft = $today->diff($carExpiry)->days;
      
      if ($carExpiry < $today) {
          $messages[] = "انتهت صلاحية وثيقة السيارة للسائق {$driver['first_name']} {$driver['last_name']} (هاتف: {$driver['phone']})";
      } elseif ($daysLeft <= 30) {
          $messages[] = "وثيقة السيارة للسائق {$driver['first_name']} {$driver['last_name']} ستنتهي خلال $daysLeft يوم";
      }
      
      if (!empty($messages)) {
          $expiredDocs[$driver['user_id']] = [
              'driver' => $driver,
              'messages' => $messages
          ];
      }
  }
  
  // إرسال الإشعارات
  foreach ($expiredDocs as $doc) {
      foreach ($doc['messages'] as $message) {
          sendTelegramNotification($message);
          
          // يمكنك أيضاً تسجيل الإشعار في قاعدة البيانات إذا لزم الأمر
          $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
          $stmt->execute([$doc['driver']['user_id'], $message]);
      }
  }
  
  return $expiredDocs;
}

// تفعيل التحقق عند تحميل الصفحة
$expiredDocuments = checkDriverDocuments();

?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>لوحة الإدارة - مرشد</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
  <style>
    .modal-rtl .modal-header, .modal-rtl .modal-footer {
      flex-direction: row-reverse;
    }
    .action-btns {
      white-space: nowrap;
    }
  </style>
</head>
<body dir="rtl" class="bg-light">
<li class="nav-item">
  <a class="nav-link" href="#notifications" data-bs-toggle="tab">
    الإشعارات
    <?php if(!empty($expiredDocuments)): ?>
      <span class="badge bg-danger"><?= count($expiredDocuments) ?></span>
    <?php endif; ?>
  </a>
</li>
<div class="tab-pane fade" id="notifications">
  <div class="d-flex justify-content-between mb-3">
    <h4>إشعارات انتهاء الوثائق</h4>
    <button class="btn btn-primary" onclick="location.reload()">
      <i class="bi bi-arrow-clockwise"></i> تحديث
    </button>
  </div>
  
  <?php if(empty($expiredDocuments)): ?>
    <div class="alert alert-success">لا توجد وثائق منتهية الصلاحية أو قريبة من الانتهاء</div>
  <?php else: ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle-fill"></i> يوجد <?= count($expiredDocuments) ?> سائق بحاجة إلى متابعة
    </div>
    
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>السائق</th>
          <th>الهاتف</th>
          <th>الإشعارات</th>
          <th>تحكم</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($expiredDocuments as $doc): ?>
          <tr class="table-warning">
            <td><?= $doc['driver']['first_name'] ?> <?= $doc['driver']['last_name'] ?></td>
            <td><?= $doc['driver']['phone'] ?></td>
            <td>
              <ul>
                <?php foreach($doc['messages'] as $message): ?>
                  <li><?= $message ?></li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td>
              <a href="?edit=1&table=driver_details&id=<?= $doc['driver']['id'] ?>" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> تعديل
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
  <div class="container my-4">
    <?php if(isset($success_msg)): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?= $success_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <?php if(isset($error_msg)): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?= $error_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <h1 class="text-center mb-4">لوحة التحكم الإدارية</h1>

    <ul class="nav nav-tabs mb-4">
      <li class="nav-item"><a class="nav-link active" href="#bus_lines" data-bs-toggle="tab">الخطوط</a></li>
      <li class="nav-item"><a class="nav-link" href="#road_network" data-bs-toggle="tab">الطرقات</a></li>
      <li class="nav-item"><a class="nav-link" href="#taxis" data-bs-toggle="tab">الطاكسيات</a></li>
      <li class="nav-item"><a class="nav-link" href="#transport_nodes" data-bs-toggle="tab">محطات النقل</a></li>
      <li class="nav-item"><a class="nav-link" href="#users" data-bs-toggle="tab">المستخدمين</a></li>
      <li class="nav-item"><a class="nav-link" href="#ratings" data-bs-toggle="tab">التقييمات</a></li>
      <li class="nav-item"><a class="nav-link" href="#driver_details" data-bs-toggle="tab">تفاصيل السائقين</a></li>
    </ul>

    <div class="tab-content">
      <!-- الخطوط -->
      <div class="tab-pane fade show active" id="bus_lines">
        <div class="d-flex justify-content-between mb-3">
          <h4>قائمة خطوط النقل</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lineModal">
            إضافة خط جديد
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>الخط</th>
              <th>النوع</th>
              <th>من</th>
              <th>إلى</th>
              <th>السعر</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT * FROM bus_lines");
              while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['line_name']}</td>
                        <td>{$row['transport_type']}</td>
                        <td>{$row['start_name']}</td>
                        <td>{$row['end_name']}</td>
                        <td>{$row['fare']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=bus_lines&id={$row['id']}#bus_lines' class='btn btn-sm btn-warning'>تعديل</a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='bus_lines'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من الحذف؟\")'>حذف</button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>

      <!-- الطرقات -->
      <div class="tab-pane fade" id="road_network">
        <div class="d-flex justify-content-between mb-3">
          <h4>شبكة الطرق</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roadModal">
            إضافة طريق جديد
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>من</th>
              <th>إلى</th>
              <th>النوع</th>
              <th>المسافة (كم)</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT * FROM road_network");
              while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['start_point']}</td>
                        <td>{$row['end_point']}</td>
                        <td>{$row['road_type']}</td>
                        <td>{$row['distance_km']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=road_network&id={$row['id']}#road_network' class='btn btn-sm btn-warning'>تعديل</a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='road_network'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من الحذف؟\")'>حذف</button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>

      <!-- الطاكسيات -->
      <div class="tab-pane fade" id="taxis">
        <div class="d-flex justify-content-between mb-3">
          <h4>الطاكسيات النشيطة</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taxiModal">
            إضافة طاكسي جديد
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>السائق</th>
              <th>السيارة</th>
              <th>الهاتف</th>
              <th>الموقع الحالي</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT * FROM taxis");
              while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['driver_name']}</td>
                        <td>{$row['car_color']} - {$row['car_number']}</td>
                        <td>{$row['phone_number']}</td>
                        <td>{$row['current_lat']}, {$row['current_lng']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=taxis&id={$row['id']}#taxis' class='btn btn-sm btn-warning'>تعديل</a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='taxis'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من الحذف؟\")'>حذف</button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>

      <!-- المحطات -->
      <div class="tab-pane fade" id="transport_nodes">
        <div class="d-flex justify-content-between mb-3">
          <h4>نقاط التوقف</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nodeModal">
            إضافة محطة جديدة
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>الاسم</th>
              <th>خط</th>
              <th>النوع</th>
              <th>السعر</th>
              <th>ترتيب المحطة</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT * FROM transport_nodes ORDER BY line_code, stop_order");
              while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['line_code']}</td>
                        <td>{$row['transport_type']}</td>
                        <td>{$row['fare']}</td>
                        <td>{$row['stop_order']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=transport_nodes&id={$row['id']}#transport_nodes' class='btn btn-sm btn-warning'>تعديل</a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='transport_nodes'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من الحذف؟\")'>حذف</button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal لإضافة/تعديل الخطوط -->
  <div class="modal fade" id="lineModal" tabindex="-1" aria-labelledby="lineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="lineModalLabel"><?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? 'تعديل الخط' : 'إضافة خط جديد' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? $edit_item['id'] : '' ?>">
            <div class="mb-3">
              <label class="form-label">اسم الخط</label>
              <input type="text" class="form-control" name="line_name" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? $edit_item['line_name'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">نوع النقل</label>
              <select class="form-select" name="transport_type" required>
                <option value="حافلة" <?= (isset($edit_item) && $_GET['table'] == 'bus_lines' && $edit_item['transport_type'] == 'حافلة') ? 'selected' : '' ?>>حافلة</option>
                <option value="ميكروباص" <?= (isset($edit_item) && $_GET['table'] == 'bus_lines' && $edit_item['transport_type'] == 'ميكروباص') ? 'selected' : '' ?>>ميكروباص</option>
                <option value="ترامواي" <?= (isset($edit_item) && $_GET['table'] == 'bus_lines' && $edit_item['transport_type'] == 'ترامواي') ? 'selected' : '' ?>>ترامواي</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">نقطة البداية</label>
              <input type="text" class="form-control" name="start_name" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? $edit_item['start_name'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">نقطة النهاية</label>
              <input type="text" class="form-control" name="end_name" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? $edit_item['end_name'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">السعر (دج)</label>
              <input type="number" class="form-control" name="fare" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'bus_lines' ? $edit_item['fare'] : '' ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_line" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal لإضافة/تعديل الطرق -->
  <div class="modal fade" id="roadModal" tabindex="-1" aria-labelledby="roadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roadModalLabel"><?= isset($edit_item) && $_GET['table'] == 'road_network' ? 'تعديل الطريق' : 'إضافة طريق جديد' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'road_network' ? $edit_item['id'] : '' ?>">
            <div class="mb-3">
              <label class="form-label">نقطة البداية</label>
              <input type="text" class="form-control" name="start_point" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'road_network' ? $edit_item['start_point'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">نقطة النهاية</label>
              <input type="text" class="form-control" name="end_point" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'road_network' ? $edit_item['end_point'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">نوع الطريق</label>
              <select class="form-select" name="road_type" required>
                <option value="رئيسي" <?= (isset($edit_item) && $_GET['table'] == 'road_network' && $edit_item['road_type'] == 'رئيسي') ? 'selected' : '' ?>>رئيسي</option>
                <option value="ثانوي" <?= (isset($edit_item) && $_GET['table'] == 'road_network' && $edit_item['road_type'] == 'ثانوي') ? 'selected' : '' ?>>ثانوي</option>
                <option value="سريع" <?= (isset($edit_item) && $_GET['table'] == 'road_network' && $edit_item['road_type'] == 'سريع') ? 'selected' : '' ?>>سريع</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">المسافة (كم)</label>
              <input type="number" step="0.1" class="form-control" name="distance_km" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'road_network' ? $edit_item['distance_km'] : '' ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_road" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal لإضافة/تعديل الطاكسيات -->
  <div class="modal fade" id="taxiModal" tabindex="-1" aria-labelledby="taxiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="taxiModalLabel"><?= isset($edit_item) && $_GET['table'] == 'taxis' ? 'تعديل بيانات الطاكسي' : 'إضافة طاكسي جديد' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['id'] : '' ?>">
            <div class="mb-3">
              <label class="form-label">اسم السائق</label>
              <input type="text" class="form-control" name="driver_name" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['driver_name'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">لون السيارة</label>
              <input type="text" class="form-control" name="car_color" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['car_color'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">رقم السيارة</label>
              <input type="text" class="form-control" name="car_number" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['car_number'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">رقم الهاتف</label>
              <input type="text" class="form-control" name="phone_number" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['phone_number'] : '' ?>">
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">خط العرض</label>
                <input type="text" class="form-control" name="current_lat" required 
                       value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['current_lat'] : '' ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">خط الطول</label>
                <input type="text" class="form-control" name="current_lng" required 
                       value="<?= isset($edit_item) && $_GET['table'] == 'taxis' ? $edit_item['current_lng'] : '' ?>">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_taxi" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal لإضافة/تعديل المحطات -->
  <div class="modal fade" id="nodeModal" tabindex="-1" aria-labelledby="nodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="nodeModalLabel"><?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? 'تعديل المحطة' : 'إضافة محطة جديدة' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? $edit_item['id'] : '' ?>">
            <div class="mb-3">
              <label class="form-label">اسم المحطة</label>
              <input type="text" class="form-control" name="name" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? $edit_item['name'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">رمز الخط</label>
              <input type="text" class="form-control" name="line_code" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? $edit_item['line_code'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">نوع النقل</label>
              <select class="form-select" name="transport_type" required>
                <option value="حافلة" <?= (isset($edit_item) && $_GET['table'] == 'transport_nodes' && $edit_item['transport_type'] == 'حافلة') ? 'selected' : '' ?>>حافلة</option>
                <option value="ميكروباص" <?= (isset($edit_item) && $_GET['table'] == 'transport_nodes' && $edit_item['transport_type'] == 'ميكروباص') ? 'selected' : '' ?>>ميكروباص</option>
                <option value="ترامواي" <?= (isset($edit_item) && $_GET['table'] == 'transport_nodes' && $edit_item['transport_type'] == 'ترامواي') ? 'selected' : '' ?>>ترامواي</option>
                <option value="طاكسي" <?= (isset($edit_item) && $_GET['table'] == 'transport_nodes' && $edit_item['transport_type'] == 'طاكسي') ? 'selected' : '' ?>>طاكسي</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">السعر (دج)</label>
              <input type="number" class="form-control" name="fare" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? $edit_item['fare'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">ترتيب المحطة</label>
              <input type="number" class="form-control" name="stop_order" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'transport_nodes' ? $edit_item['stop_order'] : '' ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_node" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <div class="tab-pane fade" id="users">
        <div class="d-flex justify-content-between mb-3">
          <h4>إدارة المستخدمين</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bi bi-plus-lg"></i> إضافة مستخدم
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>الاسم</th>
              <th>الهاتف</th>
              <th>النوع</th>
              <th>تاريخ التسجيل</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
              while ($row = $stmt->fetch()) {
                echo "<tr>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['user_type']}</td>
                        <td>{$row['created_at']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=users&id={$row['id']}#users' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='users'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من حذف المستخدم؟\")'><i class='bi bi-trash'></i></button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>

      <!-- قسم التقييمات -->
      <div class="tab-pane fade" id="ratings">
        <div class="d-flex justify-content-between mb-3">
          <h4>إدارة التقييمات</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ratingModal">
            <i class="bi bi-plus-lg"></i> إضافة تقييم
          </button>
        </div>
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>المستخدم</th>
              <th>السائق</th>
              <th>التقييم</th>
              <th>التعليق</th>
              <th>التاريخ</th>
              <th>تحكم</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $pdo->query("SELECT r.*, 
                                  u.first_name as user_name, 
                                  d.first_name as driver_name 
                                  FROM ratings r
                                  JOIN users u ON r.user_id = u.id
                                  JOIN users d ON r.driver_id = d.id
                                  ORDER BY r.created_at DESC");
              while ($row = $stmt->fetch()) {
                $stars = str_repeat('<i class="bi bi-star-fill star-rating"></i>', $row['rating']);
                echo "<tr>
                        <td>{$row['user_name']}</td>
                        <td>{$row['driver_name']}</td>
                        <td>{$stars}</td>
                        <td>" . substr($row['comment'], 0, 50) . "...</td>
                        <td>{$row['created_at']}</td>
                        <td class='action-btns'>
                          <a href='?edit=1&table=ratings&id={$row['id']}#ratings' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                          <form method='post' style='display:inline'>
                            <input type='hidden' name='table' value='ratings'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من حذف التقييم؟\")'><i class='bi bi-trash'></i></button>
                          </form>
                        </td>
                      </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel"><?= isset($edit_item) && $_GET['table'] == 'users' ? 'تعديل المستخدم' : 'إضافة مستخدم جديد' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'users' ? $edit_item['id'] : '' ?>">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">الاسم الأول</label>
                <input type="text" class="form-control" name="first_name" required 
                       value="<?= isset($edit_item) && $_GET['table'] == 'users' ? $edit_item['first_name'] : '' ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">الاسم الأخير</label>
                <input type="text" class="form-control" name="last_name" required 
                       value="<?= isset($edit_item) && $_GET['table'] == 'users' ? $edit_item['last_name'] : '' ?>">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">رقم الهاتف</label>
              <input type="text" class="form-control" name="phone" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'users' ? $edit_item['phone'] : '' ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">كلمة المرور</label>
              <input type="password" class="form-control" name="password" <?= !isset($edit_item) ? 'required' : '' ?>>
              <?php if(isset($edit_item) && $_GET['table'] == 'users'): ?>
                <small class="text-muted">اترك الحقل فارغاً إذا كنت لا تريد تغيير كلمة المرور</small>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label class="form-label">نوع المستخدم</label>
              <select class="form-select" name="user_type" required>
                <option value="person" <?= (isset($edit_item) && $_GET['table'] == 'users' && $edit_item['user_type'] == 'person') ? 'selected' : '' ?>>شخص عادي</option>
                <option value="driver" <?= (isset($edit_item) && $_GET['table'] == 'users' && $edit_item['user_type'] == 'driver') ? 'selected' : '' ?>>سائق</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_user" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal لإضافة/تعديل التقييمات -->
  <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-rtl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ratingModalLabel"><?= isset($edit_item) && $_GET['table'] == 'ratings' ? 'تعديل التقييم' : 'إضافة تقييم جديد' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'ratings' ? $edit_item['id'] : '' ?>">
            <div class="mb-3">
              <label class="form-label">المستخدم</label>
              <select class="form-select" name="user_id" required>
                <?php
                  $users = $pdo->query("SELECT * FROM users WHERE user_type = 'person'");
                  while ($user = $users->fetch()) {
                    $selected = (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['user_id'] == $user['id']) ? 'selected' : '';
                    echo "<option value='{$user['id']}' $selected>{$user['first_name']} {$user['last_name']}</option>";
                  }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">السائق</label>
              <select class="form-select" name="driver_id" required>
                <?php
                  $drivers = $pdo->query("SELECT * FROM users WHERE user_type = 'driver'");
                  while ($driver = $drivers->fetch()) {
                    $selected = (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['driver_id'] == $driver['id']) ? 'selected' : '';
                    echo "<option value='{$driver['id']}' $selected>{$driver['first_name']} {$driver['last_name']}</option>";
                  }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">التقييم</label>
              <select class="form-select" name="rating" required>
                <option value="1" <?= (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['rating'] == 1) ? 'selected' : '' ?>★</option>
                <option value="2" <?= (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['rating'] == 2) ? 'selected' : '' ?>>★★</option>
                <option value="3" <?= (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['rating'] == 3) ? 'selected' : '' ?>>★★★</option>
                <option value="4" <?= (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['rating'] == 4) ? 'selected' : '' ?>>★★★★</option>
                <option value="5" <?= (isset($edit_item) && $_GET['table'] == 'ratings' && $edit_item['rating'] == 5) ? 'selected' : '' ?>>★★★★★</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">التعليق</label>
              <textarea class="form-control" name="comment" rows="3"><?= isset($edit_item) && $_GET['table'] == 'ratings' ? $edit_item['comment'] : '' ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" name="save_rating" class="btn btn-primary">حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="driver_details">
  <div class="d-flex justify-content-between mb-3">
    <h4>تفاصيل السائقين</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#driverDetailModal">
      <i class="bi bi-plus-lg"></i> إضافة تفاصيل
    </button>
  </div>
  
  <table class="table table-bordered table-hover">
    <thead class="table-dark">
      <tr>
        <th>السائق</th>
        <th>رخصة السلامة</th>
        <th>انتهاء الرخصة</th>
        <th>وثيقة السيارة</th>
        <th>انتهاء الوثيقة</th>
        <th>تحكم</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $stmt = $pdo->query("
          SELECT d.*, u.first_name, u.last_name 
          FROM driver_details d
          JOIN users u ON d.user_id = u.id
          ORDER BY d.certificate_expiry ASC
        ");
        
        while ($row = $stmt->fetch()) {
          $certClass = (strtotime($row['certificate_expiry']) < strtotime('+30 days')) ? 'text-danger' : '';
          $carClass = (strtotime($row['car_expiry_date']) < strtotime('+30 days')) ? 'text-danger' : '';
          
          echo "<tr>
                  <td>{$row['first_name']} {$row['last_name']}</td>
                  <td>{$row['safety_certificate_number']}</td>
                  <td class='$certClass'>{$row['certificate_expiry']}</td>
                  <td>{$row['car_color']} - {$row['car_acquisition_date']}</td>
                  <td class='$carClass'>{$row['car_expiry_date']}</td>
                  <td class='action-btns'>
                    <a href='?edit=1&table=driver_details&id={$row['id']}#driver_details' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                    <form method='post' style='display:inline'>
                      <input type='hidden' name='table' value='driver_details'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <button type='submit' name='delete_item' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من الحذف؟\")'><i class='bi bi-trash'></i></button>
                    </form>
                  </td>
                </tr>";
        }
      ?>
    </tbody>
  </table>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ... السكريبت السابق يبقى كما هو مع إضافة ...
    
    // فتح المودال المناسب عند الضغط على تعديل
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const edit = urlParams.get('edit');
      const table = urlParams.get('table');
      
      if(edit && table) {
        let modalId = '';
        switch(table) {
          case 'bus_lines': modalId = 'lineModal'; break;
          case 'road_network': modalId = 'roadModal'; break;
          case 'taxis': modalId = 'taxiModal'; break;
          case 'transport_nodes': modalId = 'nodeModal'; break;
          case 'users': modalId = 'userModal'; break;
          case 'ratings': modalId = 'ratingModal'; break;
        }
        
        if(modalId) {
          const modal = new bootstrap.Modal(document.getElementById(modalId));
          modal.show();
        }
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // فتح المودال المناسب عند الضغط على تعديل
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const edit = urlParams.get('edit');
      const table = urlParams.get('table');
      
      if(edit && table) {
        let modalId = '';
        switch(table) {
          case 'bus_lines': modalId = 'lineModal'; break;
          case 'road_network': modalId = 'roadModal'; break;
          case 'taxis': modalId = 'taxiModal'; break;
          case 'transport_nodes': modalId = 'nodeModal'; break;
          case 'driver_details': modalId = 'driverDetailModal'; break;
        }
        
        if(modalId) {
          const modal = new bootstrap.Modal(document.getElementById(modalId));
          modal.show();
        }
      }
    });
  </script>
  <!-- Modal لإضافة/تعديل تفاصيل السائق -->
<div class="modal fade" id="driverDetailModal" tabindex="-1" aria-labelledby="driverDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-rtl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="driverDetailModalLabel"><?= isset($edit_item) && $_GET['table'] == 'driver_details' ? 'تعديل تفاصيل السائق' : 'إضافة تفاصيل سائق' ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['id'] : '' ?>">
          
          <div class="mb-3">
            <label class="form-label">السائق</label>
            <select class="form-select" name="user_id" required <?= isset($edit_item) && $_GET['table'] == 'driver_details' ? 'disabled' : '' ?>>
              <option value="">اختر سائقاً</option>
              <?php
                $drivers = $pdo->query("SELECT * FROM users WHERE user_type = 'driver'");
                while ($driver = $drivers->fetch()) {
                  $selected = (isset($edit_item) && $_GET['table'] == 'driver_details' && $edit_item['user_id'] == $driver['id']) ? 'selected' : '';
                  echo "<option value='{$driver['id']}' $selected>{$driver['first_name']} {$driver['last_name']}</option>";
                }
              ?>
            </select>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">مكان الإقامة</label>
              <input type="text" class="form-control" name="residence" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['residence'] : '' ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">رقم رخصة السلامة المهنية</label>
              <input type="text" class="form-control" name="safety_certificate_number" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['safety_certificate_number'] : '' ?>">
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">صورة رخصة السلامة</label>
              <input type="file" class="form-control" name="safety_certificate_image" <?= !isset($edit_item) ? 'required' : '' ?>>
              <?php if(isset($edit_item) && $_GET['table'] == 'driver_details' && !empty($edit_item['safety_certificate_image'])): ?>
                <small class="text-muted">الصورة الحالية: <?= $edit_item['safety_certificate_image'] ?></small>
              <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">انتهاء الرخصة</label>
              <input type="date" class="form-control" name="certificate_expiry" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['certificate_expiry'] : '' ?>">
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">لون السيارة</label>
              <input type="text" class="form-control" name="car_color" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['car_color'] : '' ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">تاريخ استلام السيارة</label>
              <input type="date" class="form-control" name="car_acquisition_date" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['car_acquisition_date'] : '' ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">انتهاء وثيقة السيارة</label>
              <input type="date" class="form-control" name="car_expiry_date" required 
                     value="<?= isset($edit_item) && $_GET['table'] == 'driver_details' ? $edit_item['car_expiry_date'] : '' ?>">
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">وثيقة السيارة</label>
            <input type="file" class="form-control" name="car_document" <?= !isset($edit_item) ? 'required' : '' ?>>
            <?php if(isset($edit_item) && $_GET['table'] == 'driver_details' && !empty($edit_item['car_document'])): ?>
              <small class="text-muted">الوثيقة الحالية: <?= $edit_item['car_document'] ?></small>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" name="save_driver_details" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>


<?php
  // إغلاق الاتصال بقاعدة البيانات
  $pdo = null;
?>

</body>

