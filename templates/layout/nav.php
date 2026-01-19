<?php

use App\Core\Session;

$navItems = [
    '/dashboard' => 'Dashboard',
    '/leaderboard' => 'Rangliste',
    '/team' => 'Team',
];

$accountItems = [
    '/settings' => 'Einstellungen',
    '/logout' => 'Abmelden',
];

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$userName = Session::getUsername() ?? 'Profil';
?>

<nav>
    <ul class="topnav">
        <li class="account-dropdown">
            <a href="#" class="dropdown-toggle">👤 <?= htmlspecialchars($userName) ?> ▼</a>
            <ul class="dropdown-menu">
                <?php foreach ($accountItems as $path => $label): ?>
                    <li>
                        <a href="<?= $path ?>"
                           class="<?= $currentPath === $path ? 'active' : '' ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php foreach ($navItems as $path => $label): ?>
            <li>
                <a href="<?= $path ?>"
                   class="<?= $currentPath === $path ? 'active' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
