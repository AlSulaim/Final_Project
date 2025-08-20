<?php
session_start();
session_unset(); // يمسح كل متغيرات الجلسة
session_destroy(); // ينهي الجلسة بالكامل

header("Location: ../login.php");
exit();

