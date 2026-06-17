<?php
// main/ajax/notifications/MarkRead.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { header('Location: ../../login.php'); exit; }

$user_id = authUser()['id'];
$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')->execute([$user_id]);
header('Location: ../../index.php');
