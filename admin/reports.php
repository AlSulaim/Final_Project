<?php
session_start();

require_once '../database/db.php';

// نسحب البيانات الثابتة للفلاتر (أدمن يشوف الكل)
$allActivities = $pdo->query("SELECT activity_id, activity_name FROM activity ORDER BY proposed_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$teamMembers   = $pdo->query("SELECT member_id, CONCAT(first_name, ' ', last_name) AS member_name FROM members")->fetchAll(PDO::FETCH_ASSOC);
$kpis          = $pdo->query("SELECT kpi_id, kpi_name, measurement, note FROM activity_kpi WHERE activity_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
$teamKpis      = $pdo->query("SELECT team_kpi_id, kpi_name FROM activity_team_kpi")->fetchAll(PDO::FETCH_ASSOC);

// نقرا قيم الفلاتر من الرابط، ولو فاضية نخليها all
$activity_filter  = isset($_GET['activity_filter'])  ? $_GET['activity_filter']  : 'all';
$kpi_filter       = isset($_GET['kpi_filter'])       ? $_GET['kpi_filter']       : 'all';
$member_filter    = isset($_GET['member_filter'])    ? $_GET['member_filter']    : 'all';
$team_kpi_filter  = isset($_GET['team_kpi_filter'])  ? $_GET['team_kpi_filter']  : 'all';

// نبني شرط WHERE ديناميكي لقياسات الفعاليات
$whereClause = "WHERE 1=1"; // نخليها كذا عشان نضيف شروط بسهولة
if ($activity_filter != 'all') {
  $whereClause .= " AND a.activity_id = " . (int) $activity_filter;
}
if ($kpi_filter != 'all') {
  $whereClause .= " AND k.kpi_id = " . (int) $kpi_filter;
}

// نسحب قياسات الفعاليات بعد التصفية
$measurements = $pdo->query("
  SELECT  
      am.activity_id,
      am.kpi_id,
      am.measure_date,
      a.activity_name,
      k.kpi_name,
      k.measurement,
      k.note       AS note_type,
      am.result,
      am.note      AS note_value,
      a.proposed_date,
      a.actual_date,
      a.proposed_start_time,
      a.actual_start_time,
      a.proposed_cost,
      a.actual_cost,
      a.proposed_audience_number,
      a.actual_audience_number
  FROM activity_measurement am
  JOIN activity a ON am.activity_id = a.activity_id
  JOIN activity_kpi k ON am.kpi_id = k.kpi_id
  $whereClause
  ORDER BY am.measure_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// نبني شرط WHERE ديناميكي لتقييمات أعضاء الفريق
$team_where = "WHERE 1=1";
if ($activity_filter != 'all') {
  $team_where .= " AND a.activity_id = " . (int) $activity_filter;
}
if ($member_filter != 'all') {
  $team_where .= " AND tm.member_id = " . (int) $member_filter;
}
if ($team_kpi_filter != 'all') {
  $team_where .= " AND k.team_kpi_id = " . (int) $team_kpi_filter;
}

// نسحب تقييمات الأعضاء
$teamEval = $pdo->query("
  SELECT
      tm.activity_id,
      tm.measure_date,
      tm.member_id,
      tm.team_kpi_id,
      a.activity_name,
      CONCAT(m.first_name,' ',m.last_name) AS member_name,
      k.kpi_name,
      tm.result
  FROM activity_team_measurement tm
  JOIN activity a ON tm.activity_id = a.activity_id
  JOIN members m  ON tm.member_id   = m.member_id
  JOIN activity_team_kpi k ON tm.team_kpi_id = k.team_kpi_id
  $team_where
  ORDER BY tm.measure_date DESC
");
$teamRows = $teamEval->fetchAll(PDO::FETCH_ASSOC);

// نجهز التوصيات (نكتب رسائل سريعة مفيدة)
$recommendations = [];
$teamRecommendations = [];

foreach ($measurements as $m) {
  $rec = [
    'activity_name' => $m['activity_name'],
    'kpi_name'      => $m['kpi_name'],
    'message'       => '',
    'type'          => '', // warning أو praise
    'measurement'   => $m['measurement']
  ];

  switch ($m['measurement']) {
        case 'date':
            if ($m['actual_date'] && $m['proposed_date']) {
                $diff_days = round((strtotime($m['actual_date']) - strtotime($m['proposed_date']))/86400, 2);
                if ($diff_days > 0) {
                    $rec['message'] = "تأخرت الفعالية بمقدار {$diff_days} يوم. يُنصح بتحسين التخطيط الزمني.";
                    $rec['type'] = 'warning';
                } else {
                    $rec['message'] = "تم إنجاز الفعالية قبل الموعد المحدد بمقدار " . abs($diff_days) . " يوم. عمل ممتاز!";
                    $rec['type'] = 'praise';
                }
            }
            break;
        case 'time':
            if ($m['actual_start_time'] && $m['proposed_start_time']) {
                $diff_mins = round((strtotime($m['actual_start_time']) - strtotime($m['proposed_start_time']))/60, 2);
                if ($diff_mins > 0) {
                    $rec['message'] = "تأخرت الفعالية بمقدار {$diff_mins} دقيقة. يُنصح بتحسين التنظيم.";
                    $rec['type'] = 'warning';
                } else {
                    $rec['message'] = "تم البدء في الفعالية قبل الموعد المحدد بمقدار " . abs($diff_mins) . " دقيقة. عمل ممتاز!";
                    $rec['type'] = 'praise';
                }
            }
            break;
        case 'cost':
            $diff = ($m['actual_cost'] ?? 0) - ($m['proposed_cost'] ?? 0);
            if ($diff > 0) {
                $rec['message'] = "تجاوزت التكلفة المقدرة بمقدار {$diff} ر.س. يُنصح بمراجعة الميزانية.";
                $rec['type'] = 'warning';
            } else {
                $rec['message'] = "تم إنجاز الفعالية بتكلفة أقل من المقدرة بمقدار " . abs($diff) . " ر.س. عمل ممتاز!";
                $rec['type'] = 'praise';
            }
            break;
        case 'audience':
            $diff = ($m['actual_audience_number'] ?? 0) - ($m['proposed_audience_number'] ?? 0);
            if ($diff < 0) {
                $rec['message'] = "كان عدد الحضور أقل من المتوقع بمقدار " . abs($diff) . " شخص. يُنصح بتحسين الدعاية.";
                $rec['type'] = 'warning';
            } else {
                $rec['message'] = "تجاوز عدد الحضور المتوقع بمقدار {$diff} شخص. عمل ممتاز!";
                $rec['type'] = 'praise';
            }
            break;
        case 'feedback':
            $score = floatval($m['result']);
            if ($score < 3.25) {
                $rec['message'] = "التقييم العام للفعالية كان منخفضًا ({$score}/5). يُنصح بتحسين جودة الفعالية.";
                $rec['type'] = 'warning';
            } else {
                $rec['message'] = "التقييم العام للفعالية كان ممتازًا ({$score}/5). عمل ممتاز!";
                $rec['type'] = 'praise';
            }
            break;
        default:
            if ($m['note_type'] === 'boolean' && $m['note_value'] === '0') {
                $rec['message'] = "لم يتم تحقيق هذا المؤشر. يُنصح بمراجعة أسباب عدم الإنجاز.";
                $rec['type'] = 'warning';
            } elseif ($m['note_type'] === 'boolean' && $m['note_value'] === '1') {
                $rec['message'] = "تم تحقيق هذا المؤشر بنجاح. عمل ممتاز!";
                $rec['type'] = 'praise';
            }
    }
    
    if (!empty($rec['message'])) {
        $recommendations[] = $rec;
    }
}

foreach ($teamRows as $r) {
    $rec = [
        'member_name' => $r['member_name'],
        'activity_name' => $r['activity_name'],
        'kpi_name' => $r['kpi_name'],
        'message' => '',
        'type' => '' // warning أو praise
    ];
    
    // إذا كان المؤشر رقمي
    if (is_numeric($r['result'])) {
        if ($r['result'] < 3) { 
            $rec['message'] = "تقييم أداء {$r['member_name']} في مؤشر '{$r['kpi_name']}' منخفض ({$r['result']}/5). يُنصح بتقديم الدعم والتدريب.";
            $rec['type'] = 'warning';
        } else {
            $rec['message'] = "تقييم أداء {$r['member_name']} في مؤشر '{$r['kpi_name']}' ممتاز ({$r['result']}/5). عمل ممتاز!";
            $rec['type'] = 'praise';
        }
    } 
    // إذا كان نعم/لا
    else {
        if ($r['result'] == 0) {
            $rec['message'] = "لم يحقق {$r['member_name']} مؤشر '{$r['kpi_name']}'. يُنصح بمراجعة الأداء.";
            $rec['type'] = 'warning';
        } else {
            $rec['message'] = "{$r['member_name']} حقق مؤشر '{$r['kpi_name']}' بنجاح. عمل ممتاز!";
            $rec['type'] = 'praise';
        }
    }
    
    $teamRecommendations[] = $rec;
}

// حذف قياس فعالية 
if (isset($_GET['delete_activity_id'], $_GET['delete_kpi_id'], $_GET['delete_date'])) {
  $delStmt = $pdo->prepare("DELETE FROM activity_measurement WHERE activity_id = ? AND kpi_id = ? AND measure_date = ?");
  $delStmt->execute([
    (int) $_GET['delete_activity_id'],
    (int) $_GET['delete_kpi_id'],
    $_GET['delete_date']
  ]);
  header("Location: reports.php?activity_filter={$activity_filter}&kpi_filter={$kpi_filter}&member_filter={$member_filter}&team_kpi_filter={$team_kpi_filter}");
  exit;
}

// حذف تقييم عضو فريق
if (isset($_GET['delete_team_activity_id'], $_GET['delete_member_id'], $_GET['delete_team_kpi_id'], $_GET['delete_date'])) {
  $del = $pdo->prepare("DELETE FROM activity_team_measurement WHERE activity_id = ? AND member_id= ? AND team_kpi_id = ? AND measure_date = ?");
  $del->execute([
    (int) $_GET['delete_team_activity_id'],
    (int) $_GET['delete_member_id'],
    (int) $_GET['delete_team_kpi_id'],
    $_GET['delete_date']
  ]);
  header("Location: reports.php?activity_filter={$activity_filter}&kpi_filter={$kpi_filter}&member_filter={$member_filter}&team_kpi_filter={$team_kpi_filter}");
  exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>قياس المؤشرات</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <div class="sidebar">
    <h3 class="sidebar-title">لوحة التحكم</h3>
    <a href="dashboard.php">الرئيسية</a>
    <a href="members.php">الأعضاء</a>
    <a href="clubs.php">الأندية</a>
    <a href="activities.php">الفعاليات</a>
    <a href="kpis.php">المؤشرات</a>
    <a class="active" href="reports.php">التقارير</a>
    <a href="logout.php" onclick="return confirm('متأكد تبي تسجل خروج؟');">خروج</a>
  </div>

  <div class="main-content">
    <h1><strong>التقارير</strong></h1>

    <div class="filters">
      <form method="GET">
        <div>
          <h3> الفعاليات</h3>
          <label>الفعالية:
            <select name="activity_filter" class="form-select">
              <option value="all" <?php if ($activity_filter == 'all') { echo 'selected'; } ?>>كل الفعاليات</option>
              <?php foreach ($allActivities as $activity): ?>
                <option value="<?= $activity['activity_id'] ?>" <?php if ($activity_filter == $activity['activity_id']) { echo 'selected'; } ?>>
                  <?= htmlspecialchars($activity['activity_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>المؤشر:
            <select name="kpi_filter">
              <option value="all" <?php if ($kpi_filter == 'all') { echo 'selected'; } ?>>كل المؤشرات</option>
              <?php foreach ($kpis as $k): ?>
                <option value="<?= $k['kpi_id'] ?>" <?php if ($kpi_filter == $k['kpi_id']) { echo 'selected'; } ?>>
                  <?= htmlspecialchars($k['kpi_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <div>
          <h3>الأعضاء</h3>
          <label>عضو الفريق:
            <select name="member_filter">
              <option value="all" <?php if ($member_filter == 'all') { echo 'selected'; } ?>>كل الأعضاء</option>
              <?php foreach ($teamMembers as $m): ?>
                <option value="<?= $m['member_id'] ?>" <?php if ($member_filter == $m['member_id']) { echo 'selected'; } ?>>
                  <?= htmlspecialchars($m['member_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>مؤشر الفريق:
            <select name="team_kpi_filter">
              <option value="all" <?php if ($team_kpi_filter == 'all') { echo 'selected'; } ?>>كل مؤشرات الفريق</option>
              <?php foreach ($teamKpis as $tk): ?>
                <option value="<?= $tk['team_kpi_id'] ?>" <?php if ($team_kpi_filter == $tk['team_kpi_id']) { echo 'selected'; } ?>>
                  <?= htmlspecialchars($tk['kpi_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <button type="submit" class="btn">ابحث</button>
      </form>
    </div>

    <div>
      <h2>الفعاليات</h2>
    </div>

    <?php if ($measurements): ?>
      <table>
        <thead>
          <tr>
            <th>التاريخ</th>
            <th>الفعالية</th>
            <th>المؤشر</th>
            <th>المتوقع</th>
            <th>الفعلي</th>
            <th>النتيجة</th>
            <th>الحالة</th>
            <th>إجراء</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($measurements as $m):
            // ننسق العرض حسب نوع القياس
            $prop = '—';
            $act = '—';
            $resultDisplay = '—';
            $status = '';

            switch ($m['measurement']) {
              case 'date':
                if ($m['proposed_date'] && $m['actual_date']) {
                  $prop_date = new DateTime($m['proposed_date']);
                  $act_date  = new DateTime($m['actual_date']);
                  $diff_days = $prop_date->diff($act_date)->days;

                  $prop = $m['proposed_date'];
                  $act  = $m['actual_date'];

                  if ($act_date > $prop_date) {
                    $resultDisplay = "<span class='bad'>+{$diff_days} يوم تأخير</span>";
                    $status = 'status-bad';
                  } else {
                    $resultDisplay = "<span class='good'>-{$diff_days} يوم مبكر</span>";
                    $status = 'status-good';
                  }
                }
                break;

              case 'time':
                if ($m['proposed_start_time'] && $m['actual_start_time']) {
                  $prop = $m['proposed_start_time'];
                  $act  = $m['actual_start_time'];

                  $diff_mins = (strtotime($m['actual_start_time']) - strtotime($m['proposed_start_time'])) / 60;

                  if ($diff_mins > 0) {
                    $resultDisplay = "<span class='bad'>+{$diff_mins} دقيقة تأخير</span>";
                    $status = 'status-bad';
                  } else {
                    $resultDisplay = "<span class='good'>" . abs($diff_mins) . " دقيقة مبكر</span>";
                    $status = 'status-good';
                  }
                }
                break;

              case 'cost':
                $prop = $m['proposed_cost'] . ' ر.س';
                $act  = $m['actual_cost']   . ' ر.س';

                $diff = $m['actual_cost'] - $m['proposed_cost'];
                if ($diff > 0) {
                  $resultDisplay = "<span class='bad'>+{$diff} ر.س زيادة</span>";
                  $status = 'status-bad';
                } else {
                  $resultDisplay = "<span class='good'>" . abs($diff) . " ر.س توفير</span>";
                  $status = 'status-good';
                }
                break;

              case 'audience':
                $prop = $m['proposed_audience_number'] . ' شخص';
                $act  = $m['actual_audience_number']   . ' شخص';

                $diff = $m['actual_audience_number'] - $m['proposed_audience_number'];
                if ($diff < 0) {
                  $resultDisplay = "<span class='bad'>" . abs($diff) . " شخص نقص</span>";
                  $status = 'status-bad';
                } else {
                  $resultDisplay = "<span class='green'>+{$diff} شخص زيادة</span>";
                  $status = 'status-good';
                }
                break;

              case 'feedback':
                $prop = '5';
                $act  = htmlspecialchars($m['result']);
                $score = floatval($m['result']);

                if ($score < 3.00) {
                  $resultDisplay = "<span class='bad'>{$score}/5</span>";
                  $status = 'status-bad';
                } else {
                  $resultDisplay = "<span class='green'>{$score}/5</span>";
                  $status = 'status-good';
                }
                break;

              default:
                if ($m['note_type'] === 'boolean') {
                  $prop = 'نعم/لا';
                  if ($m['note_value'] === '1') {
                    $act = 'نعم';
                    $resultDisplay = $act;
                    $status = 'status-good';
                  } else {
                    $act = 'لا';
                    $resultDisplay = $act;
                    $status = 'status-bad';
                  }
                } elseif ($m['note_type'] === 'text') {
                  $prop = '—';
                  $act  = htmlspecialchars($m['note_value']);
                  $resultDisplay = $act;
                  $status = 'status-good';
                }
            }
          ?>
            <tr>
              <td><?= htmlspecialchars($m['measure_date']) ?></td>
              <td><?= htmlspecialchars($m['activity_name']) ?></td>
              <td><?= htmlspecialchars($m['kpi_name']) ?></td>
              <td><?= $prop ?></td>
              <td><?= $act ?></td>
              <td><?= $resultDisplay ?></td>
              <td>
                <?php if ($status): ?>
                  <span class="status-indicator <?= $status ?>"></span>
                  <?php
                    if ($status == 'status-good') {
                      echo 'ممتاز';
                    } else {
                      echo 'تحذير';
                    }
                  ?>
                <?php endif; ?>
              </td>
              <td>
                <a class="btn danger"
                   href="?activity_filter=<?= $activity_filter ?>&kpi_filter=<?= $kpi_filter ?>&member_filter=<?= $member_filter ?>&team_kpi_filter=<?= $team_kpi_filter ?>&delete_activity_id=<?= $m['activity_id'] ?>&delete_kpi_id=<?= $m['kpi_id'] ?>&delete_date=<?= $m['measure_date'] ?>"
                   onclick="return confirm('متأكد تبي تحذف هذا القياس؟')">
                  حذف
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>ما فيه قياسات مسجلة حالياً</p>
    <?php endif; ?>

    <?php if (!empty($recommendations)): ?>
      <div class="recommendations">
        <h2>التوصيات</h2>
        <?php foreach ($recommendations as $rec): ?>
          <div class="recommendation <?= $rec['type'] ?>">
            <span class="status-indicator <?php if ($rec['type'] == 'praise') { echo 'status-good'; } else { echo 'status-bad'; } ?>"></span>
            <strong><?= htmlspecialchars($rec['activity_name']) ?> - <?= htmlspecialchars($rec['kpi_name']) ?>:</strong>
            <?= $rec['message'] ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div>
      <h2>أعضاء الفعالية</h2>
    </div>

    <?php if ($teamRows): ?>
      <table>
        <thead>
          <tr>
            <th>التاريخ</th>
            <th>الفعالية</th>
            <th>العضو</th>
            <th>المؤشر</th>
            <th>النتيجة</th>
            <th>الحالة</th>
            <th>إجراء</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($teamRows as $r):
            $status = '';
            $resultDisplay = $r['result'];

            // نحدد طريقة عرض النتيجة حسب كونها رقم أو بولين
            if (is_numeric($r['result'])) {
              $score = floatval($r['result']);
              if ($score < 3) {
                $resultDisplay = "<span class='bad'>{$score}/5</span>";
                $status = 'status-bad';
              } else {
                $resultDisplay = "<span class='good'>{$score}/5</span>";
                $status = 'status-good';
              }
            } else {
              if ($r['result'] == 0) {
                $resultDisplay = "<span class='bad'>لا</span>";
                $status = 'status-bad';
              } else {
                $resultDisplay = "<span class='good'>نعم</span>";
                $status = 'status-good';
              }
            }
          ?>
            <tr>
              <td><?= htmlspecialchars($r['measure_date']) ?></td>
              <td><?= htmlspecialchars($r['activity_name']) ?></td>
              <td><?= htmlspecialchars($r['member_name']) ?></td>
              <td><?= htmlspecialchars($r['kpi_name']) ?></td>
              <td><?= $resultDisplay ?></td>
              <td>
                <?php if ($status): ?>
                  <span class="status-indicator <?= $status ?>"></span>
                  <?php
                    if ($status == 'status-good') {
                      echo 'ممتاز';
                    } else {
                      echo 'تحذير';
                    }
                  ?>
                <?php endif; ?>
              </td>
              <td>
                <a class="btn danger"
                   href="?activity_filter=<?= $activity_filter ?>&kpi_filter=<?= $kpi_filter ?>&member_filter=<?= $member_filter ?>&team_kpi_filter=<?= $team_kpi_filter ?>&delete_team_activity_id=<?= $r['activity_id'] ?>&delete_member_id=<?= $r['member_id'] ?>&delete_team_kpi_id=<?= $r['team_kpi_id'] ?>&delete_date=<?= $r['measure_date'] ?>"
                   onclick="return confirm('متأكد تبي تحذف هذا التقييم؟')">
                  حذف
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>ما فيه تقييمات لأعضاء الفريق حالياً</p>
    <?php endif; ?>

    <?php if (!empty($teamRecommendations)): ?>
      <div class="recommendations">
        <h2>توصيات أعضاء الفريق</h2>
        <?php foreach ($teamRecommendations as $rec): ?>
          <div class="recommendation <?= $rec['type'] ?>">
            <span class="status-indicator <?php if ($rec['type'] == 'praise') { echo 'status-good'; } else { echo 'status-bad'; } ?>"></span>
            <strong><?= htmlspecialchars($rec['member_name']) ?> - <?= htmlspecialchars($rec['kpi_name']) ?>:</strong>
            <?= $rec['message'] ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
