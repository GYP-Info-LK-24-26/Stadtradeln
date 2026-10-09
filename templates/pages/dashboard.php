<?php
use App\Controllers\DashboardController;
use App\Core\Csrf;
use App\Core\Event;
use App\Core\Icon;
use App\Core\Session;
use App\Core\View;
use App\Repository\UserRepository;

$title = 'Dashboard';
$scripts = ['dashboard.js'];

$tourError = Session::getFlash('tour_error');
$tourPopupDate = Session::getFlash('tour_popup_date');

// Touren je Tag (für den Dialog) und Kennzahlen des Aktionszeitraums
$toursByDate = [];
$eventTours = 0;
$activeDays = 0;
$elapsedDays = 0;
$todayDate = null;
foreach ($calendar as $week) {
    foreach ($week as $cell) {
        if (!$cell['editable']) {
            continue;
        }
        $elapsedDays++;
        if ($cell['isToday']) {
            $todayDate = $cell['date'];
        }
        if (!empty($cell['tours'])) {
            $toursByDate[$cell['date']] = $cell['tours'];
            $eventTours += count($cell['tours']);
        }
        if ($cell['total'] > 0) {
            $activeDays++;
        }
    }
}

$today = new DateTimeImmutable('today');
if (Event::isUpcoming()) {
    $daysLabel = 'Start in';
    $daysValue = (int)$today->diff(Event::start())->days;
} else {
    $daysLabel = 'Verbleibend';
    $daysValue = Event::isOver() ? 0 : (int)$today->diff(Event::end())->days + 1;
}

// Link auf die Ranglisten-Seite, auf der die Person steht (#me springt zur eigenen Zeile)
$rankPage = intdiv($rank['position'] - 1, UserRepository::LEADERBOARD_PAGE_SIZE);
$rankUrl = '/leaderboard?type=users' . ($rankPage > 0 ? '&page=' . $rankPage : '') . '#me';

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
            <?php if ($todayDate !== null): ?>
                <button type="button" class="btn btn-primary" data-open-day="<?= htmlspecialchars($todayDate) ?>">
                    <?= Icon::svg('plus') ?> Tour eintragen
                </button>
            <?php endif; ?>
        </div>
    </header>

    <div class="stack-lg">
        <?php if (Event::isUpcoming()): ?>
            <div class="alert alert-info alert-banner">
                <?= Icon::svg('calendar') ?>
                <span>Die Aktion läuft vom <?= Event::start()->format('d.m.') ?> bis <?= Event::end()->format('d.m.Y') ?>. Ab dem <?= Event::start()->format('d.m.') ?> kannst du Touren eintragen.</span>
            </div>
        <?php elseif (Event::isOver()): ?>
            <div class="alert alert-info alert-banner">
                <?= Icon::svg('check-circle') ?>
                <span>Die Aktion ist beendet. Danke fürs Mitradeln!</span>
            </div>
        <?php endif; ?>

        <?php if ($teamId === null): ?>
            <div class="alert alert-warning alert-banner reveal" style="--i: 1">
                <?= Icon::svg('users') ?>
                <span>Du bist noch in keinem Team. Gemeinsam macht's mehr Spaß!</span>
                <a href="/team/join" class="btn btn-sm btn-secondary">Team finden</a>
            </div>
        <?php endif; ?>

        <section class="stat-grid stat-grid-dashboard" aria-label="Deine Statistik">
            <div class="stat stat-hero reveal" style="--i: 1">
                <span class="stat-icon"><?= Icon::svg('route') ?></span>
                <span class="stat-label">Kilometer gesamt</span>
                <span class="stat-value">
                    <span data-count-to="<?= $totalDistance ?>" data-decimals="1"><?= View::number($totalDistance) ?></span><span class="stat-unit">km</span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 2">
                <span class="stat-icon"><?= Icon::svg('bike') ?></span>
                <span class="stat-label">Touren</span>
                <span class="stat-value">
                    <span data-count-to="<?= $eventTours ?>"><?= $eventTours ?></span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 3">
                <span class="stat-icon stat-icon-accent"><?= Icon::svg('flame') ?></span>
                <span class="stat-label">Aktive Tage</span>
                <span class="stat-value">
                    <span data-count-to="<?= $activeDays ?>"><?= $activeDays ?></span><span class="stat-unit">/ <?= $elapsedDays ?></span>
                </span>
            </div>
            <div class="stat reveal" style="--i: 4">
                <span class="stat-icon"><?= Icon::svg('calendar') ?></span>
                <span class="stat-label"><?= $daysLabel ?></span>
                <span class="stat-value">
                    <span data-count-to="<?= $daysValue ?>"><?= $daysValue ?></span><span class="stat-unit"><?= $daysValue === 1 ? 'Tag' : 'Tage' ?></span>
                </span>
            </div>
            <a class="stat stat-link reveal" style="--i: 5" href="<?= htmlspecialchars($rankUrl) ?>"
               aria-label="Platz <?= $rank['position'] ?> von <?= $rank['total'] ?> – zur Rangliste">
                <span class="stat-icon stat-icon-accent"><?= Icon::svg('trophy') ?></span>
                <span class="stat-go" aria-hidden="true"><?= Icon::svg('chevron-right') ?></span>
                <span class="stat-label">Platzierung</span>
                <span class="stat-value">
                    <span><?= $rank['position'] ?>.</span><span class="stat-unit">von <?= $rank['total'] ?></span>
                </span>
            </a>
        </section>

        <section class="card card-calendar reveal" style="--i: 3" aria-labelledby="calendarTitle">
            <div class="card-header">
                <div>
                    <h2 class="card-title" id="calendarTitle">Aktionszeitraum <?= Event::label() ?></h2>
                    <p class="card-subtitle">Tippe auf einen Tag, um Touren einzutragen oder zu bearbeiten.</p>
                </div>
            </div>

            <div class="calendar">
                <?php foreach ($weekdays as $wd): ?>
                    <div class="calendar-weekday"><?= $wd ?></div>
                <?php endforeach; ?>

                <?php $i = 0; foreach ($calendar as $week): ?>
                    <?php foreach ($week as $cell): $i++; ?>
                        <?php if (!$cell['inEvent']): ?>
                            <div class="cal-day is-outside" style="--i: <?= $i ?>" aria-hidden="true">
                                <span class="cal-day-num"><?= $cell['day'] ?></span>
                            </div>
                        <?php elseif (!$cell['editable']): ?>
                            <div class="cal-day is-future" style="--i: <?= $i ?>" aria-label="<?= htmlspecialchars($cell['label']) ?>: noch nicht erreicht">
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

        <div class="alert alert-info" id="dayLimitNote" role="status" hidden>
            <?= Icon::svg('info') ?>
            <span>Du hast an diesem Tag schon <?= DashboardController::MAX_TOURS_PER_DAY ?> Touren eingetragen – mehr geht nicht. Bestehende Touren kannst du weiterhin bearbeiten.</span>
        </div>

        <form method="post" id="tourForm" action="/dashboard/tour" class="form">
            <?= Csrf::field() ?>
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
    <?= Csrf::field() ?>
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
    'maxToursPerDay' => DashboardController::MAX_TOURS_PER_DAY,
    'error' => $tourError ?: null,
    'reopenDate' => $tourPopupDate ?: null,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
