<?php

use App\Core\Session;

$navItems = [
    '/dashboard' => 'Dashboard',
    '/leaderboard' => 'Rangliste',
    '/team' => 'Team',
    '/team/join' => 'Team erstellen/beitreten',
    '/logout' => 'Abmelden'
];

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<nav>
    <ul class="topnav">
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
