<?php
use App\Core\Icon;
use App\Core\View;

$title = $team ? $team->name : 'Team';
$isLeader = $team && $userId === $team->teamleiterId;
$maxDistance = max(array_merge([0.0], array_map(fn($m) => $m->totalDistance, $members)));

require __DIR__ . '/../layout/header.php';
?>
<div class="container page">
    <?php if (!$hasTeam): ?>
        <div class="card empty">
            <span class="empty-icon"><?= Icon::svg('users') ?></span>
            <h1>Noch kein Team</h1>
            <p>Tritt einem bestehenden Team bei oder gründe dein eigenes – gemeinsam sammelt ihr mehr Kilometer.</p>
            <a href="/team/join" class="btn btn-primary btn-lg"><?= Icon::svg('user-plus') ?> Team finden oder gründen</a>
        </div>
    <?php elseif ($team): ?>
        <header class="page-header">
            <div class="team-title">
                <span class="team-mark"><?= Icon::svg('users') ?></span>
                <div class="page-header-text">
                    <span class="eyebrow">Dein Team</span>
                    <h1>
                        <?php if ($isLeader): ?>
                            <form method="post" action="/team/name" class="inline-edit" data-inline-edit>
                                <span class="inline-edit-text"><?= htmlspecialchars($team->name) ?></span>
                                <input type="text" name="team_name" class="inline-edit-input"
                                       value="<?= htmlspecialchars($team->name) ?>" aria-label="Teamname" required>
                                <button type="button" class="inline-edit-btn" title="Teamname bearbeiten" aria-label="Teamname bearbeiten">
                                    <?= Icon::svg('pencil') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <?= htmlspecialchars($team->name) ?>
                        <?php endif; ?>
                    </h1>
                </div>
            </div>
            <div class="page-actions">
                <a href="/leaderboard?type=teams" class="btn btn-secondary"><?= Icon::svg('trophy') ?> Teamrangliste</a>
            </div>
        </header>

        <section class="stat-grid" aria-label="Team-Statistik">
            <div class="stat stat-hero">
                <span class="stat-icon"><?= Icon::svg('route') ?></span>
                <span class="stat-label">Kilometer gesamt</span>
                <span class="stat-value">
                    <span data-count-to="<?= $stats['totalDistance'] ?>" data-decimals="1"><?= View::number($stats['totalDistance']) ?></span><span class="stat-unit">km</span>
                </span>
            </div>
            <div class="stat">
                <span class="stat-icon"><?= Icon::svg('bike') ?></span>
                <span class="stat-label">Touren</span>
                <span class="stat-value"><span data-count-to="<?= $stats['totalTours'] ?>"><?= $stats['totalTours'] ?></span></span>
            </div>
            <div class="stat">
                <span class="stat-icon stat-icon-accent"><?= Icon::svg('gauge') ?></span>
                <span class="stat-label">Ø pro Tour</span>
                <?php $avg = $stats['totalTours'] > 0 ? $stats['totalDistance'] / $stats['totalTours'] : 0; ?>
                <span class="stat-value">
                    <span data-count-to="<?= $avg ?>" data-decimals="1"><?= View::number($avg) ?></span><span class="stat-unit">km</span>
                </span>
            </div>
        </section>

        <div class="section-title">
            <h2>Mitglieder <span class="badge"><?= count($members) ?></span></h2>
        </div>

        <div class="card card-flush">
            <ol class="rank-list">
                <?php foreach ($members as $index => $member): ?>
                    <?php
                        $isMe = $member->id === $userId;
                        $isMemberLeader = $member->id === $team->teamleiterId;
                        $percent = $maxDistance > 0 ? round($member->totalDistance / $maxDistance * 100, 1) : 0;
                    ?>
                    <li class="rank-row<?= $isMe ? ' is-me' : '' ?>">
                        <span class="rank-pos"><?= $index + 1 ?></span>
                        <?= View::avatar($member->name) ?>
                        <div class="rank-main">
                            <div class="rank-name">
                                <span><?= htmlspecialchars($member->name) ?></span>
                                <?php if ($isMe): ?>
                                    <span class="badge badge-primary">Du</span>
                                <?php endif; ?>
                                <?php if ($isMemberLeader): ?>
                                    <span class="badge badge-accent"><?= Icon::svg('crown') ?> Teamleitung</span>
                                <?php endif; ?>
                            </div>
                            <div class="bar"><span style="--p: <?= $percent ?>%; --i: <?= $index ?>"></span></div>
                        </div>
                        <div class="rank-side">
                            <span class="rank-value"><?= View::number($member->totalDistance) ?> <small>km</small></span>
                            <?php if ($isLeader && !$isMemberLeader): ?>
                                <form method="post" action="/team/leader"
                                      data-confirm="<?= htmlspecialchars($member->name) ?> übernimmt die Teamleitung. Du verlierst damit deine Teamleiter-Rechte."
                                      data-confirm-title="Teamleitung übergeben?" data-confirm-ok="Übergeben">
                                    <input type="hidden" name="new_leader" value="<?= (int)$member->id ?>">
                                    <button type="submit" class="btn btn-ghost btn-icon btn-sm"
                                            title="Zum Teamleiter machen" aria-label="<?= htmlspecialchars($member->name) ?> zum Teamleiter machen">
                                        <?= Icon::svg('crown') ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <div class="danger-zone">
            <div>
                <strong>Team verlassen</strong>
                <p>Deine Kilometer zählen danach nicht mehr für dieses Team.</p>
            </div>
            <form method="post" action="/team/leave"
                  data-confirm="Du kannst danach einem anderen Team beitreten oder ein neues gründen."
                  data-confirm-title="Team wirklich verlassen?" data-confirm-ok="Verlassen" data-confirm-variant="danger">
                <button type="submit" class="btn btn-danger-soft"><?= Icon::svg('log-out') ?> Team verlassen</button>
            </form>
        </div>
    <?php else: ?>
        <div class="card empty">
            <span class="empty-icon"><?= Icon::svg('alert') ?></span>
            <h1>Teamdaten nicht verfügbar</h1>
            <p>Die Daten deines Teams konnten nicht geladen werden.</p>
            <a href="/team/join" class="btn btn-primary">Anderes Team wählen</a>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
