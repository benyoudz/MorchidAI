<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شرح نظام مرشدكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .diagram-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .component-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s;
        }
        .component-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .api-endpoint {
            font-family: monospace;
            background-color: #f1f8ff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .login-section {
            background-color: #e9f5ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-primary">نظام <span class="text-dark">مرشدكم</span></h1>
            <p class="lead">نظام متكامل لإدارة النقل العام والمواصلات الذكية</p>
        </div>

        <div class="alert alert-warning text-center">
            <h5>تنبيه هام</h5>
            <p>هذا النموذج الأولي عالي الحساسية ويستخدم فقط لأغراض التحكيم والتقييم</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="login-section">
                    <h3 class="mb-4">صفحة تسجيل الدخول</h3>
                    <p>صفحة تسجيل الدخول الأساسية للنظام متاحة عبر الرابط:</p>
                    <div class="api-endpoint mb-3">/login.php</div>
                    <p>تتضمن الصفحة حقول لإدخال:</p>
                    <ul>
                        <li>اسم المستخدم أو البريد الإلكتروني</li>
                        <li>كلمة المرور</li>
                        <li>نوع المستخدم (مسؤول، سائق، مسافر)</li>
                    </ul>
                    <a href="login.php" class="btn btn-outline-primary">عرض نموذج تسجيل الدخول</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="login-section">
                    <h3 class="mb-4">صفحة إنشاء حساب جديد</h3>
                    <p>صفحة تسجيل المستخدمين الجدد متاحة عبر الرابط:</p>
                    <div class="api-endpoint mb-3">/register.php</div>
                    <p>تتضمن الصفحة حقول لإدخال:</p>
                    <ul>
                        <li>الاسم الكامل</li>
                        <li>البريد الإلكتروني</li>
                        <li>كلمة المرور وتأكيدها</li>
                        <li>رقم الهاتف</li>
                        <li>نوع الحساب (سائق أو مسافر)</li>
                    </ul>
                    <a href="register.php" class="btn btn-outline-primary">عرض نموذج التسجيل</a>
                </div>
            </div>
        </div>

        <div class="diagram-container mt-5">
            <h3 class="mb-4">صفحة المحطة (station.php)</h3>
            <p>صفحة المحطة تعرض معلومات محددة عن محطة النقل بناءً على معرّف المحطة.</p>
            
            <div class="api-endpoint mb-3">GET /station.php?id={station_id}</div>
            
            <h5 class="mt-4">المعطيات المطلوبة:</h5>
            <ul>
                <li><strong>station_id</strong>: معرّف المحطة الرقمي (مثال: 6)</li>
            </ul>
            
            <h5 class="mt-4">البيانات المعروضة:</h5>
            <ul>
                <li>اسم المحطة وموقعها</li>
                <li>الخطوط التي تمر عبر المحطة</li>
                <li>مواعيد القادمة للمركبات</li>
                <li>خريطة توضح موقع المحطة</li>
                <li>إمكانية مسح QR Code للمحطة</li>
            </ul>
            
            <h5 class="mt-4">واجهة برمجة التطبيقات المقابلة:</h5>
            <div class="api-endpoint mb-3">GET /api/station/info?id={station_id}</div>
            <p>تعيد هذه الواجهة بيانات JSON تحتوي على جميع معلومات المحطة المطلوبة.</p>
            
            <a href="station.php?id=6" class="btn btn-primary">تجربة صفحة المحطة (مثال: المحطة رقم 6)</a>
        </div>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 component-card">
                    <div class="card-body">
                        <h5 class="card-title">واجهة الإدارة</h5>
                        <h6 class="card-subtitle mb-2 text-muted">للمسؤولين</h6>
                        <p class="card-text">
                            لوحة تحكم شاملة لإدارة:
                            <ul>
                                <li>السائقين والمركبات</li>
                                <li>خطوط النقل والمواعيد</li>
                                <li>المحطات والنقاط</li>
                                <li>التقارير والإحصائيات</li>
                            </ul>
                        </p>
                        <div class="api-endpoint mb-2">/admin.php</div>
                        <p>تتطلب صلاحيات مسؤول للوصول</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 component-card">
                    <div class="card-body">
                        <h5 class="card-title">واجهة السائقين</h5>
                        <h6 class="card-subtitle mb-2 text-muted">للمستخدمين الميدانيين</h6>
                        <p class="card-text">
                            مميزات الواجهة:
                            <ul>
                                <li>تحديث الموقع في الوقت الحقيقي</li>
                                <li>عرض تفاصيل الرحلة</li>
                                <li>إدارة الوثائق والتراخيص</li>
                                <li>التواصل مع الإدارة</li>
                            </ul>
                        </p>
                        <div class="api-endpoint mb-2">/driver_dashboard.php</div>
                        <p>تتطلب تسجيل دخول كسائق</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 component-card">
                    <div class="card-body">
                        <h5 class="card-title">واجهة المسافر</h5>
                        <h6 class="card-subtitle mb-2 text-muted">للمستخدمين العاديين</h6>
                        <p class="card-text">
                            مميزات الواجهة:
                            <ul>
                                <li>البحث عن المحطات والخطوط</li>
                                <li>عرض مواعيد القادمة</li>
                                <li>تتبع المركبات</li>
                                <li>حفظ المحطات المفضلة</li>
                            </ul>
                        </p>
                        <div class="api-endpoint mb-2">/passenger_dashboard.php</div>
                        <p>متاحة بعد تسجيل الدخول</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="diagram-container mt-5">
            <h3 class="mb-4">واجهات برمجة التطبيقات (APIs)</h3>
            
            <div class="mb-4">
                <h5>API تحديث موقع السائق</h5>
                <div class="api-endpoint mb-2">POST /api/driver/update-location</div>
                <p>يستخدم من قبل تطبيق السائقين لتحديث الموقع الحالي للمركبة</p>
                <p>المعطيات المطلوبة: driver_id, vehicle_id, latitude, longitude</p>
            </div>
            
            <div class="mb-4">
                <h5>API معلومات المحطة</h5>
                <div class="api-endpoint mb-2">GET /api/station/info?id={station_id}</div>
                <p>يستخدم من قبل صفحة المحطة وتطبيق المحطات لاسترجاع معلومات المحطة</p>
                <p>المعطيات المطلوبة: station_id</p>
            </div>
            
            <div class="mb-4">
                <h5>API بيانات الرحلة</h5>
                <div class="api-endpoint mb-2">GET /api/trip/details/{trip_id}</div>
                <p>يستخدم لاسترجاع تفاصيل الرحلة الحالية للسائق أو المسافر</p>
            </div>
            
            <div class="mb-4">
                <h5>API تسجيل الدخول</h5>
                <div class="api-endpoint mb-2">POST /api/auth/login</div>
                <p>يستخدم للتحقق من بيانات المستخدم وإرجاع token للوصول</p>
                <p>المعطيات المطلوبة: email, password, user_type</p>
            </div>
        </div>

        <div class="alert alert-info mt-5">
            <h5>التطبيق المرفق</h5>
            <p>يوجد تطبيق جوال مرفق (Android/iOS) يستمد بياناته مباشرة من هذا النظام عبر واجهات برمجة التطبيقات المذكورة أعلاه.</p>
            <p>واجهات المستخدم الرئيسية في التطبيق:</p>
            <ul>
                <li>واجهة تسجيل الدخول والتحقق</li>
                <li>واجهة السائق (تحديث الموقع، إدارة الرحلات)</li>
                <li>واجهة المسافر (البحث، تتبع الرحلات، عرض المحطات)</li>
                <li>واجهة عرض المحطة (عند مسح QR Code)</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>