<?php
include '../database/db.php';
session_start();

$manager_id = $_SESSION['member_id'] ?? null;
if (!$manager_id) {
    header("Location: login.php");
    exit;
}

// جلب النادي التابع لمدير الفعالية
$stmt = $pdo->prepare("SELECT club_id FROM members WHERE member_id=?");
$stmt->execute([$manager_id]);
$club_id = $stmt->fetchColumn();

// إضافة فعالية جديدة
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO activity (
        activity_name,
        proposed_date,
        proposed_start_time,
        proposed_duration_min,
        proposed_cost,
        proposed_audience_number,
        club_id,
        status,
        activity_manager_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['activity_name'],
        $_POST['proposed_date'],
        $_POST['proposed_start_time'],
        $_POST['proposed_duration_min'],
        $_POST['proposed_cost'],
        $_POST['proposed_audience_number'],
        $club_id, // تلقائي من المدير
        $_POST['status'],
        $manager_id
    ]);

    header("Location: activities.php");
    exit;
}
if (isset($_GET['remove'])) {
    $activity_id = $_GET['remove'];
    
    $stmt = $pdo->prepare("DELETE FROM activity 
    WHERE activity_id = ? 
    AND activity_manager_id = ?");
    $stmt->execute([$activity_id, $manager_id]);
    
    header("Location: activities.php");
    exit;
}

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
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a class="active" href="activities.php">الفعاليات</a>
    <a href="club.php">النادي</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

<div class="main-content">
  <h2>فعالياتي</h2>
  <a href="activities.php?add=1" class="btn">إضافة فعالية</a>

  <?php if (isset($_GET['add'])): ?>
    <form method="post" class="form-box">
      <label>اسم الفعالية:      <br><input name="activity_name" required></label><br>
      <label>التاريخ المقترح: <br><input type="date" name="proposed_date"></label><br>
      <label>وقت البدء المقترح: <br><input type="datetime-local" name="proposed_start_time"></label><br>
      <label>المدة المقترحة: <br><input type="number" name="proposed_duration_min"></label><br>
      <label>التكلفة المقترحة: <br><input type="number" step="0.01" name="proposed_cost"></label><br>
      <label>عدد الحضور المتوقع: <br><input type="number" name="proposed_audience_number"></label><br>
      <label>الحالة:
        <select name="status">
          <option value="pending">قادم</option>
          <option value="active">نشط</option>
          <option value="ended">مكتمل</option>
        </select>
      </label><br><br>
      <button name="save" class="btn">حفظ</button>
    <form action="clubs.php" method="get">
    <a href="activities.php" class="btn danger">إلغاء</a></form>
    </form>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>اسم الفعالية</th>
        <th>التاريخ</th>
        <th>النادي</th>
        <th>الحالة</th>
        <th>إجراء</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $stmt = $pdo->prepare("SELECT a.*, c.club_name 
          FROM activity a 
          LEFT JOIN clubs c ON a.club_id = c.club_id 
          WHERE a.activity_manager_id = ? 
          ORDER BY a.proposed_date DESC");
        $stmt->execute([$manager_id]);
        foreach ($stmt as $row):
      ?>
        <tr>
          <td><a href="activity_details.php?id=<?= $row['activity_id'] ?>"><?= htmlspecialchars($row['activity_name']) ?></a></td>
          <td><?= $row['proposed_date'] ?></td>
          <td><?= htmlspecialchars($row['club_name']) ?></td>
          <td><span class="status <?= $row['status'] ?>">
            <?= $row['status']=='active' ? 'نشط' : ($row['status']=='pending' ? 'قادم' : 'مكتمل') ?>
          </span></td>
          <td>
            <a class="btn danger" href="activities.php?remove=<?= $row['activity_id'] ?>" onclick="return confirm('هل تريد حذف الفعالية؟');">حذف</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>