<?php

use App\Core\Csrf;
use App\Core\Icon;
use App\Core\Session;
use App\Core\View;

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isActive = function (string $path) use ($currentPath): bool {
    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
};

$navItems = $isLoggedIn
    ? [
        '/dashboard' => ['Dashboard', 'dashboard'],
        '/leaderboard' => ['Rangliste', 'trophy'],
        '/team' => ['Team', 'users'],
    ]
    : [
        '/leaderboard' => ['Rangliste', 'trophy'],
    ];

$userName = Session::getDisplayName() ?? 'Profil';
?>
<header class="topbar">
    <div class="topbar-inner container">
        <a href="<?= $isLoggedIn ? '/dashboard' : '/' ?>" class="brand">
            <span class="brand-mark"><?= Icon::svg('bike') ?></span>
            <span class="brand-name">GYP-Radeln</span>
        </a>

        <nav class="topnav" aria-label="Hauptnavigation">
            <?php foreach ($navItems as $path => [$label, $icon]): ?>
                <a href="<?= $path ?>" class="topnav-link<?= $isActive($path) ? ' is-active' : '' ?>"
                   <?= $isActive($path) ? 'aria-current="page"' : '' ?>>
                    <?= Icon::svg($icon) ?>
                    <span><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="topbar-actions">
            <?php if ($isLoggedIn): ?>
                <div class="menu" data-menu>
                    <button type="button" class="user-button" data-menu-toggle
                            aria-haspopup="menu" aria-expanded="false" aria-controls="userMenu">
                        <?= View::avatar($userName, 'sm') ?>
                        <span class="user-button-name"><?= htmlspecialchars($userName) ?></span>
                        <?= Icon::svg('chevron-down', 'icon user-button-chevron') ?>
                    </button>
                    <div class="menu-panel" id="userMenu" role="menu">
                        <div class="menu-header">
                            <?= View::avatar($userName) ?>
                            <div class="menu-header-text">
                                <strong><?= htmlspecialchars($userName) ?></strong>
                                <span>Angemeldet</span>
                            </div>
                        </div>
                        <a href="/settings" class="menu-item<?= $isActive('/settings') ? ' is-active' : '' ?>" role="menuitem">
                            <?= Icon::svg('settings') ?> Einstellungen
                        </a>
                        <a href="/faq" class="menu-item<?= $isActive('/faq') ? ' is-active' : '' ?>" role="menuitem">
                            <?= Icon::svg('info') ?> FAQ
                        </a>
                        <div class="menu-separator" role="separator"></div>
                        <form method="post" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="menu-item menu-item-danger" role="menuitem">
                                <?= Icon::svg('log-out') ?> Abmelden
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm<?= $isActive('/login') ? ' is-active' : '' ?>">Anmelden</a>
                <a href="/register" class="btn btn-primary btn-sm">Registrieren</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($isLoggedIn): ?>
    <nav class="tabbar" aria-label="Hauptnavigation (mobil)">
        <?php foreach ($navItems + ['/settings' => ['Profil', 'user']] as $path => [$label, $icon]): ?>
            <a href="<?= $path ?>" class="tabbar-link<?= $isActive($path) ? ' is-active' : '' ?>"
               <?= $isActive($path) ? 'aria-current="page"' : '' ?>>
                <span class="tabbar-icon"><?= Icon::svg($icon) ?></span>
                <span class="tabbar-label"><?= $label ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
