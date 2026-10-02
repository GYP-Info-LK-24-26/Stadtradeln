<?php
use App\Core\Icon;
use App\Core\Session;

$title = 'Gemeinsam radeln für das Klima';
$layout = 'landing';
require __DIR__ . '/../layout/header.php';

// Dekoratives Aktivitätsraster für die App-Vorschau
$previewLevels = [0, 2, 1, 0, 3, 4, 2, 1, 0, 2, 4, 3, 0, 1];
?>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="pill reveal">
                <span class="pill-dot"><?= Icon::svg('leaf') ?></span>
                Stadtradeln am Gymnasium Penzberg
            </span>
            <h1 class="reveal" style="--i: 1">Gemeinsam radeln<br>für das <em>Klima.</em></h1>
            <p class="hero-lead reveal" style="--i: 2">
                Trag deine Fahrradtouren ein, schließ dich einem Team an und sammelt
                zusammen Kilometer für eine nachhaltige Zukunft.
            </p>
            <div class="hero-actions reveal" style="--i: 3">
                <?php if (Session::isLoggedIn()): ?>
                    <a href="/dashboard" class="btn btn-primary btn-lg">
                        Zum Dashboard <?= Icon::svg('arrow-right') ?>
                    </a>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary btn-lg">
                        Jetzt mitmachen <?= Icon::svg('arrow-right') ?>
                    </a>
                <?php endif; ?>
                <a href="/leaderboard" class="btn btn-secondary btn-lg">
                    <?= Icon::svg('trophy') ?> Rangliste ansehen
                </a>
            </div>
        </div>

        <div class="preview reveal" style="--i: 2" aria-hidden="true">
            <div class="card stat stat-hero preview-km">
                <span class="stat-icon"><?= Icon::svg('route') ?></span>
                <span class="stat-label">Deine Kilometer</span>
                <span class="stat-value"><span data-count-to="248.6" data-decimals="1">248,6</span><span class="stat-unit">km</span></span>
            </div>
            <div class="card preview-cal">
                <div class="card-title">Aktionszeitraum</div>
                <div class="preview-cells">
                    <?php foreach ($previewLevels as $i => $level): ?>
                        <span style="--i: <?= $i ?>; <?= $level ? 'background: var(--heat-' . $level . ')' : '' ?>"></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card preview-rank">
                <span class="podium-crown"><?= Icon::svg('crown') ?></span>
                <div class="w-full">
                    <div class="card-title">Platz 1 im Team</div>
                    <div class="bar mt-8"><span style="--p: 82%"></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container features">
    <article class="card feature reveal" style="--i: 3">
        <span class="feature-step">1</span>
        <span class="stat-icon"><?= Icon::svg('calendar') ?></span>
        <h3>Touren eintragen</h3>
        <p>Tipp auf einen Tag im Kalender und trag deine gefahrenen Kilometer ein – fertig.</p>
    </article>
    <article class="card feature reveal" style="--i: 4">
        <span class="feature-step">2</span>
        <span class="stat-icon"><?= Icon::svg('users') ?></span>
        <h3>Im Team fahren</h3>
        <p>Tritt einem Team bei oder gründe dein eigenes. Jeder Kilometer zählt fürs ganze Team.</p>
    </article>
    <article class="card feature reveal" style="--i: 5">
        <span class="feature-step">3</span>
        <span class="stat-icon stat-icon-accent"><?= Icon::svg('trophy') ?></span>
        <h3>Ranglisten erklimmen</h3>
        <p>Vergleiche dich mit anderen Radlern und Teams und motiviert euch gegenseitig.</p>
    </article>
</section>

<footer class="site-footer">
    <div class="container">
        <span>GYP-Radeln · Gymnasium Penzberg</span>
        <span>Mit jedem Kilometer für eine grünere Zukunft.</span>
    </div>
</footer>
<?php require __DIR__ . '/../layout/footer.php'; ?>
