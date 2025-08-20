<?php
include '../database/db.php';

// حذف فعالية 
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM Activity WHERE activity_id=?")->execute([$_GET['delete']]);
    header("Location: activities.php"); exit;
}

//نجيب الفعاليات للتعديل
$edit=null;
if(isset($_GET['edit'])){
    $stmt=$pdo->prepare("SELECT * FROM Activity WHERE activity_id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit=$stmt->fetch();
}
?>
<!DOCTYPE html><html lang="ar" dir="rtl">
<head><meta charset="utf-8">
<title>الفعاليات</title>
 <link rel="stylesheet" href="../css/style.css">
</head>
<body>

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


  <h2>الفعاليات</h2>
  
   <!--  جدول الفعاليات وحالتها -->
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>الفعالية</th>
        <th>التاريخ</th>
        <th>النادي المسؤول  </th>
        <th>المدير</th>
        <td>الحالة</td>
        <th>إجراءات</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $stmt="SELECT a.*, c.club_name, CONCAT(m.first_name,' ', m.last_name)  AS mgr
            FROM Activity a
            LEFT JOIN Clubs c ON a.club_id=c.club_id
            LEFT JOIN Members m ON a.activity_manager_id=m.member_id";
      foreach($pdo->query($stmt) as $row):
    ?>
      <tr>
        <td><?= htmlspecialchars($row['activity_id']) ?></td>
        <td><a href="activity_details.php?id=<?= $row['activity_id'] ?>"><?= htmlspecialchars($row['activity_name']) ?></a></td>
        <td><?= $row['proposed_date'] ?></td>
        <td><?= htmlspecialchars($row['club_name']) ?></td>
        <td><?= htmlspecialchars($row['mgr']) ?></td>
        <td><span class="status <?= $row['status'] ?>">
    <?= $row['status']=='active' ? 'نشط' : ($row['status']=='pending' ? 'قادم' : 'مكتمل') ?></span></td>
        <td>
          <a class="btn danger" href="activities.php?delete=<?= $row['activity_id'] ?>"  onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
