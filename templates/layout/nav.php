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

    $userName = Session::getDisplayName() ?? 'Profil';
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

<nav class="site-nav">
    <a href="/" class="nav-brand">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <path d="M5 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 6a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7-8h3l2 4h-4l-1-4zm-2 0L8 8H5V6h4l1-2zm3 4l2 4H9l-1-4h5z"/>
        </svg>
        Stadtradeln
    </a>

    <button class="nav-toggle" id="nav-toggle" aria-label="Navigation öffnen" aria-expanded="false" aria-controls="topnav">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <ul class="topnav" id="topnav" role="list">
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

<script>
(function () {
    var toggle = document.getElementById('nav-toggle');
    var nav    = document.getElementById('topnav');
    if (!toggle || !nav) return;

    function closeMenu() {
        nav.classList.remove('nav-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.classList.remove('is-open');
    }

    toggle.addEventListener('click', function () {
        var isOpen = nav.classList.toggle('nav-open');
        this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        this.classList.toggle('is-open', isOpen);
    });

    // Close when a navigation link is clicked (except dropdown toggle)
    nav.querySelectorAll('a:not(.dropdown-toggle)').forEach(function (a) {
        a.addEventListener('click', closeMenu);
    });

    // Close when clicking outside the nav
    document.addEventListener('click', function (e) {
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
            closeMenu();
        }
    });
})();
</script>
