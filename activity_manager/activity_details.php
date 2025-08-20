<?php
include '../database/db.php';
session_start();
if (!isset($_GET['id'])) {
    header("Location: activities.php");
    exit;
}
$activity_id = (int)$_GET['id'];
// تحديث بيانات الفعالية
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE Activity SET
        activity_name = ?, 
        proposed_date = ?, 
        actual_date = ?,
        proposed_start_time = ?, 
        actual_start_time = ?,
        proposed_duration_min = ?, 
        actual_duration_min = ?,
        proposed_cost = ?, 
        actual_cost = ?,
        proposed_audience_number = ?, 
        actual_audience_number = ?,
        status = ?
      WHERE activity_id = ?
    ");
    $stmt->execute([
        $_POST['activity_name'],
        $_POST['proposed_date'] ?: null,
        $_POST['actual_date']   ?: null,
        $_POST['proposed_start_time'] ?: null,
        $_POST['actual_start_time']   ?: null,
        $_POST['proposed_duration_min'] ?: null,
        $_POST['actual_duration_min']   ?: null,
        $_POST['proposed_cost'] ?: null,
        $_POST['actual_cost']   ?: null,
        $_POST['proposed_audience_number'] ?: null,
        $_POST['actual_audience_number']   ?: null,
        $_POST['status'],
        $activity_id
    ]);
    header("Location: activity_details.php?id={$activity_id}");
    exit;
}
// جلب بيانات الفعالية
$stmt = $pdo->prepare("SELECT a.*, c.club_name, CONCAT(m.first_name,' ',m.last_name) AS manager_name
                       FROM Activity a
                       LEFT JOIN Clubs c ON a.club_id = c.club_id
                       LEFT JOIN Members m ON a.activity_manager_id = m.member_id
                      WHERE a.activity_id = ?");
$stmt->execute([$activity_id]);
$activity = $stmt->fetch();
if (!$activity) {
    echo "الفعالية غير موجودة.";
    exit;
}
// جلب طلبات التسجيل الجديدة
$pendingStmt = $pdo->prepare("
  SELECT ar.member_id, ar.status, m.first_name, m.last_name 
    FROM activity_registration ar
    JOIN Members m ON ar.member_id = m.member_id
   WHERE ar.activity_id = ?
");
$pendingStmt->execute([$activity_id]);
$requests = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
// جلب تقييمات الفعالية
$feedbackStmt = $pdo->prepare("
  SELECT f.*, CONCAT(m.first_name,' ',m.last_name) AS member_name
    FROM feedback f
    JOIN members m ON f.member_id = m.member_id
   WHERE f.activity_id = ?
   ORDER BY f.created_at DESC
");
$feedbackStmt->execute([$activity_id]);
$feedbacks = $feedbackStmt->fetchAll(PDO::FETCH_ASSOC);
// متوسط التقييم
$avgStmt = $pdo->prepare("SELECT AVG(feedback_score) as avg_score FROM feedback WHERE activity_id = ?");
$avgStmt->execute([$activity_id]);
$avgScore = $avgStmt->fetchColumn();
// جلب مهام الفريق
$tasksStmt = $pdo->prepare("
  SELECT at.activity_id, at.member_id, at.task_id, 
         t.task_name, t.task_cost, 
         CONCAT(m.first_name, ' ', m.last_name) AS member_name
  FROM activity_team at
  LEFT JOIN activity_task t ON at.task_id = t.task_id
  JOIN members m ON at.member_id = m.member_id
  WHERE at.activity_id = ?
");
$tasksStmt->execute([$activity_id]);
$team_tasks = $tasksStmt->fetchAll(PDO::FETCH_ASSOC);
// جلب المهام المتاحة
$availableTasksStmt = $pdo->prepare("SELECT * FROM activity_task WHERE activity_id = ?");
$availableTasksStmt->execute([$activity_id]);
$available_tasks = $availableTasksStmt->fetchAll(PDO::FETCH_ASSOC);
// جلب أعضاء النادي التابع للفعالية فقط (يشمل المدير)
$approvedMembersStmt = $pdo->prepare("
  SELECT m.member_id, CONCAT(m.first_name, ' ', m.last_name) AS member_name
    FROM members m
    JOIN activity a ON m.club_id = a.club_id
   WHERE a.activity_id = ?
");
$approvedMembersStmt->execute([$activity_id]);
$approved_members = $approvedMembersStmt->fetchAll(PDO::FETCH_ASSOC);


// إسناد مهمة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task_id = (int)$_POST['task_id'];
    $member_id = (int)$_POST['member_id'];
    // التحقق من وجود العضو في الفريق
    $checkStmt = $pdo->prepare("SELECT * FROM activity_team WHERE activity_id = ? AND member_id = ?");
    $checkStmt->execute([$activity_id, $member_id]);
    if ($checkStmt->fetch()) {
        // تحديث المهمة إذا كان العضو موجود
        $stmt = $pdo->prepare("UPDATE activity_team SET task_id = ? 
                               WHERE activity_id = ? AND member_id = ?");
        $stmt->execute([$task_id, $activity_id, $member_id]);
    } else {
        // إضافة العضو مع المهمة إذا لم يكن موجود
        $stmt = $pdo->prepare("INSERT INTO activity_team 
                              (activity_id, member_id, task_id) 
                              VALUES (?, ?, ?)");
        $stmt->execute([$activity_id, $member_id, $task_id]);
    }
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// إنشاء مهمة جديدة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_task'])) {
    $task_name = $_POST['new_task_name'];
    $task_cost = $_POST['new_task_cost'] ?: 0;
    $stmt = $pdo->prepare("INSERT INTO activity_task 
                          (activity_id, task_name, task_cost) 
                          VALUES (?, ?, ?)");
    $stmt->execute([$activity_id, $task_name, $task_cost]);
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// تعديل مهمة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_task'])) {
    $task_id = (int)$_POST['task_id'];
    $task_name = $_POST['edit_task_name'];
    $task_cost = $_POST['edit_task_cost'] ?: 0;
    $stmt = $pdo->prepare("UPDATE activity_task SET 
                          task_name = ?, task_cost = ?
                          WHERE task_id = ?");
    $stmt->execute([$task_name, $task_cost, $task_id]);
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// حذف مهمة
if (isset($_GET['delete_task'])) {
    $task_id = (int)$_GET['delete_task'];
    // إلغاء إسناد المهمة من جميع الأعضاء
    $stmt = $pdo->prepare("UPDATE activity_team SET task_id = NULL 
                           WHERE task_id = ?");
    $stmt->execute([$task_id]);
    // حذف المهمة نفسها
    $stmt = $pdo->prepare("DELETE FROM activity_task WHERE task_id = ?");
    $stmt->execute([$task_id]);
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// إلغاء إسناد مهمة من عضو
if (isset($_GET['unassign_task'])) {
    $member_id = (int)$_GET['unassign_task'];
    $stmt = $pdo->prepare("UPDATE activity_team SET task_id = NULL 
                           WHERE activity_id = ? AND member_id = ?");
    $stmt->execute([$activity_id, $member_id]);
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// حذف إسناد مهمة من عضو
if (isset($_GET['del_assignment'])) {
    $member_id = (int)$_GET['del_assignment'];
    $stmt = $pdo->prepare("DELETE FROM activity_team 
                           WHERE activity_id = ? AND member_id = ?");
    $stmt->execute([$activity_id, $member_id]);
    header("Location: activity_details.php?id={$activity_id}#tasks");
    exit;
}
// قبول او رفض طلب الانضمام للفعاليه
if (isset($_GET['approve']) || isset($_GET['reject'])) {
    $member_id = (int)(isset($_GET['approve']) ? $_GET['approve'] : $_GET['reject']);
    $status = isset($_GET['approve']) ? 'approved' : 'rejected';
    // تعديل حالة الطلب
    $stmt = $pdo->prepare("UPDATE activity_registration SET status = ? 
                           WHERE activity_id = ? AND member_id = ?");
    $stmt->execute([$status, $activity_id, $member_id]);
    // إذا تم القبول، أضفه في activity_team لو مو موجود
    if ($status === 'approved') {
        $checkStmt = $pdo->prepare("SELECT * FROM activity_team WHERE activity_id = ? AND member_id = ?");
        $checkStmt->execute([$activity_id, $member_id]);
        if (!$checkStmt->fetch()) {
            $insertStmt = $pdo->prepare("INSERT INTO activity_team (activity_id, member_id) VALUES (?, ?)");
            $insertStmt->execute([$activity_id, $member_id]);
        }
    }
    header("Location: activity_details.php?id={$activity_id}");

    
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تفاصيل الفعالية</title>
  <link rel="stylesheet" href="../css/style.css">
  
  <script>
    function showEditForm(taskId, taskName, taskCost) {
      document.getElementById('edit_task_id').value = taskId;
      document.getElementById('edit_task_name').value = taskName;
      document.getElementById('edit_task_cost').value = taskCost;
      document.getElementById('editTaskForm').style.display = 'block';
      document.getElementById('editTaskForm').scrollIntoView({ behavior: 'smooth' });
    }
  </script>
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
    <h2>تفاصيل الفعالية: <?= htmlspecialchars($activity['activity_name']) ?></h2>
    <p><strong>النادي المسؤول:</strong> <?= htmlspecialchars($activity['club_name']) ?></p>
    <p><strong>مدير الفعالية:</strong> <?= htmlspecialchars($activity['manager_name']) ?></p>
    <a href="activity_team_evaluation.php?id=<?= $activity_id ?>" class="btn"> تقييم فريق الفعالية</a>
    <a href="activity_details.php?id=<?= $activity_id ?>&edit=1" class="btn">التعديل</a>
    <?php if (isset($_GET['edit'])): ?>
    <form method="post">
      <h3>تعديل الفعالية</h3>
      <label>الاسم:<input name="activity_name" value="<?= htmlspecialchars($activity['activity_name']) ?>"></label>
      <label>التاريخ المقترح:<input type="date" name="proposed_date" value="<?= $activity['proposed_date'] ?>"></label>
      <label>التاريخ الفعلي:<input type="date" name="actual_date" value="<?= $activity['actual_date'] ?>"></label>
      <label>وقت البدء المقترح:<input type="datetime-local" name="proposed_start_time" value="<?= date('Y-m-d\TH:i', strtotime($activity['proposed_start_time'])) ?>"></label>
      <label>وقت البدء الفعلي:<input type="datetime-local" name="actual_start_time" value="<?= $activity['actual_start_time'] ? date('Y-m-d\TH:i', strtotime($activity['actual_start_time'])) : '' ?>"></label>
      <label>المدة المقترحة:<input type="number" name="proposed_duration_min" value="<?= $activity['proposed_duration_min'] ?>"></label>
      <label>المدة الفعلية:<input type="number" name="actual_duration_min" value="<?= $activity['actual_duration_min'] ?>"></label>
      <label>التكلفة المقترحة:<input type="number" step="0.01" name="proposed_cost" value="<?= $activity['proposed_cost'] ?>"></label>
      <label>التكلفة الفعلية:<input type="number" step="0.01" name="actual_cost" value="<?= $activity['actual_cost'] ?>"></label>
      <label>الحضور المتوقع:<input type="number" name="proposed_audience_number" value="<?= $activity['proposed_audience_number'] ?>"></label>
      <label>الحضور الفعلي:<input type="number" name="actual_audience_number" value="<?= $activity['actual_audience_number'] ?>"></label>
      <label>الحالة:
        <select name="status">
          <option value="pending" <?= $activity['status']=='pending'?'selected':'' ?>>قادم</option>
          <option value="active"  <?= $activity['status']=='active'?'selected':'' ?>>نشط</option>
          <option value="ended"   <?= $activity['status']=='ended'?'selected':'' ?>>مكتمل</option>
        </select>
      </label>
      <button name="update" class="btn">حفظ التعديلات</button>
    </form>
    <?php endif; ?>
    <div >
      <h3 >معلومات الفعالية</h3>
      <table >
        <thead>
          <tr>
            <th>الاسم</th>
            <th>التاريخ المقترح</th>
            <th>التاريخ الفعلي</th>
            <th>وقت البدء المقترح</th>
            <th>وقت البدء الفعلي</th>
            <th>المدة المقترحة</th>
            <th>المدة الفعلية</th>
            <th>التكلفة المقترحة</th>
            <th>التكلفة الفعلية</th>
            <th>الحضور المتوقع</th>
            <th>الحضور الفعلي</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?= htmlspecialchars($activity['activity_name']) ?></td>
            <td><?= $activity['proposed_date'] ?></td>
            <td><?= $activity['actual_date'] ?? '—' ?></td>
            <td><?= $activity['proposed_start_time'] ?></td>
            <td><?= $activity['actual_start_time'] ?? '—' ?></td>
            <td><?= $activity['proposed_duration_min'] ?> دقيقة</td>
            <td><?= $activity['actual_duration_min'] ?? '—' ?> دقيقة</td>
            <td><?= $activity['proposed_cost'] ?> ريال</td>
            <td><?= $activity['actual_cost'] ?? '—' ?> ريال</td>
            <td><?= $activity['proposed_audience_number'] ?? '—' ?></td>
            <td><?= $activity['actual_audience_number'] ?? '—' ?></td>
            <td><?= $activity['status'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- طلبات التسجيل بالفعالية -->
    <div >
      <h3 >طلبات التسجيل للفعالية</h3>
      <table>
        <thead>
          <tr>
            <th>العضو</th
            ><th>الحالة</th>
            <th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($requests as $req): ?>
            <tr>
              <td><?= htmlspecialchars($req['first_name'].' '.$req['last_name']) ?></td>
              <td>
                <?= $req['status']=='pending'?'بانتظار الموافقة':($req['status']=='approved'?'تمت الموافقة':'مرفوض') ?>
              </td>
              <td>
                <?php if ($req['status']=='pending'): ?>
                  <a class="btn" href="?id=<?= $activity_id ?>&approve=<?= $req['member_id'] ?>">قبول</a>
                  <a class="btn danger" href="?id=<?= $activity_id ?>&reject=<?= $req['member_id'] ?>">رفض</a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

     <br><hr><br>


    <!-- مهام فريق الفعالية -->
    <div  id="tasks">
      <h3 >مهام فريق الفعالية</h3>
      <!-- نموذج إنشاء مهمة جديدة -->
      <form method="post">
        <h4>إنشاء مهمة جديدة</h4>
        <div class="task-form" >
          <input type="text" name="new_task_name" placeholder="اسم المهمة" required>
          <input type="number" step="0.01" name="new_task_cost" placeholder="التكلفة المتوقعة (اختياري)">
          <button name="create_task" class="btn">إنشاء المهمة</button>
        </div>
      </form>
    


      <!-- جدول عرض جميع المهام المتاحة -->
      <h4>المهام المتاحة</h4>
      <table>
        <thead>
          <tr>
            <th>اسم المهمة</th>
            <th>التكلفة</th>
            <th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($available_tasks)): ?>
            <tr><td colspan="3">لا توجد مهام متاحة</td></tr>
          <?php else: ?>
            <?php foreach ($available_tasks as $task): ?>
              <tr>
                <td><?= htmlspecialchars($task['task_name']) ?></td>
                <td><?= $task['task_cost'] ?> ريال</td>
                <td class="actions-cell">
                  <button class="btn" onclick="showEditForm(<?= $task['task_id'] ?>, '<?= htmlspecialchars($task['task_name']) ?>', <?= $task['task_cost'] ?>)">تعديل</button>
                  <a class="btn danger" href="?id=<?= $activity_id ?>&delete_task=<?= $task['task_id'] ?>" onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟ سيتم إلغاء إسنادها من أي عضو.')">حذف</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
     
      <!-- فورمه تعديل المهمة) -->
      <form method="post" id="editTaskForm" style="display: none;">
        <h4>تعديل المهمة</h4>
        <input type="hidden" name="task_id" id="edit_task_id">
        <div class="task-form">
          <input type="text" name="edit_task_name" id="edit_task_name" placeholder="اسم المهمة" required>
          <input type="number" step="0.01" name="edit_task_cost" id="edit_task_cost" placeholder="التكلفة المتوقعة">
          <button name="update_task" class="btn">تحديث المهمة</button>
          <button type="button" class="btn danger" onclick="document.getElementById('editTaskForm').style.display='none'">إلغاء</button>
        </div>
      </form>
      
      <!-- فورمة اسناد المهمة -->
      <form method="post" >
        <h4>إسناد مهمة لفريق الفعالية</h4>
        <div class="task-form">
          <select name="member_id" required>
            <option value="">-- اختر عضوا --</option>
           <?php foreach ($approved_members as $member): ?>
  <option value="<?= $member['member_id'] ?>">
    <?= htmlspecialchars($member['member_name']) ?>
  </option>
<?php endforeach; ?>
          </select>
          <select name="task_id" required>
            <option value="">-- اختر مهمة --</option>
            <?php foreach ($available_tasks as $task): ?>
              <option value="<?= $task['task_id'] ?>">
                <?= htmlspecialchars($task['task_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button name="add_task" class="btn">إسناد المهمة</button>
        </div>
      </form>
      
      <!--جدول عرض المهام  -->
      <h4>المهام المسندة</h4>
      <table>
        <thead>
          <tr>
            <th>المهمة</th>
            <th>التكلفة</th>
            <th>العضو المسؤول</th>
            <th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($team_tasks)): ?>
            <tr><td colspan="4">لا توجد مهام مسندة بعد</td></tr>
          <?php else: ?>
            <?php foreach ($team_tasks as $task): 
              $is_manager = $activity['activity_manager_id'] == $task['member_id'];
            ?>
              <tr <?= $is_manager ? 'class="manager-row"' : '' ?>>
                <td>
                  <?= $task['task_name'] ? htmlspecialchars($task['task_name']) : '—' ?>
                </td>
                <td>
                  <?= $task['task_cost'] ? $task['task_cost'] . ' ريال' : '—' ?>
                </td>
                <td>
                  <?= htmlspecialchars($task['member_name']) ?>
                  <?php if ($is_manager): ?>
                    <span class="manager-label">مدير</span>
                  <?php endif; ?>
                </td>
                <td class="actions-cell">
                  <?php if ($task['task_id']): ?>
                    <a class="btn danger" href="?id=<?= $activity_id ?>&del_assignment=<?= $task['member_id'] ?>" onclick="return confirm('هل تريد الحذف')">حذف </a>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <!-- تقييمات الأعضاء للفعالية -->
    <div >
      <h3 >تقييمات الأعضاء للفعالية</h3>
      <?php if ($avgScore): ?>
        <div>متوسط تقييم الفعالية: <?= round($avgScore,2) ?> ⭐</div>
      <?php endif; ?>
      <table>
        <thead>
        <tr>
          <th>العضو</th>
          <th>التقييم</th>
          <th>الملاحظة</th>
          <th>التاريخ</th>
        </tr>
      </thead>
        <tbody>
          <?php if ($feedbacks): foreach($feedbacks as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['member_name']) ?></td>
              <td><?= $row['feedback_score']!==null?$row['feedback_score'].'⭐':'—' ?></td>
              <td><?= htmlspecialchars($row['content']) ?></td>
              <td><?= $row['created_at'] ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="4">لا توجد تقييمات لهذه الفعالية.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>