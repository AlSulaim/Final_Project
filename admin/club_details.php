<?php
include '../database/db.php';


if (!isset($_GET['id'])) {
  header("Location: clubs.php");
  exit;
}

$club_id = (int) $_GET['id']; // ناخذ رقم النادي ونتأكد إنه رقم

//نجيب بيانات النادي مع اسم المدير 
$stmt = $pdo->prepare("SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) AS manager 
                       FROM Clubs c
                       LEFT JOIN Members m ON c.club_manager_id = m.member_id
                       WHERE c.club_id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetch(); // نخزن بيانات النادي

//الأعضاء اللي ينتمون لهذا النادي
$members = $pdo->prepare("SELECT * FROM Members WHERE club_id = ?");
$members->execute([$club_id]);

//الفعاليات الخاصة بالنادي
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
    <a href="members.php">الأعضاء</a>
    <a class="active" href="clubs.php">الأندية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>


  <div class="main-content">
    <h2>تفاصيل النادي :</h2>

    <!-- عرض معلومات النادي -->
    <div>
      <p><strong> الاسم:</strong> <?= htmlspecialchars($club['club_name']) ?></p>
      <p><strong>تاريخ الانشاء:</strong> <?= $club['initial_date'] ?></p>
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
        <?php foreach ($members as $m): ?>
          <tr>
            <td><?= htmlspecialchars("{$m['first_name']} {$m['middle_name']} {$m['third_name']} {$m['last_name']}") ?>
            </td>
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
        <?php foreach ($activities as $a): ?>
          <tr>
            <td><a
                href="activity_details.php?id=<?= $a['activity_id'] ?>"><?= htmlspecialchars($a['activity_name']) ?></a>
            </td>
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
            <td><?= $a['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>