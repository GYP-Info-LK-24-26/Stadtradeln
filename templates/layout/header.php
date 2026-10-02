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
    <meta name="color-scheme" content="light dark">
    <style>
        /* Kritische Grundfarben inline, damit schon der erste Frame (vor app.css) im
           richtigen Farbschema gemalt wird – verhindert den weißen Blitz im Dark Mode. */
        html { background: #F4F7F5; color: #0F2419; }
        @media (prefers-color-scheme: dark) { html { background: #0A120E; color: #E6EFE9; } }
    </style>
    <title><?= isset($title) ? htmlspecialchars($title) . ' · GYP-Radeln' : 'GYP-Radeln' ?></title>
    <?php require __DIR__ . '/pwa-head.php'; ?>
    <link rel="preload" href="/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= View::asset('/css/app.css') ?>">
    <!-- Leeres Inline-Script: zwingt Firefox, vor dem ersten Rendern auf app.css zu warten (verhindert FOUC) -->
    <script>0</script>
    <script src="<?= View::asset('/js/app.js') ?>" defer></script>
</head>
<body class="<?= $bodyClasses ?>">
    <a class="skip-link" href="#main">Zum Inhalt springen</a>
    <?php require __DIR__ . '/nav.php'; ?>
    <main id="main" class="main">
