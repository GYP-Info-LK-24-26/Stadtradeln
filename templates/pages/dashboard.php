<?php
use App\Core\Icon;
use App\Core\Session;
use App\Core\View;

$title = 'Dashboard';
$scripts = ['dashboard.js'];

$tourError = Session::getFlash('tour_error');
$tourPopupDate = Session::getFlash('tour_popup_date');

// Touren je Tag (für den Dialog) und Kennzahlen des Zwei-Wochen-Fensters
$toursByDate = [];
$windowDistance = 0.0;
$windowTours = 0;
$activeDays = 0;
$windowDays = 0;
$todayDate = null;
foreach ($calendar as $week) {
    foreach ($week as $cell) {
        if (!$cell['inRange']) {
            continue;
        }
        $windowDays++;
        if ($cell['isToday']) {
            $todayDate = $cell['date'];
        }
        if (!empty($cell['tours'])) {
            $toursByDate[$cell['date']] = $cell['tours'];
            $windowTours += count($cell['tours']);
        }
        if ($cell['total'] > 0) {
            $windowDistance += $cell['total'];
            $activeDays++;
        }
    }
}

$weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

require __DIR__ . '/../layout/header.php';
?>
<div class="container page">
    <header class="page-header">
        <div class="page-header-text reveal">
            <span class="eyebrow"><?= View::longDate(new DateTimeImmutable('today')) ?></span>
            <h1>Hallo, <?= htmlspecialchars(Session::getDisplayName() ?? '') ?>!</h1>
        </div>
        <div class="page-actions reveal" style="--i: 1">
            <button type="button" class="btn btn-primary" data-open-day="<?= htmlspecialchars($todayDate ?? '') ?>">
                <?= Icon::svg('plus') ?> Tour eintragen
            </button>
        </div>
    </header>

    <div class="stack-lg">
        <?php if ($teamId === null): ?>
            <div class="alert alert-warning alert-banner reveal" style="--i: 1">
                <?= Icon::svg('users') ?>
                <span>Du bist noch in keinem Team. Gemeinsam macht's mehr Spaß!</span>
                <a href="/team/join" class="btn btn-sm btn-secondary">Team finden</a>
            </div>
        <?php endif; ?>

        <section class="stat-grid" aria-label="Deine Statistik">
            <div class="stat stat-hero reveal" style="--i: 1">
                <span class="stat-icon"><?= Icon::svg('route') ?></span>
                <span class="stat-label">Kilometer gesamt</span>
                <span class="stat-value">
                    <span data-count-to="<?= $totalDistance ?>" data-decimals="1"><?= View::number($totalDistance) ?></span><span class="stat-unit">km</span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 2">
                <span class="stat-icon"><?= Icon::svg('trending-up') ?></span>
                <span class="stat-label">Letzte 14 Tage</span>
                <span class="stat-value">
                    <span data-count-to="<?= $windowDistance ?>" data-decimals="1"><?= View::number($windowDistance) ?></span><span class="stat-unit">km</span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 3">
                <span class="stat-icon stat-icon-accent"><?= Icon::svg('flame') ?></span>
                <span class="stat-label">Aktive Tage</span>
                <span class="stat-value">
                    <span data-count-to="<?= $activeDays ?>"><?= $activeDays ?></span><span class="stat-unit">/ <?= $windowDays ?></span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 4">
                <span class="stat-icon"><?= Icon::svg('bike') ?></span>
                <span class="stat-label">Touren (14 Tage)</span>
                <span class="stat-value">
                    <span data-count-to="<?= $windowTours ?>"><?= $windowTours ?></span>
                </span>
            </div>
        </section>

        <section class="card card-calendar reveal" style="--i: 3" aria-labelledby="calendarTitle">
            <div class="card-header">
                <div>
                    <h2 class="card-title" id="calendarTitle">Letzte zwei Wochen</h2>
                    <p class="card-subtitle">Tippe auf einen Tag, um Touren einzutragen oder zu bearbeiten.</p>
                </div>
            </div>

            <div class="calendar">
                <?php foreach ($weekdays as $wd): ?>
                    <div class="calendar-weekday"><?= $wd ?></div>
                <?php endforeach; ?>

                <?php $i = 0; foreach ($calendar as $week): ?>
                    <?php foreach ($week as $cell): $i++; ?>
                        <?php if (!$cell['inRange']): ?>
                            <div class="cal-day is-outside" style="--i: <?= $i ?>" aria-hidden="true">
                                <span class="cal-day-num"><?= $cell['day'] ?></span>
                            </div>
                        <?php else: ?>
                            <?php
                                $classes = 'cal-day level-' . $cell['level'];
                                if ($cell['isToday']) {
                                    $classes .= ' is-today';
                                }
                                $kmLabel = View::number($cell['total']) . ' km';
                            ?>
                            <button type="button" class="<?= $classes ?>" style="--i: <?= $i ?>"
                                    data-open-day="<?= htmlspecialchars($cell['date']) ?>"
                                    aria-label="<?= htmlspecialchars($cell['label']) ?>: <?= $kmLabel ?>">
                                <span class="cal-day-num"><?= $cell['day'] ?></span>
                                <?php if ($cell['total'] > 0): ?>
                                    <span class="cal-day-km"><?= View::number($cell['total']) ?><small>km</small></span>
                                <?php else: ?>
                                    <span class="cal-day-add"><?= Icon::svg('plus') ?></span>
                                <?php endif; ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <div class="calendar-legend" aria-hidden="true">
                <span>Weniger</span>
                <i></i><i class="level-1"></i><i class="level-2"></i><i class="level-3"></i><i class="level-4"></i>
                <span>Mehr</span>
            </div>
        </section>
    </div>
</div>

<!-- Tagesdialog: Touren eines Tages anzeigen, hinzufügen, bearbeiten -->
<dialog class="dialog" id="dayDialog" aria-labelledby="dayDialogTitle">
    <div class="dialog-header">
        <div>
            <h2 class="dialog-title" id="dayDialogTitle">Touren</h2>
            <p class="dialog-subtitle" id="dayDialogSubtitle"></p>
        </div>
        <button type="button" class="btn btn-ghost btn-icon dialog-close" data-dialog-close aria-label="Schließen">
            <?= Icon::svg('x') ?>
        </button>
    </div>

    <div class="dialog-body">
        <div class="alert alert-error" id="dayDialogError" role="alert" hidden>
            <?= Icon::svg('alert') ?>
            <span></span>
        </div>

        <ul class="tour-list" id="dayTourList"></ul>

        <form method="post" id="tourForm" action="/dashboard/tour" class="form">
            <input type="hidden" name="tour_id" id="formTourId" value="">
            <input type="hidden" name="date" id="formDate" value="">

            <div class="field">
                <label class="field-label" for="formDistance" id="formDistanceLabel">Neue Tour</label>
                <div class="input-wrap">
                    <input class="input input-lg" type="number" step="0.1" min="0.1" max="300"
                           inputmode="decimal" id="formDistance" name="distance" placeholder="0,0" required>
                    <span class="input-suffix">km</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="formCancelEdit" hidden>Abbrechen</button>
                <button type="submit" class="btn btn-primary btn-lg" id="formSubmit">
                    <?= Icon::svg('plus') ?> <span>Hinzufügen</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

<form id="deleteForm" method="post" action="/dashboard/tour/delete" hidden
      data-confirm="Diese Tour wird dauerhaft gelöscht." data-confirm-title="Tour löschen?"
      data-confirm-ok="Löschen" data-confirm-variant="danger">
    <input type="hidden" id="deleteTourId" name="tour_id">
</form>

<template id="tourItemTemplate">
    <li class="tour-item">
        <span class="icon-bubble"><?= Icon::svg('bike') ?></span>
        <span class="tour-dist"></span>
        <span class="tour-actions">
            <button type="button" class="btn btn-ghost btn-icon btn-sm btn-edit" aria-label="Tour bearbeiten"><?= Icon::svg('pencil') ?></button>
            <button type="button" class="btn btn-ghost btn-icon btn-sm btn-delete" aria-label="Tour löschen"><?= Icon::svg('trash') ?></button>
        </span>
    </li>
</template>

<script type="application/json" id="dashboardData"><?= json_encode([
    'toursByDate' => $toursByDate,
    'error' => $tourError ?: null,
    'reopenDate' => $tourPopupDate ?: null,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
