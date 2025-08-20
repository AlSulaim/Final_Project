<?php
include '../database/db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: activities.php");
    exit;
}
$activity_id = (int) $_GET['id'];

//  النادي التابع لمدير الفعالية
$clubStmt = $pdo->prepare("SELECT club_id FROM members WHERE member_id = ?");
$clubStmt->execute([$_SESSION['member_id']]);
$club_id = $clubStmt->fetchColumn();

//  أعضاء النادي
$membersStmt = $pdo->prepare("
  SELECT member_id, CONCAT(first_name,' ',last_name) AS name
    FROM members
   WHERE club_id = ?
   ORDER BY first_name, last_name
");
$membersStmt->execute([$club_id]);
$teamMembers = $membersStmt->fetchAll(PDO::FETCH_ASSOC);

// مؤشرات الفريق مع نوع القيمة
$teamKpis = $pdo->query("
  SELECT team_kpi_id, kpi_name, note
    FROM activity_team_kpi
   ORDER BY team_kpi_id
")->fetchAll(PDO::FETCH_ASSOC);

// حفظ التقييمات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scores'])) {
    $ins = $pdo->prepare("
      INSERT INTO activity_team_measurement
        (activity_id, member_id, team_kpi_id, result, measure_date)
      VALUES (?, ?, ?, ?, NOW())
    ");
    foreach ($_POST['scores'] as $s) {
        $mid  = (int)$s['member_id'];
        $tk   = (int)$s['kpi_id'];
        $type = $s['type'];
        $val  = $s['value'];
        if ($type === 'boolean') {
            $val = ($val === '1' ? 1 : 0);
        }
        if ($mid && $tk && $val !== '') {
            $ins->execute([$activity_id, $mid, $tk, $val]);
        }
    }
    header("Location: activity_team_evaluation.php?id={$activity_id}");
    exit;
}

//   سجل التقييمات
$rows = $pdo->prepare("
  SELECT tm.measure_date,
         CONCAT(m.first_name,' ',m.last_name) AS member_name,
         k.kpi_name,
         tm.result,
         k.note
    FROM activity_team_measurement tm
    JOIN members m ON tm.member_id = m.member_id
    JOIN activity_team_kpi k ON tm.team_kpi_id = k.team_kpi_id
   WHERE tm.activity_id = ?
   ORDER BY tm.measure_date DESC
");
$rows->execute([$activity_id]);
$history = $rows->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تقييم أعضاء النادي</title>
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
    <h2>تقييم أعضاء النادي</h2>
    <form method="POST" id="evalForm">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>العضو</th>
            <th>المؤشر</th>
            <th>التقييم</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($teamMembers as $i => $tm): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($tm['name']) ?></td>
            <td>
              <select name="scores[<?= $i ?>][kpi_id]"
                      data-type-select
                      data-row="<?= $i ?>">
                <option value="">— اختر —</option>
                <?php foreach ($teamKpis as $k): ?>
                  <option value="<?= $k['team_kpi_id'] ?>"
                          data-type="<?= $k['note'] ?>">
                    <?= htmlspecialchars($k['kpi_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <input type="hidden"
                     name="scores[<?= $i ?>][member_id]"
                     value="<?= $tm['member_id'] ?>">
              <input type="hidden"
                     name="scores[<?= $i ?>][type]"
                     class="type-field"
                     data-row="<?= $i ?>"
                     value="number">
            </td>
            <td class="input-cell" data-row="<?= $i ?>">
              <input type="number"
                     name="scores[<?= $i ?>][value]"
                     step="1"
                     class="value-field"
                     data-row="<?= $i ?>">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table><br>
      <button type="submit" class="btn">حفظ التقييم</button>
    </form>

    <h3>سجل التقييمات</h3>
    <?php if ($history): ?>
    <table>
      <thead>
        <tr>
          <th>تاريخ</th>
          <th>العضو</th>
          <th>المؤشر</th>
          <th>النتيجة</th>
        </tr>
      </thead>
      <tbody>
         <?php foreach ($history as $r): ?>
    <tr>
      <td><?= htmlspecialchars($r['measure_date']) ?></td>
      <td><?= htmlspecialchars($r['member_name']) ?></td>
      <td><?= htmlspecialchars($r['kpi_name']) ?></td>
      <td>
        <?php
          if ($r['note'] === 'boolean') {
            echo ($r['result'] == 1) ? 'نعم' : 'لا';
          } else {
            echo htmlspecialchars($r['result']);
          }
        ?>
      </td>
    </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>لا توجد تقييمات مسجلة بعد.</p>
    <?php endif; ?>
  </div>

  <script>
    document.querySelectorAll('[data-type-select]').forEach(sel => {
      sel.addEventListener('change', () => {
        const type    = sel.selectedOptions[0].dataset.type;
        const row     = sel.dataset.row;
        const valueEl = document.querySelector(`.value-field[data-row="${row}"]`);
        const typeEl  = document.querySelector(`.type-field[data-row="${row}"]`);
        let newHtml;

        switch(type) {
          case 'boolean':
            newHtml = `
              <select name="scores[${row}][value]"
                      class="value-field"
                      data-row="${row}">
                <option value="1">نعم</option>
                <option value="0">لا</option>
              </select>`;
            break;
          case 'text':
            newHtml = `
              <input type="text"
                     name="scores[${row}][value]"
                     class="value-field"
                     data-row="${row}">`;
            break;
          default:
            newHtml = `
              <input type="number"
                     name="scores[${row}][value]"
                     step="1"
                     class="value-field"
                     data-row="${row}">`;
        }

        valueEl.outerHTML = newHtml;
        typeEl.value = type;
      });
    });
  </script>
</body>
</html>
