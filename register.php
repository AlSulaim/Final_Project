<?php

require_once 'database/db.php';

session_start();

// مصفوفات نحفظ فيها الأخطاء أو الرسائل الناجحة
$errors = $success = [];

// اذا المستخدم ضغط زر الإرسال 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ناخذ كل البيانات من الفورم ونتأكد إنها نظيفة (نشيل الفراغات الزايدة)
    $first    = trim($_POST['first_name']  ?? '');
    $middle   = trim($_POST['middle_name'] ?? '');
    $third    = trim($_POST['third_name']  ?? '');
    $last     = trim($_POST['last_name']   ?? '');
    $email    = trim($_POST['email']       ?? '');
    $user     = trim($_POST['username']    ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $pass     = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm']          ?? '';

    // نتحقق من الشروط مثل الطول الصحيح والبيانات مطابقة
    if (mb_strlen($first) < 2)  $errors[] = 'الاسم الأول قصير.';
    if (mb_strlen($last) < 2)   $errors[] = 'الاسم الأخير قصير.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد غير صالح.';
    if (mb_strlen($user) < 3) $errors[] = 'اسم المستخدم قصير.';
    if ($pass !== $confirm) $errors[] = 'كلمة المرور غير متطابقة.';
    if (!preg_match('/^\d{10}$/', $phone_number)) $errors[] = 'رقم الجوال غير صحيح.';

    // اذا ما فيه أخطاء، نشيك هل الايميل أو الاسم مستخدم من قبل
    if (!$errors) {
        $check = $pdo->prepare("SELECT 1 FROM Members WHERE email = ? OR account_name = ?");
        $check->execute([$email, $user]);
        if ($check->fetch()) $errors[] = 'البريد أو اسم المستخدم مستخدم مسبقًا.';
    }

    // اذا كل شي تمام وكلمة السر متطابقة
    if (!$errors && !empty($pass) && !empty($confirm) && $pass === $confirm) {
        // نشفر كلمة المرور
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        // نحفظ البيانات في جدول الأعضاء
        $insert = $pdo->prepare("INSERT INTO Members (first_name, middle_name, third_name , last_name, member_role, email, account_name, phone_number, pwd_hash)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$first, $middle, $third, $last, 'Student', $email, $user, $phone_number, $hash]);

        // نضيف رسالة نجاح
        $success[] = 'تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول.';
    }

}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إنشاء حساب</title>
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-card">
  <h2>إنشاء حساب جديد</h2>

  <!-- نعرض الأخطاء هنا إذا فيه -->
  <?php foreach ($errors  as $e) echo "<div class='status-msg error'>$e</div>"; ?>
  <!-- نعرض رسالة النجاح إذا تمت العملية -->
  <?php foreach ($success as $s) echo "<div class='status-msg success'>$s</div>"; ?>

  <form method="post">
    <!-- الاسم الأول والثاني -->
    <div class="form-row">
      <div class="form-group">
        <input type="text" name="first_name" placeholder="الاسم الأول" value="<?=htmlspecialchars($_POST['first_name']??'')?>" required>
        <i class="fa fa-user"></i>
      </div>
      <div class="form-group">
        <input type="text" name="middle_name" placeholder="الاسم الثاني" value="<?=htmlspecialchars($_POST['middle_name']??'')?>" required>
        <i class="fa fa-user"></i>
      </div>
    </div>

    <!-- الاسم الثالث والأخير -->
    <div class="form-row">
      <div class="form-group">
        <input type="text" name="third_name" placeholder="الاسم الثالث" value="<?=htmlspecialchars($_POST['third_name']??'')?>">
        <i class="fa fa-user"></i>
      </div>
      <div class="form-group">
        <input type="text" name="last_name" placeholder="الاسم الأخير" value="<?=htmlspecialchars($_POST['last_name']??'')?>" required>
        <i class="fa fa-user"></i>
      </div>
    </div>


    <div class="form-group">
      <input type="email" name="email" placeholder="البريد الالكتروني" value="<?=htmlspecialchars($_POST['email']??'')?>" required>
      <i class="fa fa-envelope"></i>
    </div>


    <div class="form-group">
      <input type="text" name="username" placeholder="اسم المستخدم" value="<?=htmlspecialchars($_POST['username']??'')?>" required>
      <i class="fa fa-user-tag"></i>
    </div>


    <div class="form-group">
      <input type="text" name="phone_number" placeholder="رقم الجوال" value="<?=htmlspecialchars($_POST['phone_number']??'')?>" required>
      <i class="fa fa-phone"></i>
    </div>


    <div class="form-group">
      <input type="password" name="password" placeholder="كلمة المرور" required>
      <i class="fa fa-lock"></i>
    </div>


    <div class="form-group">
      <input type="password" name="confirm" placeholder="تاكيد كلمة المرور" required>
      <i class="fa fa-lock"></i>
    </div>


    <button class="btn-primary" type="submit">انشاء الحساب</button>
  </form>

  <!-- رابط تسجيل الدخول إذا عندك حساب -->
  <div class="link">لديك حساب بالفعل ؟ <a href="login.php">سجّل الدخول</a></div>
</div>

</body>
</html>
