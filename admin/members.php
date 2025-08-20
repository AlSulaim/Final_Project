<?php

include '../database/db.php';

// حذف عضو من الجدول إذا ضغطنا زرdelete
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $del = $pdo->prepare("DELETE FROM Members WHERE member_id=?"); // نحضر جملة الحذف
  $del->execute([$id]); // ننفذ الحذف
  header("Location: members.php"); // نرجع لنفس الصفحة بعد الحذف
  exit;
}

// تعديل بيانات عضو
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
  $id = (int) $_POST['member_id'];
  $fullName = trim($_POST['name']);
  $account = $_POST['account_name'];
  $role = $_POST['member_role'];
  $fac = $_POST['faculty'];
  $dep = $_POST['department'];
  $email = $_POST['email'];
  $phone = $_POST['phone_number'];
  $club_id = $_POST['club_id'] ?: null; // رقم النادي أو null إذا ما اختار نادي

  // نقسم الاسم الكامل إلى ٤ أسماء
  $parts = explode(" ", $fullName);
  $first = $parts[0] ?? '';
  $middle = $parts[1] ?? '';
  $third = $parts[2] ?? '';
  $last = $parts[3] ?? '';

  // نسوي تحديث للبيانات
  $upd = $pdo->prepare("
    UPDATE Members 
    SET first_name=?, middle_name=?, third_name=?, last_name=?, account_name=?, member_role=?, faculty=?, department=?, email=?,phone_number=? , club_id=?
    WHERE member_id=?
  ");
  $upd->execute([$first, $middle, $third, $last, $account, $role, $fac, $dep, $email, $phone, $club_id, $id]);

  header("Location: members.php");
  exit;
}

// نجيب بيانات العضو المطلوب تعديله إذا ضغط تعديل
$editMember = null;
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM Members WHERE member_id=?");
  $stmt->execute([$id]);
  $editMember = $stmt->fetch();
}

// نجيب قائمة الأندية عشان نعرضها
$clubsList = $pdo->query("SELECT club_id, club_name FROM Clubs")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>الأعضاء</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>


  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a class="active" href="members.php">الأعضاء</a>
    <a href="clubs.php">الأندية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="kpis.php">المؤشرات</a>
    <a href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('هل أنت متأكد؟');">خروج</a>
  </div>


  <div class="main-content">
    <h2>الأعضاء</h2>

    <!-- نموذج تعديل العضو -->
    <?php if ($editMember): ?>
      <h3>تعديل عضو</h3>
      <form method="post">
        <!-- نخزن رقم العضو مخفي -->
        <input type="hidden" name="member_id" value="<?= $editMember['member_id'] ?>">
        <label>الاسم الكامل:
          <input type="text" name="name"
            value="<?= htmlspecialchars("{$editMember['first_name']} {$editMember['middle_name']} {$editMember['third_name']} {$editMember['last_name']}") ?>"
            required>
        </label>


        <label>الدور:
          <select name="member_role">
            <?php foreach (['Student', 'ActivityManager'] as $r): ?>
              <option value="<?= $r ?>" <?= $editMember['member_role'] == $r ? 'selected' : '' ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>الكلية: <input type="text" name="faculty" value="<?= $editMember['faculty'] ?>"></label>

        <label>القسم: <input type="text" name="department" value="<?= $editMember['department'] ?>"></label>


        <label>النادي:
          <select name="club_id">
            <option value="">بدون نادي</option>
            <?php foreach ($clubsList as $club): ?>
              <option value="<?= $club['club_id'] ?>" <?= ($editMember['club_id'] == $club['club_id'] ? 'selected' : '') ?>>
                <?= htmlspecialchars($club['club_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>


        <label>رقم الجوال: <input type="text" name="phone_number" value="<?= $editMember['phone_number'] ?>"></label>


        <label>الإيميل: <input type="email" name="email" value="<?= $editMember['email'] ?>"></label>


        <label>اسم الحساب: <input type="text" name="account_name"
            value="<?= htmlspecialchars($editMember['account_name']) ?>" required></label>


        <button name="save" class="btn">حفظ</button>

        <!-- زر الإلغاء يرجع للصفحة بدون تعديل -->
        <a class="btn danger" href="members.php">الغاء</a>
      </form>
    <?php endif; ?>


    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>الدور</th>
          <th>الكلية</th>
          <th>القسم</th>
          <th>النادي</th>
          <th>الإيميل</th>
          <th>رقم الجوال</th>
          <th>اسم الحساب</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // جلب بيانات الأعضاء مع أسماء الأندية إذا فيه
        $query = "SELECT m.*, c.club_name 
                  FROM Members m 
                  LEFT JOIN Clubs c ON m.club_id = c.club_id";

        foreach ($pdo->query($query) as $row):
          ?>
          <tr>
            <td><?= htmlspecialchars($row['member_id']) ?></td>
            <td>
              <?= htmlspecialchars("{$row['first_name']} {$row['middle_name']} {$row['third_name']} {$row['last_name']}") ?>
            </td>
            <td><?= htmlspecialchars($row['member_role']) ?></td>
            <td><?= htmlspecialchars($row['faculty']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['club_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
            <td><?= htmlspecialchars($row['account_name']) ?></td>
            <td>

              <a class="btn edit" href="members.php?edit=<?= $row['member_id'] ?>">تعديل</a>
              <a class="btn danger" href="members.php?delete=<?= $row['member_id'] ?>"
                onclick="return confirm('سيتم حذف العضو نهائيا ! أنت متأكد من الحذف؟')">حذف</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</body>

</html>