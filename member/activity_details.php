<?php
require_once '../database/db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT activity_name ,proposed_date ,actual_date, proposed_start_time, proposed_duration_min,activity_id FROM Activity WHERE activity_id = ?");
$stmt->execute([$activity_id]);
$activity = $stmt->fetch();

if (!$activity) {
    die("الفعالية غير موجودة.");
}
$member_name = $_SESSION['member_name'] ?? 'عضو';

// إلغاء التسجيل (إذا كان طلبه ما زال pending)
if (isset($_GET['cancel'])) {
    $activity_id = intval($_GET['cancel']);
    $stmt = $pdo->prepare("DELETE FROM activity_registration WHERE activity_id = ? AND member_id = ? AND status = 'pending'");
    $stmt->execute([$activity_id, $member_id]);
    header("Location: my_activities.php?cancelled=1");
    exit();
}


// التحقق هل الطالب قيّم الفعالية بالفعل
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE activity_id = ? AND member_id = ?");
$stmt->execute([$activity_id, $member_id]);
$hasRated = $stmt->fetchColumn() > 0;

// التحقق من ان الفعالية لم تنتهي
$stmt = $pdo->prepare("SELECT COUNT(*) FROM activity WHERE activity_id = ? AND status in ('pending','active')");
$stmt->execute([$activity_id]);
$isended = $stmt->fetchColumn() > 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فعالياتي</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <div class="sidebar">
    <h3>لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a href="my_activities.php" class="active">فعالياتي</a>
    <a href="logout.php" onclick="return confirm('هل تريد تسجيل الخروج؟');">خروج</a>
  </div>

  <div class="main-content">
    <h2>فعالياتي المسجلة</h2>
    <table>
      <thead>
        <tr>
          <th>الفعالية</th>
          <th>التاريخ المقترح</th>
          <th>التاريخ الفعلي</th>
          <th>وقت البدء</th>
          <th>المدة</th>
          <th>التقييم</th>
        </tr>
      </thead>
      <tbody>
          <tr>
            <td><?= htmlspecialchars($activity['activity_name'] ?: '—') ?></td>
            <td><?= htmlspecialchars($activity['proposed_date'] ?: '—') ?></td>
            <td><?= htmlspecialchars($activity['actual_date'] ?: '—') ?></td>
            <td>
              <?= $activity['proposed_start_time']
                   ? date('H:i', strtotime($activity['proposed_start_time']))
                   : '—' ?>
            </td>
            <td>
              <?= !empty($activity['proposed_duration_min'])
                   ? htmlspecialchars($activity['proposed_duration_min']) . ' دقيقة'
                   : '—' ?>
            </td>
<td>
  <?php if ($hasRated): ?>
    <span class="status active">تم التقييم </span>
  <?php elseif ($isended): ?>
    <span class="status pending">غير متاح</span>
    <?php else: ?>
    <a href="feedback.php?id=<?= $activity['activity_id'] ?>" class="btn idle">تقييم</a>
  <?php endif; ?>
</td>
          </tr>
      </tbody>
    </table>
  </div>

</body>
</html>
