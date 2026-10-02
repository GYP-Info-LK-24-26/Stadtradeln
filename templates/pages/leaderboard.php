<?php
use App\Core\Icon;
use App\Core\Session;
use App\Core\View;

$title = 'Rangliste';
Session::start(); // öffentliche Route: Sitzung für die Hervorhebung des eigenen Eintrags laden

$entries = $viewUsers ? $users : $teams;
$page = $viewUsers ? ($page ?? 0) : 0;
$offset = $page * 20;
$highlightId = $viewUsers ? Session::getUserId() : Session::getTeamId();
$maxDistance = max(array_merge([0.0], array_map(fn($e) => $e->totalDistance, $entries)));

// Podest nur auf der ersten Seite und bei mindestens drei Einträgen
$podium = ($page === 0 && count($entries) >= 3) ? array_slice($entries, 0, 3) : [];
$rest = array_slice($entries, count($podium), null, true);

require __DIR__ . '/../layout/header.php';
?>
<div class="container container-narrow page">
    <header class="page-header">
        <div class="page-header-text">
            <h1>Rangliste</h1>
            <p class="lead">Wer hat die meisten Kilometer gesammelt?</p>
        </div>
        <nav class="segmented" style="--count: 2; --index: <?= $viewUsers ? 0 : 1 ?>" aria-label="Ansicht">
            <a href="/leaderboard?type=users" class="segmented-item<?= $viewUsers ? ' is-active' : '' ?>"
               <?= $viewUsers ? 'aria-current="page"' : '' ?>>
                <?= Icon::svg('user') ?> Personen
            </a>
            <a href="/leaderboard?type=teams" class="segmented-item<?= !$viewUsers ? ' is-active' : '' ?>"
               <?= !$viewUsers ? 'aria-current="page"' : '' ?>>
                <?= Icon::svg('users') ?> Teams
            </a>
        </nav>
    </header>

    <?php if (empty($entries)): ?>
        <div class="card empty">
            <span class="empty-icon"><?= Icon::svg('trophy') ?></span>
            <h2>Noch keine Einträge</h2>
            <p>Sobald die ersten Kilometer eingetragen sind, erscheint hier die Rangliste.</p>
        </div>
    <?php else: ?>
        <?php if ($podium): ?>
            <section class="podium" aria-label="Top 3">
                <?php foreach ($podium as $index => $entry): $place = $index + 1; ?>
                    <div class="podium-place place-<?= $place ?><?= $entry->id === $highlightId ? ' is-me' : '' ?>">
                        <?php if ($place === 1): ?>
                            <span class="podium-crown"><?= Icon::svg('crown') ?></span>
                        <?php endif; ?>
                        <span class="podium-avatar">
                            <?= View::avatar($entry->name, 'lg') ?>
                            <span class="podium-medal"><?= $place ?></span>
                        </span>
                        <span class="podium-name" title="<?= htmlspecialchars($entry->name) ?>"><?= htmlspecialchars($entry->name) ?></span>
                        <span class="podium-value"><?= View::number($entry->totalDistance) ?> <small>km</small></span>
                        <?php if (!$viewUsers): ?>
                            <span class="rank-meta"><?= $entry->memberCount ?> <?= $entry->memberCount === 1 ? 'Mitglied' : 'Mitglieder' ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if ($rest): ?>
            <div class="card card-flush">
                <ol class="rank-list" start="<?= $offset + count($podium) + 1 ?>">
                    <?php foreach ($rest as $index => $entry): ?>
                        <?php
                            $percent = $maxDistance > 0 ? round($entry->totalDistance / $maxDistance * 100, 1) : 0;
                            $isMe = $entry->id === $highlightId;
                        ?>
                        <li class="rank-row<?= $isMe ? ' is-me' : '' ?>">
                            <span class="rank-pos"><?= $offset + $index + 1 ?></span>
                            <?= View::avatar($entry->name) ?>
                            <div class="rank-main">
                                <div class="rank-name">
                                    <span><?= htmlspecialchars($entry->name) ?></span>
                                    <?php if ($isMe): ?>
                                        <span class="badge badge-primary"><?= $viewUsers ? 'Du' : 'Dein Team' ?></span>
                                    <?php endif; ?>
                                    <?php if (!$viewUsers): ?>
                                        <span class="rank-meta"><?= $entry->memberCount ?> <?= $entry->memberCount === 1 ? 'Mitglied' : 'Mitglieder' ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="bar"><span style="--p: <?= $percent ?>%; --i: <?= $index ?>"></span></div>
                            </div>
                            <span class="rank-value"><?= View::number($entry->totalDistance) ?> <small>km</small></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>

        <?php if ($viewUsers && ($page > 0 || count($users) === 20)): ?>
            <nav class="pagination" aria-label="Seiten">
                <?php if ($page > 0): ?>
                    <a class="btn btn-secondary" href="/leaderboard?type=users&page=<?= $page - 1 ?>"><?= Icon::svg('chevron-left') ?> Zurück</a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>
                <?php if (count($users) === 20): ?>
                    <a class="btn btn-secondary" href="/leaderboard?type=users&page=<?= $page + 1 ?>">Weiter <?= Icon::svg('chevron-right') ?></a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
