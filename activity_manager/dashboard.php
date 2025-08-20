<?php
include '../database/db.php';
session_start();


if (!isset($_SESSION['member_id'])) {
  header("Location: ../login.php");
  exit;
}

$manager_id = $_SESSION['member_id'];

$stmt = $pdo->prepare("SELECT first_name FROM members WHERE member_id = ?");
$stmt->execute([$manager_id]);
$name = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT club_id FROM clubs WHERE club_manager_id = ?");
$stmt->execute([$manager_id]);
$club_id = $stmt->fetchColumn();


?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>لوحة تحكم مدير الفعاليات</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a class="active" href="dashboard.php">الرئيسية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="club.php">النادي</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

  <div class="main-content">
    <div class="header">
      <h2>مرحبا <?= htmlspecialchars($name['first_name']) ?> !</h2>
    </div>

    <!-- البطاقات -->
    <div class="cards">

      <!-- عدد الأندية -->
      <div class="card">
        <div class="card-text">
          <p>أنت مدير </p>
          <?php
          $stmt = $pdo->prepare("
        SELECT c.club_name
        FROM Clubs c
        WHERE c.club_manager_id = ?
      ");
          $stmt->execute([$manager_id]);
          $club_name = $stmt->fetchColumn();
          ?>
          <h3><?= $club_name ? htmlspecialchars($club_name) : 'غير محدد' ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-landmark"></i></div>
      </div>


      <!-- عدد الفعاليات -->
      <div class="card">
        <div class="card-text">
          <p>اجمالي الفعاليات الخاصه بالنادي</p>
          <?php
          $stmt = $pdo->prepare("SELECT COUNT(*) FROM Activity WHERE activity_manager_id = ?");
          $stmt->execute([$manager_id]);
          $activity_count = $stmt->fetchColumn();
          ?>
          <h3><?= $activity_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
      </div>

      <!-- عدد الاعضاء-->
      <div class="card">
        <div class="card-text">
          <p>اجمالي اعضاء النادي</p>
          <?php
          $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE club_id = ?");
          $stmt->execute([$club_id]);
          $member_count = $stmt->fetchColumn();

          ?>
          <h3><?= (int) $member_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-users"></i></div>
      </div>


    </div>


    <div class="activity-table">
      <h4>الفعاليات الخاصة بي القادمة</h4>
      <table>
        <thead>
          <tr>
            <th>الفعالية</th>
            <th>النادي</th>
            <th>التاريخ</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT a.activity_name, c.club_name, a.proposed_date, a.status
          FROM Activity a
          LEFT JOIN Clubs c ON a.club_id = c.club_id
          WHERE a.status IN ('pending', 'active') 
            AND a.activity_manager_id = ?
          ORDER BY a.proposed_date ASC
        ";
          $stmt = $pdo->prepare($query);
          $stmt->execute([$manager_id]);
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['activity_name']) ?></td>
              <td><?= htmlspecialchars($row['club_name'] ?? 'بدون نادي') ?></td>
              <td><?= htmlspecialchars($row['proposed_date']) ?></td>
              <td><span class="status <?= $row['status'] ?>">
                <?= $row['status'] == 'active' ? 'نشط' : ($row['status'] == 'pending' ? 'قادم' : 'مكتمل') ?>
                </span></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>

</html>