<?php
require_once '../database/db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT *FROM Activity WHERE activity_id = ?");
$stmt->execute([$activity_id]);
$activity = $stmt->fetch();

if (!$activity) {
    die("الفعالية غير موجودة.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = trim($_POST['feedback']);
    $score = isset($_POST['feedback_score']) ? intval($_POST['feedback_score']) : null;

    if ($score >= 1 && $score <= 5 && !empty($feedback)) {
        $stmt = $pdo->prepare("INSERT INTO Feedback (activity_id, member_id, content, feedback_score)
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$activity_id, $member_id, $feedback, $score]);
        header("Location: my_activities.php");
        exit();
    } else {
        $error = "يرجى تعبئة الملاحظة واختيار تقييم من 1 إلى 5.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تقييم النشاط</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>

    .main-content {
      max-width: 500px;
      background: white;
      margin: 80px auto;
      padding: 30px 35px;
      border-radius: 15px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      text-align: center;
    }

    textarea, select {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
    }

    
    a {
      display: block;
      margin-top: 25px;
      color: #007bff;
      text-decoration: none;
    }

  </style>
</head>
<body>

  <div class="main-content">
    <h2>تقييمك للفعالية: <span style="color:#111"><?= htmlspecialchars($activity['activity_name']) ?></span></h2>

    <?php if (isset($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>فضلا قيم الفعالية (من 1 إلى 5):</label>
      <select name="feedback_score" required>
        <option value="">-- اختر --</option>
        <option value="5">5 - ممتاز ⭐⭐⭐⭐⭐</option>
        <option value="4">4 - جيد جداً ⭐⭐⭐⭐</option>
        <option value="3">3 - جيد ⭐⭐⭐</option>
        <option value="2">2 - مقبول ⭐⭐</option>
        <option value="1">1 - ضعيف ⭐</option>
      </select>
      <br><br>
      <label style="margin-top:15px;">ملاحظتك:</label>
      <textarea name="feedback" placeholder="هل هناك اي ملاحظات او اقتراحات ؟" required></textarea>
      <br><br>
      <button class="btn idle" type="submit"><b>إرسال التقييم</b></button>
    </form>

    <a href="my_activities.php">الرجوع</a>
  </div>

</body>
</html>
