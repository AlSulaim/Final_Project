<?php
require_once '../database/db.php'; 
session_start(); 


$manager_id = $_SESSION['member_id'] ?? null;
if (!$manager_id) {
    header("Location: login.php"); 
    exit;
}

// نجيب club_id الخاص بالمستخدم اللي سجل دخول
$stmt = $pdo->prepare("SELECT club_id FROM members WHERE member_id = ?");
$stmt->execute([$manager_id]);
$club_id = $stmt->fetchColumn(); // نأخذ رقم النادي

// إذا ما عنده نادي نوقف الصفحة
if (!$club_id) {
    echo "<h2 style='color:red;'>لا يوجد نادي مرتبط بك.</h2>";
    exit;
}

// نجيب الشراكات الخاصة بالنادي حقه
$stmt = $pdo->prepare("SELECT * FROM partnerships WHERE club_id = ? ORDER BY created_at DESC");
$stmt->execute([$club_id]);
$partnerships = $stmt->fetchAll(PDO::FETCH_ASSOC); // نحول النتيجة لمصفوفة
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>شراكات النادي</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<div class="sidebar">
  <h3 class="sidebar-title">لوحة التحكم</h3>
  <a href="dashboard.php">الرئيسية</a>
  <a href="activities.php">الفعاليات</a>
  <a class="active" href="club.php">النادي</a>
  <a href="kpis.php">المؤشرات</a>
  <a href="reports.php">التقارير</a>
  <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
</div>


<div class="main-content">
  <h2>الشراكات المرتبطة بالنادي</h2>

  <h2>جدول الشراكات</h2>
  <table>
    <thead>
        <tr>
            <th>الجهة</th>
            <th>النوع</th>
            <th>التصنيف</th>
            <th>الوصف</th>
            <th>الايميل</th>
            <th>تاريخ البداية</th>
            <th>تاريخ النهاية</th>
            <th>حالة الشراكة</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($partnerships as $p): ?>
        <tr>
            <!-- نعرض بيانات الشراكة -->
            <td><?= htmlspecialchars($p['partner_name']) ?></td>
            <td><?= htmlspecialchars($p['partnership_type']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= htmlspecialchars($p['contact_email']) ?></td>
            <td><?= htmlspecialchars($p['start_date']) ?></td>
            <td><?= htmlspecialchars($p['end_date']) ?></td>
            <td>
              <!-- نعرض الحالة: قادم أو منتهي -->
              <span class="status <?= $p['status'] ?>">
                <?= $p['status']=='pending' ? 'قادم' : 'منتهي' ?>
              </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
