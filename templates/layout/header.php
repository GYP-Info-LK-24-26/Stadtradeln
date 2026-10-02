<?php
/**
 * Gemeinsamer Seitenkopf für alle Seiten.
 *
 * Variablen:
 *   $title  – Seitentitel (ohne " · GYP-Radeln")
 *   $layout – 'app' (Standard), 'auth' (zentrierte Karte) oder 'landing'
 */

use App\Core\Session;
use App\Core\View;

Session::start();
$isLoggedIn = Session::isLoggedIn();
$layout = $layout ?? 'app';
$bodyClasses = 'layout-' . $layout . ($isLoggedIn ? ' has-tabbar' : '');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= isset($title) ? htmlspecialchars($title) . ' · GYP-Radeln' : 'GYP-Radeln' ?></title>
    <?php require __DIR__ . '/pwa-head.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= View::asset('/css/app.css') ?>">
    <script src="<?= View::asset('/js/app.js') ?>" defer></script>
</head>
<body class="<?= $bodyClasses ?>">
    <a class="skip-link" href="#main">Zum Inhalt springen</a>
    <?php require __DIR__ . '/nav.php'; ?>
    <main id="main" class="main">
