<?php
use App\Core\Icon;
use App\Core\View;

$title = 'Team finden';
require __DIR__ . '/../layout/header.php';
?>
<div class="container page">
    <header class="page-header">
        <div class="page-header-text">
            <h1>Team finden</h1>
            <p class="lead">Tritt einem bestehenden Team bei oder gründe dein eigenes.</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-dialog-open="createDialog">
                <?= Icon::svg('plus') ?> Neues Team
            </button>
        </div>
    </header>

    <?php if (!empty($error) && !$showCreate): ?>
        <div class="alert alert-error mb-24" role="alert">
            <?= Icon::svg('alert') ?>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($teams)): ?>
        <div class="card empty">
            <span class="empty-icon"><?= Icon::svg('users') ?></span>
            <h2>Noch keine Teams</h2>
            <p>Sei die erste Person und gründe ein Team!</p>
            <button type="button" class="btn btn-primary" data-dialog-open="createDialog">
                <?= Icon::svg('plus') ?> Team gründen
            </button>
        </div>
    <?php else: ?>
        <div class="toolbar">
            <div class="input-wrap">
                <?= Icon::svg('search') ?>
                <input class="input" type="search" id="teamSearch" placeholder="Team suchen …"
                       aria-label="Team suchen" autocomplete="off">
            </div>
        </div>

        <div class="team-grid" id="teamList">
            <?php foreach ($teams as $i => $team): ?>
                <form method="post" action="/team/join" data-team="<?= htmlspecialchars($team->name) ?>"
                      data-confirm="Du wirst Mitglied im Team „<?= htmlspecialchars($team->name) ?>“."
                      data-confirm-title="Team beitreten?" data-confirm-ok="Beitreten">
                    <input type="hidden" name="team_name" value="<?= htmlspecialchars($team->name) ?>">
                    <button type="submit" class="team-card">
                        <?= View::avatar($team->name) ?>
                        <span class="team-card-text">
                            <span class="team-card-name"><?= htmlspecialchars($team->name) ?></span>
                            <span class="team-card-meta">
                                <span><?= Icon::svg('users') ?> <?= $team->memberCount ?></span>
                                <span><?= Icon::svg('route') ?> <?= View::number($team->totalDistance) ?> km</span>
                            </span>
                        </span>
                        <span class="team-card-go"><?= Icon::svg('arrow-right') ?></span>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>

        <div class="card empty" id="noResults" hidden>
            <span class="empty-icon"><?= Icon::svg('search') ?></span>
            <h2>Kein Team gefunden</h2>
            <p>Kein Team passt zu deiner Suche. Wie wär's mit einem eigenen?</p>
            <button type="button" class="btn btn-primary" data-dialog-open="createDialog">
                <?= Icon::svg('plus') ?> Neues Team
            </button>
        </div>
    <?php endif; ?>
</div>

<dialog class="dialog" id="createDialog" aria-labelledby="createDialogTitle" <?= $showCreate ? 'data-open-on-load' : '' ?>>
    <div class="dialog-header">
        <div>
            <h2 class="dialog-title" id="createDialogTitle">Neues Team gründen</h2>
            <p class="dialog-subtitle">Du wirst automatisch Teamleiter.</p>
        </div>
        <button type="button" class="btn btn-ghost btn-icon dialog-close" data-dialog-close aria-label="Schließen">
            <?= Icon::svg('x') ?>
        </button>
    </div>
    <form method="post" action="/team/join" class="dialog-body form">
        <input type="hidden" name="type" value="create">

        <?php if (!empty($error) && $showCreate): ?>
            <div class="alert alert-error" role="alert">
                <?= Icon::svg('alert') ?>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="field">
            <label class="field-label" for="team_name">Teamname</label>
            <div class="input-wrap">
                <?= Icon::svg('users') ?>
                <input class="input" type="text" id="team_name" name="team_name" placeholder="z. B. Die Radler"
                       value="<?= htmlspecialchars($teamName ?? '') ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Team gründen</button>
    </form>
</dialog>
<?php
$inlineScript = <<<'JS'
var search = document.getElementById('teamSearch');
if (search) {
    search.addEventListener('input', function () {
        var term = this.value.trim().toLowerCase();
        var visible = 0;
        document.querySelectorAll('#teamList [data-team]').forEach(function (item) {
            var match = item.dataset.team.toLowerCase().includes(term);
            item.hidden = !match;
            if (match) visible++;
        });
        document.getElementById('noResults').hidden = visible > 0;
    });
}
JS;
require __DIR__ . '/../layout/footer.php';
