<?php
require_once '../database/db.php';
session_start();

// حذف شراكة إذا المستخدم ضغط زر حذف
if (isset($_GET['delete'])) {
  $delete_id = (int) $_GET['delete']; // نحول الـ id لرقم للتأكيد
  $stmt = $pdo->prepare("DELETE FROM partnerships WHERE partnership_id = ?");
  $stmt->execute([$delete_id]); // نحذف الشراكة من القاعدة
  header("Location: partnerships.php"); // نرجع لنفس الصفحة بعد الحذف
  exit();
}

$success = null;
$edit = null;

// إذا المستخدم بيعدل شراكة نجيب بياناتها
if (isset($_GET['edit'])) {
  $stmt = $pdo->prepare("SELECT * FROM partnerships WHERE partnership_id = ?");
  $stmt->execute([(int) $_GET['edit']]);
  $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// إذا المستخدم ضغط حفظ (جديد أو تعديل)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_partnership'])) {
  $id = $_POST['partnership_id'] ?? null;
  $club_id = $_POST['club_id'] ?? null;
  $partner_name = trim($_POST['partner_name']);
  $partnership_type = $_POST['partnership_type'];
  $category = $_POST['category'];
  $description = trim($_POST['description']);
  $contact_email = trim($_POST['contact_email']);
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  // لو تعديل
  if ($id) {
    $stmt = $pdo->prepare("UPDATE partnerships SET club_id=?, partner_name=?, partnership_type=?, category=?, description=?, contact_email=?, start_date=?, end_date=? WHERE partnership_id=?");
    $stmt->execute([$club_id, $partner_name, $partnership_type, $category, $description, $contact_email, $start_date, $end_date, $id]);
  } else {
    // إضافة جديدة
    $stmt = $pdo->prepare("INSERT INTO partnerships (club_id, partner_name, partnership_type, category, description, contact_email, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$club_id, $partner_name, $partnership_type, $category, $description, $contact_email, $start_date, $end_date]);
  }

  header("Location: partnerships.php");
  exit;
}

// نجيب كل الشراكات من القاعدة مع اسم النادي المرتبط فيها
$stmt = $pdo->query("SELECT p.*, c.club_name FROM partnerships p LEFT JOIN clubs c ON p.club_id = c.club_id ORDER BY p.created_at DESC");
$partnerships = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <title>الشراكات</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

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
    <h2>جدول الشراكات</h2>

    <?php if ($edit || isset($_GET['new'])):
      $isNew = isset($_GET['new']); // نعرف هل المستخدم يضيف أو يعدل
      ?>


      <div style="margin-top: 20px;">
        <form method="POST">
          <input type="hidden" name="save_partnership" value="1">
          <?php if (!$isNew): ?>
            <input type="hidden" name="partnership_id" value="<?= $edit['partnership_id'] ?>">
          <?php endif; ?>

          <label>النادي:</label>
          <select name="club_id" required>
            <?php
            $clubs = $pdo->query("SELECT club_id, club_name FROM clubs")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($clubs as $club) {
              $selected = (!$isNew && $edit['club_id'] == $club['club_id']) ? 'selected' : '';
              echo '<option value="' . $club['club_id'] . '" ' . $selected . '>' . htmlspecialchars($club['club_name']) . '</option>';
            }
            ?>
          </select>


          <label>اسم الجهة الشريكة:</label>
          <input type="text" name="partner_name" value="<?= $isNew ? '' : htmlspecialchars($edit['partner_name']) ?>"
            required>


          <label>نوع الشراكة:</label>
          <select name="partnership_type" required>
            <option value="خارجية" <?= (!$isNew && $edit['partnership_type'] == 'خارجية') ? 'selected' : '' ?>>خارجية
            </option>
            <option value="داخلية" <?= (!$isNew && $edit['partnership_type'] == 'داخلية') ? 'selected' : '' ?>>داخلية
            </option>
          </select>


          <label>تصنيف الشراكة:</label>
          <select name="category" required>
            <?php
            $cats = ['صحية', 'رياضية', 'تجارية', 'تطوعية', 'مجتمعية', 'إعلامية', 'تدريبية', 'أخرى'];
            foreach ($cats as $cat) {
              $selected = (!$isNew && $edit['category'] == $cat) ? 'selected' : '';
              echo "<option value='$cat' $selected>$cat</option>";
            }
            ?>
          </select>


          <label>وصف الشراكة:</label>
          <textarea name="description" required><?= $isNew ? '' : htmlspecialchars($edit['description']) ?></textarea>


          <label>البريد الإلكتروني للتواصل:</label>
          <input type="email" name="contact_email" value="<?= $isNew ? '' : htmlspecialchars($edit['contact_email']) ?>">

          <label>تاريخ البداية:</label>
          <input type="date" name="start_date" value="<?= $isNew ? '' : $edit['start_date'] ?>" required>

          <label>تاريخ النهاية:</label>
          <input type="date" name="end_date" value="<?= $isNew ? '' : $edit['end_date'] ?>" required>

          <button class="btn" type="submit">حفظ</button>
          <a class="btn danger" href="partnerships.php">إلغاء</a>
        </form>
      </div>
    <?php endif; ?>


    <a class="btn" href="partnerships.php?new=1">إضافة شراكة</a>


    <table>
      <thead>
        <tr>
          <th>الجهة</th>
          <th>النادي</th>
          <th>النوع</th>
          <th>التصنيف</th>
          <th>الوصف</th>
          <th>الايميل</th>
          <th>تاريخ البداية</th>
          <th>تاريخ النهاية</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($partnerships as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['partner_name']) ?></td>
            <td><?= htmlspecialchars($p['club_name']) ?></td>
            <td><?= htmlspecialchars($p['partnership_type']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= htmlspecialchars($p['contact_email']) ?></td>
            <td><?= htmlspecialchars($p['start_date']) ?></td>
            <td><?= htmlspecialchars($p['end_date']) ?></td>
            <td><span
                class="status <?= $p['status'] ?>"><?= $p['status'] == 'active' ? 'نشط' : ($p['status'] == 'pending' ? 'قادم' : 'مكتمل') ?></span>
            </td>
            <td>
              <a class="btn edit" href="partnerships.php?edit=<?= $p['partnership_id'] ?>">تعديل</a>
              <a class="btn danger" href="partnerships.php?delete=<?= $p['partnership_id'] ?>"
                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</body>

</html>