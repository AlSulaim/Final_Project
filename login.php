<?php

require_once 'database/db.php';

session_start();

// نجهز متغير نحفظ فيه رسالة الخطأ (لو فيه)
$error = '';

// اذا المستخدم ضغط زر "دخول"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ناخذ البيانات من الفورم ونشيل الفراغات من اسم الحساب
    $account  = trim($_POST['account'] ?? '');
    $password = $_POST['password'] ?? '';

    // أول شي نبحث في جدول المشرفين (Admin)
    $stmt = $pdo->prepare("SELECT * FROM Admin WHERE account = ?");
    $stmt->execute([$account]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // اذا لقينا مشرف بهالاسم
    if ($admin) {
        $input_hash = hash('sha256', $password); // نحسب الهاش بطريقة SHA256 (للامان)
        // نتحقق من كلمة المرور     
        if ($password === $admin['pwd_hash'] || $input_hash === $admin['pwd_hash'] || password_verify($password, $admin['pwd_hash'])) {
            $_SESSION['admin_id'] = $admin['admin_id']; // نخزن رقم المشرف
            $_SESSION['role'] = 'Admin'; // نخزن دوره
            header("Location: admin/dashboard.php"); // نحوله على صفحة المشرف
            exit();
        }
    }

    // اذا ما لقيناه في جدول Admin نبحث في جدول الأعضاء Members
    $stmt = $pdo->prepare("SELECT * FROM Members WHERE account_name = ?");
    $stmt->execute([$account]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // اذا لقينا عضو بهالاسم
    if ($member) {
        $input_hash = hash('sha256', $password); // نفس الشي نحسب هاش
        if ($password === $member['pwd_hash'] || $input_hash === $member['pwd_hash'] || password_verify($password, $member['pwd_hash'])) {
            $_SESSION['member_id'] = $member['member_id']; // نخزن رقم العضو
            $_SESSION['member_name'] = $member['name']; // اسمه
            $_SESSION['role'] = $member['member_role']; // و نوعه (طالب، مدير نشاط..)

            // نحوله حسب دوره إلى الصفحة الخاصة فيه
            switch ($member['member_role']) {
                case 'Student':
                    header("Location: member/dashboard.php");
                    break;
                case 'ActivityManager':
                    header("Location: activity_manager/dashboard.php");
                    break;
                default:
                    $error = "غير معروف."; // لو الدور ما نعرفه نعرض خطأ
            }
            exit();
        }
    }

    // اذا ما قدرنا نسجل دخول نعرض رسالة خطأ
    $error = "البيانات غير صحيحة.";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="logo-top">
  <img src="imgs/UT.png">
</div>


<div class="auth-card">
  <h2>تسجيل الدخول</h2>

  <!-- نعرض رسالة الخطأ لو فيه -->
  <?php if ($error): ?>
    <div class="status-msg error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- نموذج تسجيل الدخول -->
  <form method="post">
    <div class="input-group">

      <input type="text" name="account" placeholder="اسم المستخدم" required>
      <i class="fa fa-user"></i>
    </div>

    <div class="input-group">

      <input type="password" name="password" id="password" placeholder="كلمة المرور" required>
      <i id="togglepassword" class="fa fa-lock"></i>
    </div>

    <button class="login-btn" type="submit">دخول</button>
  </form>

  <!-- رابط انشاء حساب جديد -->
  <div class="link">
    ما عندك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
  </div>
</div>

</body>
</html>
