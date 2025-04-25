<?php
include 'db_config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type'];
    if (empty($first_name)) $errors[] = "الاسم الأول مطلوب";
    if (empty($last_name)) $errors[] = "الاسم الأخير مطلوب";
    if (empty($phone)) $errors[] = "رقم الهاتف مطلوب";
    if (empty($password)) $errors[] = "كلمة المرور مطلوبة";
    if ($password != $confirm_password) $errors[] = "كلمتا المرور غير متطابقتين";
    
    // التحقق من عدم وجود رقم الهاتف مسبقاً
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $errors[] = "رقم الهاتف مسجل مسبقاً";
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $phone, $hashed_password, $user_type);
        
        if ($stmt->execute()) {
            // إذا كان المستخدم سائقاً، نقوم بحفظ بياناته الإضافية
            if ($user_type == 'driver') {
                $user_id = $conn->insert_id;
                
                // إنشاء مجلد التحميلات إذا لم يكن موجوداً
                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                
                $residence = $_POST['residence'];
                $safety_certificate_number = $_POST['safety_certificate_number'];
                $certificate_expiry = $_POST['certificate_expiry'];
                $car_color = $_POST['car_color'];
                $car_acquisition_date = $_POST['car_acquisition_date'];
                $car_expiry_date = $_POST['car_expiry_date'];
                
                // معالجة تحميل الملفات
                $safety_certificate_image_name = '';
                $car_document_name = '';
                $target_dir = "uploads/";
                
                // تحميل شهادة السلامة
                if (isset($_FILES['safety_certificate_image'])) {
                    $safety_certificate_image_name = uniqid() . '_' . basename($_FILES['safety_certificate_image']['name']);
                    $target_file = $target_dir . $safety_certificate_image_name;
                    move_uploaded_file($_FILES['safety_certificate_image']['tmp_name'], $target_file);
                }
                
                // تحميل وثيقة السيارة
                if (isset($_FILES['car_document'])) {
                    $car_document_name = uniqid() . '_' . basename($_FILES['car_document']['name']);
                    $target_file = $target_dir . $car_document_name;
                    move_uploaded_file($_FILES['car_document']['tmp_name'], $target_file);
                }
                
                // إدخال بيانات السائق
                $stmt2 = $conn->prepare("INSERT INTO driver_details 
                    (user_id, residence, safety_certificate_number, safety_certificate_image, certificate_expiry, 
                    car_color, car_acquisition_date, car_expiry_date, car_document) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt2->bind_param("issssssss", $user_id, $residence, $safety_certificate_number, 
                    $safety_certificate_image_name, $certificate_expiry, $car_color, 
                    $car_acquisition_date, $car_expiry_date, $car_document_name);
                
                if (!$stmt2->execute()) {
                    $errors[] = "فشل في تسجيل بيانات السائق: " . $conn->error;
                }
            }
            
            if (empty($errors)) {
                $success = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
            }
        } else {
            $errors[] = "حدث خطأ أثناء إنشاء الحساب: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - مورشد وهران</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .register-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h3 {
            color: #0d6efd;
            font-weight: 700;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px;
            font-weight: 500;
        }
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .driver-fields {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="logo">
                <h3>إنشاء حساب جديد</h3>
                <p class="text-muted">مورشد وهران - نظام النقل الذكي</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-1"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-primary">تسجيل الدخول</a>
                </div>
            <?php else: ?>
                <form method="POST" action="register.php" enctype="multipart/form-data">
                    <div class="form-section">
                        <h5 class="mb-4"><i class="bi bi-person"></i> المعلومات الأساسية</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label required-field">الاسم الأول</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label required-field">الاسم الأخير</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label required-field">رقم الهاتف</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label required-field">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label required-field">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h5 class="mb-4"><i class="bi bi-person-badge"></i> نوع الحساب</h5>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="person" value="person" checked>
                                <label class="form-check-label" for="person">
                                    مستخدم عادي
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="driver" value="driver">
                                <label class="form-check-label" for="driver">
                                    سائق سيارة أجرة
                                </label>
                            </div>
                        </div>
                        
                        <div id="driver-fields" class="driver-fields">
                            <h6 class="mb-3"><i class="bi bi-car-front"></i> معلومات السائق</h6>
                            <div class="mb-3">
                                <label class="form-label required-field">العنوان</label>
                                <input type="text" class="form-control" name="residence" id="residence">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">رقم شهادة السلامة</label>
                                <input type="text" class="form-control" name="safety_certificate_number" id="safety_certificate_number">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">صورة شهادة السلامة</label>
                                <input type="file" class="form-control" name="safety_certificate_image" id="safety_certificate_image" accept="image/*,.pdf">
                                <small class="text-muted">يجب أن تكون الصورة أو PDF بحجم أقل من 2MB</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">تاريخ نهاية الشهادة</label>
                                <input type="date" class="form-control" name="certificate_expiry" id="certificate_expiry">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">لون السيارة</label>
                                <input type="text" class="form-control" name="car_color" id="car_color">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">تاريخ الحصول على السيارة</label>
                                <input type="date" class="form-control" name="car_acquisition_date" id="car_acquisition_date">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">تاريخ نهاية صلاحية السيارة</label>
                                <input type="date" class="form-control" name="car_expiry_date" id="car_expiry_date">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">وثيقة السيارة</label>
                                <input type="file" class="form-control" name="car_document" id="car_document" accept="image/*,.pdf">
                                <small class="text-muted">يجب أن تكون الوثيقة بصيغة صورة أو PDF بحجم أقل من 2MB</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // إظهار/إخفاء حقول السائق عند تغيير نوع الحساب
        document.querySelectorAll('input[name="user_type"]').forEach(el => {
            el.addEventListener('change', function() {
                const driverFields = document.getElementById('driver-fields');
                if (this.value === 'driver') {
                    driverFields.style.display = 'block';
                    // جعل الحقول المطلوبة مطلوبة فقط عند اختيار سائق
                    document.querySelectorAll('#driver-fields input[required]').forEach(field => {
                        field.required = true;
                    });
                } else {
                    driverFields.style.display = 'none';
                    // إزالة الخاصية required عند اختيار مستخدم عادي
                    document.querySelectorAll('#driver-fields input').forEach(field => {
                        field.required = false;
                    });
                }
            });
        });
        
        // التحقق من صحة التواريخ
        document.querySelector('form').addEventListener('submit', function(e) {
            const userType = document.querySelector('input[name="user_type"]:checked').value;
            
            if (userType === 'driver') {
                const today = new Date().toISOString().split('T')[0];
                const expiryDate = document.getElementById('certificate_expiry').value;
                
                if (expiryDate && expiryDate < today) {
                    alert('تاريخ نهاية الشهادة يجب أن يكون في المستقبل');
                    e.preventDefault();
                    return false;
                }
                
                // يمكنك إضافة المزيد من التحقق هنا
            }
        });
    </script>
</body>
</html>