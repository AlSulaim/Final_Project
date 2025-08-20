<?php

include '../database/db.php';


if (isset($_GET['delete'])) {

  $stmt= $pdo->prepare("DELETE FROM Clubs WHERE club_id=?");
  $stmt->execute([(int) $_GET['delete']]);
  header("Location: clubs.php");
  exit;
}

//  متغير للأخطاء (لو فيه خطأ يظهر للعضو)
$error = null;

// لو المستخدم أرسل النموذج (إضافة أو تعديل)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
  $id = $_POST['club_id'] ?? null; // رقم النادي إذا تعديل
  $name = $_POST['club_name'];
  $date = $_POST['initial_date'];
  $note = $_POST['note'];
  $mgr = $_POST['club_manager_id'] ?: null;

  // لو التاريخ فاضي نحط تاريخ اليوم تلقائي
  if (empty($date)) {
    $date = date('Y-m-d');
  }

  if ($id) {
    // تحديث بيانات نادي موجود
    $pdo->prepare("UPDATE Clubs SET club_name=?, initial_date=?, note=?, club_manager_id=? WHERE club_id=?")->execute([$name, $date, $note, $mgr, $id]);
    header("Location: clubs.php");
    exit;
  } else {
    //// تحقق إن المدير مو داخل نادي ثاني
    $check = $pdo->prepare("SELECT club_id FROM Members WHERE member_id = ?");
    $check->execute([$mgr]);
    $mngr = $check->fetch(PDO::FETCH_ASSOC);

    if (!$mngr) {
      $error = "فضلا ادخل معرف صالح.";
    } elseif (!empty($mngr['club_id'])) {
      $error = "الشخص مسجل في نادي آخر.";
    } else {
      // إنشاء نادي جديد
      $stmt = $pdo->prepare("INSERT INTO Clubs (club_name, initial_date, note, club_manager_id) VALUES (?, ?, ?, ?)");
      $stmt->execute([$name, $date, $note, $mgr]);
      $new_club = $pdo->lastInsertId(); // نحصل على رقم النادي الجديد

      // نربط المدير بالنادي كعضو
      $pdo->prepare("UPDATE Members SET club_id = ? WHERE member_id = ?")->execute([$new_club, $mgr]);

      header("Location: clubs.php");
      exit;
    }
  }
}

// جلب بيانات النادي للتعديل (إذا ضغط تعديل)
$edit = null;
if (isset($_GET['edit'])) {
  $st = $pdo->prepare("SELECT * FROM Clubs WHERE club_id=?");
  $st->execute([(int) $_GET['edit']]);
  $edit = $st->fetch();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الأندية</title>
  <link rel="stylesheet" href="../css/style.css">
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
    <h2>الأندية</h2>


    <a href="partnerships.php?new=1" class="btn">ادارة الشراكات</a>
    <a href="clubs.php?new=1" class="btn">إضافة نادي</a>

    <!-- عرض رسالة الخطأ إذا فيه -->
    <?php if (isset($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <!-- فورم تعديل أو إضافة نادي -->
    <?php if ($edit || isset($_GET['new'])):
      $isNew = isset($_GET['new']); // نحدد هل النموذج للإضافة ولا التعديل
      ?>
      <form method="post">
        <?php if (!$isNew): ?>
          <input type="hidden" name="club_id" value="<?= $edit['club_id'] ?>">
        <?php endif; ?>

        <label>اسم النادي:</label>
        <input name="club_name" value="<?= $isNew ? '' : htmlspecialchars($edit['club_name']) ?>" required>

        <label>تاريخ التأسيس:</label>
        <input type="date" name="initial_date" value="<?= $isNew ? '' : $edit['initial_date'] ?>">

        <label>ملاحظات:</label>
        <input name="note" value="<?= $isNew ? '' : htmlspecialchars($edit['note']) ?>">

        <label>معرّف المدير:</label>
        <input name="club_manager_id" value="<?= $isNew ? '' : $edit['club_manager_id'] ?>">

        <button name="save" class="btn">حفظ</button>
        <a href="clubs.php" class="btn danger">إلغاء</a>
      </form>
    <?php endif; ?>


    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>تاريخ التأسيس</th>
          <th>المدير</th>
          <th>عدد الأعضاء</th>
          <th>ملاحظات</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php
        //  نجيب معلومات كل نادي مع اسم المدير وعدد الأعضاء
        $sql = "SELECT c.*, CONCAT(m.first_name, ' ' ,m.last_name) AS manager,
                       (SELECT COUNT(*) FROM Members mem WHERE mem.club_id = c.club_id) AS member_count
                FROM Clubs c
                LEFT JOIN Members m ON c.club_manager_id = m.member_id";

        foreach ($pdo->query($sql) as $row):
          ?>
          <tr>
            <td><?= htmlspecialchars($row['club_id']) ?></td>
            <td><a href="club_details.php?id=<?= $row['club_id'] ?>"><?= htmlspecialchars($row['club_name']) ?></a></td>
            <td><?= $row['initial_date'] ?></td>
            <td><?= htmlspecialchars($row['manager']) ?></td>
            <td><?= htmlspecialchars($row['member_count']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td>
              <a class="btn edit" href="clubs.php?edit=<?= $row['club_id'] ?>">تعديل</a>
              <a class="btn danger" href="clubs.php?delete=<?= $row['club_id'] ?>"onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>