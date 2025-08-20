<?php
require_once '../database/db.php';
session_start();

///إضافة مؤشر للفعاليات + إضافة عمود تلقائي في جدول activity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_kpi'])) {
  $kpi_name = trim($_POST['name']);
  $measurement = trim($_POST['measurement']);
  $note = $_POST['note'];

  //نمنع الفراغات والرموز من اسم العمود
  if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $measurement)) {
    die('اسم القياس غير صالح. استخدم حروف انجليزية فقط مع _ للمسافه');
  }

  ///إضافة العمود إذا غير موجود
  $check = $pdo->prepare("SHOW COLUMNS FROM activity LIKE ?");
  $check->execute([$measurement]);
  if ($check->rowCount() === 0) {
    $pdo->exec("ALTER TABLE activity ADD COLUMN `$measurement` VARCHAR(255) DEFAULT NULL");
  }

  ////إضافة مؤشر
  $stmt = $pdo->prepare("INSERT INTO activity_kpi (kpi_name, measurement, note) VALUES (?, ?, ?)");
  $stmt->execute([$kpi_name, $measurement, $note]);
  header("Location: kpis.php");
  exit;
}

///حذف مؤشر الفعالية 
if (isset($_GET['delete_kpi'])) {
  $id = (int) $_GET['delete_kpi'];
  $pdo->prepare("DELETE FROM activity_kpi WHERE kpi_id = ? AND activity_id IS NULL")->execute([$id]);
  header("Location: kpis.php");
  exit;
}

///إضافة مؤشر للفرق
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tk_add'])) {
  $tk_name = trim($_POST['name']);
  $tk_note = $_POST['note'];

  $stmt = $pdo->prepare("INSERT INTO activity_team_kpi (kpi_name, note) VALUES (?, ?)");
  $stmt->execute([$tk_name, $tk_note]);
  header("Location: kpis.php");
  exit;
}

////حذف مؤشر الفرق
if (isset($_GET['tk_delete'])) {
  $id = (int) $_GET['tk_delete'];
  $pdo->prepare("DELETE FROM activity_team_kpi WHERE team_kpi_id = ?")->execute([$id]);
  header("Location: kpis.php");
  exit;
}

//جلب المؤشرات
$activityKpis = $pdo->query("SELECT kpi_id, kpi_name, measurement, note FROM activity_kpi WHERE activity_id IS NULL ORDER BY kpi_id")->fetchAll(PDO::FETCH_ASSOC); //نجيب المؤشرات الي مو مربوطه بفعاليه
$teamKpis = $pdo->query("SELECT team_kpi_id, kpi_name, note FROM activity_team_kpi ORDER BY team_kpi_id")->fetchAll(PDO::FETCH_ASSOC);

$noteLabels = [
  'number' => 'رقم',
  'percent' => 'نسبة مئوية %',
  'text' => 'نص',
  'boolean' => 'نعم/لا',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>إدارة المؤشرات</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a href="members.php">الأعضاء</a>
    <a href="clubs.php">الأندية</a>
    <a href="activities.php">الفعاليات</a>
    <a class="active" href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>

  <div class="main-content">
    <h2>مؤشرات الفعالية</h2>
    <form method="POST">
      <input type="hidden" name="add_kpi" value="1">
      <label>اسم المؤشر: <input type="text" name="name" required></label>
      <label>القياس:
        <input type="text" name="measurement" required placeholder="مثال: activity_name">
      </label>
      <label>نوع القيمة:
        <select name="note" required>
          <option value="">اختر</option>
          <?php foreach ($noteLabels as $val => $lbl): ?>
            <option value="<?= $val ?>"><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit" class="btn">إضافة</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>المؤشر</th>
          <th>القياس</th>
          <th>نوع القيمة</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($activityKpis):
          foreach ($activityKpis as $row): ?>
            <tr>
              <td><?= $row['kpi_id'] ?></td>
              <td><?= htmlspecialchars($row['kpi_name']) ?></td>
              <td><?= htmlspecialchars($row['measurement']) ?></td>
              <td><?= $noteLabels[$row['note']] ?? $row['note'] ?></td>
              <!--ادا القيمه الي عاليسار موجوده خدها ادا مب موجوده ياخد الي عاليمين -->
              <td>
                <a href="?delete_kpi=<?= $row['kpi_id'] ?>" class="btn danger"
                  onclick="return confirm('تأكيد حذف؟')">حذف</a>
              </td>
            </tr>
          <?php endforeach; else: ?>
          <tr>
            <td colspan="5">لا توجد مؤشرات.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <h2>مؤشرات الفريق</h2>
    <form method="POST">
      <input type="hidden" name="tk_add" value="1">
      <label>اسم المؤشر: <input type="text" name="name" required></label>
      <label>نوع القيمة:
        <select name="note" required>
          <option value="">اختر</option>
          <?php foreach ($noteLabels as $val => $lbl): ?>
            <option value="<?= $val ?>"><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit" class="btn">إضافة</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>المؤشر</th>
          <th>نوع القيمة</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($teamKpis):
          foreach ($teamKpis as $row): ?>
            <tr>
              <td><?= $row['team_kpi_id'] ?></td>
              <td><?= htmlspecialchars($row['kpi_name']) ?></td>
              <td><?= $noteLabels[$row['note']] ?? $row['note'] ?></td>
              <td>
                <a href="?tk_delete=<?= $row['team_kpi_id'] ?>" class="btn danger" onclick="return confirm('تأكيد حذف؟')">حذف</a>
              </td>
            </tr>
          <?php endforeach; else: ?>
          <tr>
            <td colspan="4">لا توجد مؤشرات للعرض.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>

</html>