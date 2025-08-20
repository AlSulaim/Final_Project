<?php
$host = 'localhost';
$db   = 'SmartActivity';  // ← حط اسم قاعدة البيانات الصحيحة هنا
$user = 'root';            // ← غالبًا root في XAMPP
$pass = '';                // ← فاضي إذا ما غيرت الباسوورد


$dsn = "mysql:host=$host;dbname=$db";

$pdo = new PDO($dsn, $user, $pass);

