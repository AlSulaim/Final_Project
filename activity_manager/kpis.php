<?php
require_once '../database/db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

// جلب المؤشرات
$activityKpis = $pdo->query("
  SELECT kpi_id, kpi_name, measurement, note
    FROM activity_kpi
   WHERE activity_id IS NULL
   ORDER BY kpi_id
")->fetchAll(PDO::FETCH_ASSOC);

$teamKpis = $pdo->query("
  SELECT team_kpi_id, kpi_name
    FROM activity_team_kpi
   ORDER BY team_kpi_id
")->fetchAll(PDO::FETCH_ASSOC);

// ترجمة القياسات
$measurementLabels = [
  'date' => 'فرق التاريخ',
  'time' => 'فرق الوقت',
  'cost' => 'فرق التكلفة',
  'audience' => 'عدد الحضور',
  'feedback' => 'رضا المشاركين',
  'purpose_of_activity' => 'الهدف',
  'serves_student' => 'خدمة الطالب',
  'serves_student_plan' => 'خدمة خطة الطالب',
  'serves_special_needs' => 'خدمة ذوي الاحتياجات الخاصة'
];

// ترجمة أنواع القيمة
$noteLabels = [
  'number'  => 'عدد',
  'percent' => 'نسبة مئوية %',
  'text'    => 'نص',
  'boolean' => 'نعم/لا',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>عرض مؤشرات الفعالية والفريق</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="club.php">النادي</a>
    <a class="active" href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

  <div class="main-content">
    <h2>مؤشرات الفعاليات</h2>
    <table class="kpi-table">
      <thead>
        <tr>
          <th>#</th>
          <th>المؤشر</th>
          <th>القياس</th>
          <th>نوع القيمة</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($activityKpis): ?>
          <?php foreach ($activityKpis as $k): ?>
          <tr>
            <td><?= $k['kpi_id'] ?></td>
            <td><?= htmlspecialchars($k['kpi_name']) ?></td>
            <td>
              <?=                 $measurementLabels[$k['measurement']] 
                  ?? htmlspecialchars($k['measurement']) 
              ?>
            </td>
            <td>
              <?= $noteLabels[$k['note']] ?? 'عدد' ?>
            </td>
            <td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">لا توجد مؤشرات فعاليات مسجلة.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <h2>مؤشرات تقييم الفرق</h2>
    <table class="kpi-table">
      <thead>
        <tr>
          <th>#</th>
          <th>المؤشر</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($teamKpis): ?>
          <?php foreach ($teamKpis as $tk): ?>
          <tr>
            <td><?= $tk['team_kpi_id'] ?></td>
            <td><?= htmlspecialchars($tk['kpi_name']) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3">لا توجد مؤشرات فريق مسجلة.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
