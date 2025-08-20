<?php
include '../database/db.php';
session_start();

$manager_id = $_SESSION['member_id'] ?? null;
if (!$manager_id) {
    header("Location: login.php");
    exit;
}

// جلب النادي الخاص بالمدير
$stmt = $pdo->prepare("
    SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) AS manager_name
    FROM Clubs c
    LEFT JOIN Members m ON c.club_manager_id = m.member_id
    WHERE c.club_manager_id = ?
");
$stmt->execute([$manager_id]);
$club = $stmt->fetch();

if (!$club) {
    echo "<h2 style='color:red;'>أنت لا تدير أي نادي حالياً.</h2>";
    exit;
}

$club_id = $club['club_id'];
$error = null;
$succ = null;

// إضافة عضو للنادي
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $member_id = (int)$_POST['member_id'];

    $check = $pdo->prepare("SELECT member_id, club_id FROM members WHERE member_id = ?");
    $check->execute([$member_id]);
    $member = $check->fetch();

    if (!$member) {
        $error = "⚠️ خطأ العضو غير موجود.";
    } elseif ($member['club_id'] && $member['club_id'] != $club_id) {
        $error = "⚠️ العضو مسجل في نادي آخر.";
    }else {
        $update = $pdo->prepare("UPDATE members SET club_id = ? WHERE member_id = ?");
        $update->execute([$club_id, $member_id]);
        header("Location: club.php");
        exit;
    }
}

// طرد عضو من النادي
if (isset($_GET['remove'])) {
    $member_id = (int)$_GET['remove'];
    $stmt = $pdo->prepare("UPDATE members SET club_id = NULL WHERE member_id = ?");
    $stmt->execute([$member_id]);
    header("Location: club.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فريقي</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

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
  <h2><?= htmlspecialchars($club['club_name']) ?></h2>
  <p><strong>مدير النادي:</strong> <?= htmlspecialchars($club['manager_name']) ?></p>
  <p><strong> تاريخ التاسيس:</strong> <?= htmlspecialchars($club['initial_date']) ?></p>
  

  

  <!-- جدول الأعضاء -->
  <h3>أعضاء النادي</h3>
      <!-- زر إضافة عضو -->
  <a href="partners_details.php?new=1" class="btn">الشراكات</a>
  <a href="#" onclick="document.getElementById('addBox').style.display='block'; return false;" class="btn">إضافة عضو للنادي</a>
  <div id="addBox" class="form-box" style="display:none; margin-top: 20px;">
    <form method="post">
      <label>رقم العضو: <input type="number" name="member_id" required></label>
      <button type="submit" name="add_member" class="btn">إضافة</button>
      <button type="button" class="btn danger">إلغاء</button>
    </form>
  </div>
  <?php if ($error): ?>
      <div style="color: red; font-weight: bold; margin-top: 10px;">
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif ?>

  <table>
    <thead>
      <tr>
        <th>الاسم</th>
        <th>الكلية</th>
        <th>القسم</th>
        <th>إجراء</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $stmt = $pdo->prepare("SELECT * FROM members WHERE club_id = ?");
        $stmt->execute([$club_id]);
        foreach ($stmt as $member):
      ?>
      <tr>
        <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
        <td><?= htmlspecialchars($member['faculty']) ?></td>
        <td><?= htmlspecialchars($member['department']) ?></td>
        <td>
          <a class="btn danger" href="club.php?remove=<?= $member['member_id'] ?>" onclick="return confirm('هل تريد طرد هذا العضو؟');">طرد</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table><br>



</div>

</body>
</html>
