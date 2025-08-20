<?php
include '../database/db.php';

$manager_id = $_SESSION['member_id'] ?? null;
if (!$manager_id) {
    header("Location: login.php");
    exit;
}

$club_id = (int)$_GET['member_id'];

//بيانات النادي 
$stmt = $pdo->prepare("SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) AS manager 
                       FROM Clubs c
                       LEFT JOIN Members m ON c.club_manager_id = m.member_id
                       WHERE c.club_id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetch();

// جلب الأعضاء 
$members = $pdo->prepare("SELECT * FROM Members WHERE club_id = ?");
$members->execute([$club_id]);

// جلب العفاليات 
$activities = $pdo->prepare("SELECT * FROM Activity WHERE club_id = ?");
$activities->execute([$club_id]);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تفاصيل النادي</title>
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
  <h2>تفاصيل النادي: <?= htmlspecialchars($club['club_name']) ?></h2>

  <div class="form-box">
    <p><strong>تاريخ التأسيس:</strong> <?= $club['created_at'] ?></p>
    <p><strong>مدير النادي:</strong> <?= htmlspecialchars($club['manager']) ?></p>
    <p><strong>ملاحظات:</strong> <?= htmlspecialchars($club['note']) ?></p>
  </div>

  <h3>أعضاء النادي</h3>
  <table>
    <thead>
      <tr>
        <th>الاسم</th>
        <th>الدور</th>
        <th>الكلية</th>
        <th>القسم</th>
        <th>الإيميل</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($members as $m): ?>
        <tr>
          <td><?= htmlspecialchars("{$m['first_name']} {$m['middle_name']} {$m['third_name']} {$m['last_name']}") ?></td>
          <td><?= $m['member_role'] ?></td>
          <td><?= $m['faculty'] ?></td>
          <td><?= $m['department'] ?></td>
          <td><?= $m['email'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

 <h3>فعاليات النادي</h3>
<table>
  <thead>
    <tr>
      <th>الاسم</th>
      <th>التاريخ المقترح</th>
      <th>التاريخ الفعلي</th>
      <th>وقت البدء المقترح</th>
      <th>وقت البدء الفعلي</th>
      <th>المدة المقترحة</th>
      <th>المدة الفعلية </th>
      <th>التكلفة المقترحة</th>
      <th>التكلفة الفعلية</th>
      <th>عدد الحضور المتوقع</th>
      <th>عدد الحضور الفعلي</th>
      <th>الحالة</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($activities as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['activity_name']) ?></td>
        <td><?= $a['proposed_date'] ?></td>
        <td><?= $a['actual_date'] ?></td>
        <td><?= $a['proposed_start_time'] ?></td>
        <td><?= $a['actual_start_time'] ?></td>
        <td><?= $a['proposed_duration_min'] ?></td>
        <td><?= $a['actual_duration_min'] ?></td>
        <td><?= $a['proposed_cost'] ?></td>
        <td><?= $a['actual_cost'] ?></td>
        <td><?= $a['proposed_audience_number'] ?></td>
        <td><?= $a['actual_audience_number'] ?></td>
        <td  ><?= $a['status'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</div>
</body>
</html>
