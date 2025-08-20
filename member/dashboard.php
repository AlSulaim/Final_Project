<?php
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

require_once '../database/db.php';

// جلب اسم العضو
$stmt = $pdo->prepare("SELECT CONCAT (first_name, ' '  , last_name) as fullname FROM members WHERE member_id = ?");
$stmt->execute([$member_id]);
$member_name = $stmt->fetchColumn();
$_SESSION['fullname'] = $member_name;

//التسجيل للفعاليه
 if (isset($_GET['join'])) {
    $actId = intval($_GET['join']);

    // تحقق من وجود تسجيل ب pending او approved فقط
    $check = $pdo->prepare("SELECT * FROM activity_registration WHERE activity_id = ? AND member_id = ? AND status IN ('pending', 'approved')");
    $check->execute([$actId, $member_id]);

    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO activity_registration (activity_id, member_id) VALUES (?, ?)");
        $insert->execute([$actId, $member_id]);
    }

    header("Location: dashboard.php?joined=1");
    exit();
}

//إلغاء التسجيل
if (isset($_GET['cancel'])) {
    $actId = intval($_GET['cancel']);
    $stmt = $pdo->prepare("DELETE FROM activity_registration WHERE activity_id = ? AND member_id = ? AND status = 'pending'");
    $stmt->execute([$actId, $member_id]);
    header("Location: dashboard.php?cancelled=1");
    exit();
}

//الفعاليات المتاحة
$stmt = $pdo->query("SELECT activity_id, activity_name, proposed_date FROM activity WHERE status IN ('pending','active') ORDER BY proposed_date ASC");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب حالة تسجيل العضو
$stmt = $pdo->prepare("SELECT activity_id, status FROM activity_registration WHERE member_id = ? AND status IN ('pending', 'approved', 'rejected')");
$stmt->execute([$member_id]);
$myRegsRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);


// جلب جميع الفعاليات الي انقبل فيها العضو
$stmt = $pdo->prepare("SELECT activity_id, status FROM activity_registration WHERE member_id = ? AND status =  'approved'");
$stmt->execute([$member_id]);
$myApproved = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة التحكم - الفعاليات</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <h3>لوحة التحكم</h3>
    <a href="dashboard.php" class="active">الرئيسية</a>
    <a href="my_activities.php">فعالياتي</a>
    <a href="logout.php" onclick="return confirm('هل تريد تسجيل الخروج؟');">خروج</a>
  </div>

  <div class="main-content">
    <h2>مرحبًا،  <?= htmlspecialchars($member_name) ?> ! </h2>

    <div class="cards">
      <div class="card">
        <p>الفعاليات المتاحة</p>
        <h3><?= count($activities) ?></h3>
      </div>
      <div class="card">
        <p>فعالياتي المسجلة</p>
        <h3><?= count($myApproved) ?></h3>
      </div>
    </div>

    <h3>قائمة الفعاليات</h3>
    <table>
      <thead>
        <tr>
          <th>الفعاليه</th>
          <th>التاريخ المقترح</th>
          <th>الحالة / إجراء</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($activities as $act): 
        $aid    = $act['activity_id'];
        $status = $myRegsRaw[$aid] ?? null;
      ?>
        <tr>
          <td><?= htmlspecialchars($act['activity_name']) ?></td>
          <td><?= htmlspecialchars($act['proposed_date'] ?: '—') ?></td>
          <td>
            <?php if ($status): ?>
              <?php if ($status === 'approved'): ?>
                <span class="status active">مقبول ✅</span>
              <?php elseif ($status === 'pending'): ?>
                <span class="status pending">بانتظار القبول ⏳</span>
                <a href="?cancel=<?= $aid ?>" class="btn danger">إلغاء الطلب</a>
              <?php elseif ($status === 'rejected'): ?>
                <span class="status rejected">مرفوض ❌</span>
                <a href="?join=<?= $aid ?>" class="btn idle">إعادة التسجيل</a>
              <?php endif; ?>
            <?php else: ?>
              <a href="?join=<?= $aid ?>" class="btn">تسجيل</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</body>
</html>
