<?php
include '../database/db.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة تحكم المشرف</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>


  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a class="active" href="dashboard.php">الرئيسية</a>
    <a href="members.php">الأعضاء</a>
    <a href="clubs.php">الأندية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

  <!-- المحتوى الرئيسي -->
  <div class="main-content">
    <div class="header">
      <h2>الرئيسية</h2>
    </div>

    <div class="cards">
      <!-- عدد الأعضاء -->
      <div class="card">
        <div class="card-text">
          <p>عدد الأعضاء</p>
          <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM Members");
            $members_count = $stmt->fetchColumn();
          ?>
          <h3><?= $members_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-users"></i></div>
      </div>

      <!-- عدد الأندية -->
      <div class="card">
        <div class="card-text">
          <p>عدد الأندية</p>
          <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM Clubs");
            $clubs_count = $stmt->fetchColumn();
          ?>
          <h3><?= $clubs_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-landmark"></i></div>
      </div>

      <!-- عدد الفعاليات -->
      <div class="card">
        <div class="card-text">
          <p>عدد الفعاليات</p>
          <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM Activity");
            $activity_count = $stmt->fetchColumn();
          ?>
          <h3><?= $activity_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
      </div>

      <!-- عدد المؤشرلت -->
      <div class="card">
        <div class="card-text">
          <p>KPI</p>
          <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM Activity_KPI");
            $kpi_count = $stmt->fetchColumn();
          ?>
          <h3><?= $kpi_count ?></h3>
        </div>
        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
      </div>
    </div>

    <div class="activity-table">
      <h4>الفعاليات القادمة</h4>
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

        // معلومات الفعاليات الجايه او النشطه
$query = "SELECT a.activity_name, c.club_name, a.proposed_date, a.status
  FROM Activity a
  LEFT JOIN Clubs c ON a.club_id = c.club_id
  WHERE a.status IN ('pending', 'active')
  ORDER BY a.proposed_date ASC
";
          $stmt = $pdo->query($query);
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
          <tr>
            <td><?= htmlspecialchars($row['activity_name']) ?></td>
            <td><?= htmlspecialchars($row['club_name'] ?? 'بدون نادي') ?></td>
            <td><?= htmlspecialchars($row['proposed_date']) ?></td>
            <td><span class="status <?= $row['status'] ?>"><?= $row['status']=='active' ? 'نشط' : ($row['status']=='pending' ? 'قادم' : 'مكتمل') ?></span></td>
        <td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>
</html>

