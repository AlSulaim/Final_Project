<?php
require_once '../database/db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['member_name'] ?? 'عضو';


// إلغاء التسجيل (إذا كانت حالته pending فقط)
if (isset($_GET['cancel'])) {
    $activity_id = intval($_GET['cancel']);
    $stmt = $pdo->prepare("DELETE FROM activity_registration WHERE activity_id = ? AND member_id = ? AND status = 'pending'");
    $stmt->execute([$activity_id, $member_id]);
    $_SESSION['just_cancelled'] = true;
    header("Location: my_activities.php");
    exit();
}

// جلب الفعاليات اللي العضو مقبول فيها
$stmt = $pdo->prepare("
    SELECT a.*
    FROM activity a
    JOIN activity_registration ar ON a.activity_id = ar.activity_id
    WHERE ar.member_id = ? AND ar.status = 'approved'
    ORDER BY COALESCE(a.actual_date, a.proposed_date) ASC
");
$stmt->execute([$member_id]);
$all_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);



$stmt = $pdo->prepare("
    SELECT a.*
    FROM activity a
    JOIN activity_registration ar ON a.activity_id = ar.activity_id
    WHERE ar.member_id = ? AND ar.status = 'approved' AND a.status IN ('active','pending' )
    ORDER BY COALESCE(a.actual_date, a.proposed_date) ASC
");
$stmt->execute([$member_id]);
$upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);

//جلب الفعاليات المكتملة
$stmt = $pdo->prepare("
    SELECT a.*
    FROM activity a
    JOIN activity_registration ar ON a.activity_id = ar.activity_id
    WHERE ar.member_id = ? AND ar.status = 'approved' AND a.status = 'ended'
    ORDER BY COALESCE(a.actual_date, a.proposed_date) ASC
");
$stmt->execute([$member_id]);
$past = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فعالياتي</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
  <h3>لوحة التحكم</h3>
  <a href="dashboard.php">الرئيسية</a>
  <a href="my_activities.php" class="active">فعالياتي</a>
  <a href="logout.php" onclick="return confirm('هل أنت متأكد أنك تريد تسجيل الخروج؟');">خروج</a>
</div>

<div class="main-content">
  <div class="header">
    <h2>فعالياتي</h2>
  </div>

  <div class="activity-table">
    <h3>الفعاليات القادمة</h3>
    <table>
      <thead>
        <tr>
          <th>الفعالية</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($upcoming): foreach ($upcoming as $row): ?>
        <tr>
          <td><a href="activity_details.php?id=<?= $row['activity_id'] ?>"><?= htmlspecialchars($row['activity_name']) ?></a></td>
          <td><?= $row['actual_date'] ?? $row['proposed_date'] ?></td>

        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="3" style="text-align:center;">لا توجد فعاليات قادمة.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>

    <h3 style="margin-top:40px;">الفعاليات السابقة</h3>
    <table>
      <thead>
        <tr>
          <th>الفعالية</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($past): foreach ($past as $row): ?>
        <tr>
          <td><a href="activity_details.php?id=<?= $row['activity_id'] ?>"><?= htmlspecialchars($row['activity_name']) ?></a></td>
          <td><?= $row['actual_date'] ?? $row['proposed_date'] ?></td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="2" style="text-align:center;">لا توجد فعاليات سابقة.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
