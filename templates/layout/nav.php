<?php

use App\Core\Session;

Session::start();
$isLoggedIn = Session::isLoggedIn();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($isLoggedIn) {
    $navItems = [
        '/dashboard' => 'Dashboard',
        '/leaderboard' => 'Rangliste',
        '/team' => 'Team',
    ];

    $accountItems = [
        '/settings' => 'Einstellungen',
        '/logout' => 'Abmelden',
    ];

    $userName = Session::getUsername() ?? 'Profil';
} else {
    $navItems = [
        '/leaderboard' => 'Rangliste',
    ];

    $authItems = [
        '/login' => 'Anmelden',
        '/register' => 'Registrieren',
    ];
}
?>

<nav>
    <ul class="topnav">
        <?php if ($isLoggedIn): ?>
            <li class="account-dropdown">
                <a href="#" class="dropdown-toggle"><?= htmlspecialchars($userName) ?></a>
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
        <?php endif; ?>
        <?php foreach ($navItems as $path => $label): ?>
            <li>
                <a href="<?= $path ?>"
                   class="<?= $currentPath === $path ? 'active' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            </li>
        <?php endforeach; ?>
        <?php if (!$isLoggedIn): ?>
            <?php foreach ($authItems as $path => $label): ?>
                <li>
                    <a href="<?= $path ?>"
                       class="<?= $currentPath === $path ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</nav>
