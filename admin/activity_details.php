<?php
include '../database/db.php';

if (!isset($_GET['id'])) {
  header("Location: activities.php");
  exit;
}

$activity_id = (int) $_GET['id'];

//معلومات الفعالية
$stmt = $pdo->prepare("SELECT a.*, c.club_name, CONCAT(m.first_name, ' ', m.last_name) AS manager_name
                       FROM Activity a
                       LEFT JOIN Clubs c ON a.club_id = c.club_id
                       LEFT JOIN Members m ON a.activity_manager_id = m.member_id
                       WHERE a.activity_id = ?");
$stmt->execute([$activity_id]);
$activity = $stmt->fetch();

if (!$activity) {
  echo "الفعالية غير موجودة.";
  exit;
}

//تقييمات الحضور للفعالية
$feedbackStmt = $pdo->prepare("SELECT f.*, CONCAT(m.first_name, ' ', m.last_name) AS member_name FROM feedback f
  JOIN members m ON f.member_id = m.member_id
  WHERE f.activity_id = ?
  ORDER BY f.created_at DESC
");
$feedbackStmt->execute([$activity_id]);
$feedbacks = $feedbackStmt->fetchAll(PDO::FETCH_ASSOC);




// نحسب متوسط تقييم الحضور للفعالية
$avgStmt = $pdo->prepare("SELECT AVG(feedback_score) as avg_score FROM feedback WHERE activity_id = ?");
$avgStmt->execute([$activity_id]);
$avgScore = $avgStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>تفاصيل الفعالية</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a href="members.php">الأعضاء</a>
    <a href="clubs.php">الأندية</a>
    <a class="active" href="activities.php">الفعاليات</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

  <div class="main-content">
    <h2>تفاصيل الفعالية: <?= htmlspecialchars($activity['activity_name']) ?></h2>
    <p><strong>النادي المسؤول:</strong> <?= htmlspecialchars($activity['club_name']) ?></p>
    <p><strong>مدير الفعالية:</strong> <?= htmlspecialchars($activity['manager_name']) ?></p>

    <table>
      <thead>
        <tr>
          <th>الاسم</th>
          <th>التاريخ المقترح</th>
          <th>التاريخ الفعلي</th>
          <th>وقت البدء المقترح</th>
          <th>وقت البدء الفعلي</th>
          <th>المدة المقترحة</th>
          <th>المدة الفعلية</th>
          <th>التكلفة المقترحة</th>
          <th>التكلفة الفعلية</th>
          <th>عدد الحضور المتوقع</th>
          <th>عدد الحضور الفعلي</th>
          <th>الحالة</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= htmlspecialchars($activity['activity_name']) ?></td>
          <td><?= $activity['proposed_date'] ?></td>
          <td><?= $activity['actual_date'] ?? '—' ?></td>
          <td><?= $activity['proposed_start_time'] ?></td>
          <td><?= $activity['actual_start_time'] ?? '—' ?></td>
          <td><?= $activity['proposed_duration_min'] ?> دقيقة</td>
          <td><?= $activity['actual_duration_min'] ?? '—' ?> دقيقة</td>
          <td><?= $activity['proposed_cost'] ?> ريال</td>
          <td><?= $activity['actual_cost'] ?? '—' ?> ريال</td>
          <td><?= $activity['proposed_audience_number'] ?? '—' ?></td>
          <td><?= $activity['actual_audience_number'] ?? '—' ?></td>
          <td><?= $activity['status'] ?></td>
        </tr>
      </tbody>
    </table>


    <!--جدول تقييمات الاعضاء او الحضور للفعالية -->
    <h3>تقييمات الأعضاء</h3>
    <?php if ($avgScore): ?>
      <div>متوسط تقييم الفعالية: <?= round($avgScore, 2) ?> ⭐</div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>العضو</th>
          <th>التقييم</th>
          <th>الملاحظة</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($feedbacks) > 0): ?>
          <?php foreach ($feedbacks as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['member_name']) ?></td>
              <td><?= $row['feedback_score'] !== null ? $row['feedback_score'] . '⭐' : '—' ?></td>
              <td><?= htmlspecialchars($row['content']) ?></td>
              <td><?= $row['created_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">لا توجد تقييمات لهذه الفعالية.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>

</html>