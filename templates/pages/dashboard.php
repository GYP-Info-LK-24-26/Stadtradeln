<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - GYP-Radeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/popup.css">
    <link rel="stylesheet" href="/css/components/calendar.css">
    <style>
        .dashboard-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .dashboard-header h1 {
            margin-bottom: var(--space-sm);
        }

        .total-distance {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--space-xl);
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            margin-bottom: var(--space-xl);
            position: relative;
            overflow: hidden;
        }

        .total-distance::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--mint-fresh), var(--forest-light), var(--sun-gold));
        }

        .total-distance-label {
            font-size: 0.9rem;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-xs);
        }

        .total-distance-value {
            font-family: var(--font-display);
            font-size: clamp(3rem, 10vw, 5rem);
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .total-distance-unit {
            font-size: 0.4em;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <?php
        $tourError       = \App\Core\Session::getFlash('tour_error');
        $tourPopupDate   = \App\Core\Session::getFlash('tour_popup_date');

        // Touren je Tag für das Popup als JS-Datenstruktur aufbereiten
        $toursByDate = [];
        foreach ($calendar as $week) {
            foreach ($week as $cell) {
                if (!empty($cell['tours'])) {
                    $toursByDate[$cell['date']] = $cell['tours'];
                }
            }
        }

        $weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    ?>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="dashboard-header">
            <h1>Dein Dashboard</h1>
            <?php if ($teamId === null): ?>
                <p class="warning">
                    Du bist noch in keinem Team.
                    <a href="/team/join">Jetzt Team beitreten oder erstellen</a>
                </p>
            <?php endif; ?>
        </div>

        <div class="total-distance">
            <span class="total-distance-label">Gesamte Kilometer</span>
            <span class="total-distance-value">
                <?= number_format($totalDistance, 1) ?>
                <span class="total-distance-unit">km</span>
            </span>
        </div>

        <div class="calendar-card">
            <h2>Letzte zwei Wochen</h2>
            <p class="calendar-hint">Klicke auf einen Tag, um eine Tour einzutragen.</p>

            <div class="calendar-grid">
                <?php foreach ($weekdays as $wd): ?>
                    <div class="calendar-weekday"><?= $wd ?></div>
                <?php endforeach; ?>

                <?php foreach ($calendar as $week): ?>
                    <?php foreach ($week as $cell): ?>
                        <?php if (!$cell['inRange']): ?>
                            <div class="cal-day is-outside" aria-hidden="true">
                                <span class="cal-day-num"><?= $cell['day'] ?></span>
                            </div>
                        <?php else: ?>
                            <?php
                                $classes = 'cal-day level-' . $cell['level'];
                                if ($cell['isToday']) {
                                    $classes .= ' is-today';
                                }
                                if ($cell['total'] <= 0) {
                                    $classes .= ' day-zero';
                                }
                            ?>
                            <button type="button"
                                    class="<?= $classes ?>"
                                    data-date="<?= htmlspecialchars($cell['date']) ?>"
                                    title="<?= htmlspecialchars($cell['label']) ?> – <?= number_format($cell['total'], 1) ?> km"
                                    onclick="openDayPopup('<?= htmlspecialchars($cell['date']) ?>')">
                                <span class="cal-day-num"><?= $cell['day'] ?></span>
                                <?php if ($cell['total'] > 0): ?>
                                    <span class="cal-day-km"><?= number_format($cell['total'], 1) ?><span class="unit">km</span></span>
                                <?php endif; ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <div class="calendar-legend">
                <span>Weniger</span>
                <span class="legend-cell"></span>
                <span class="legend-cell level-1"></span>
                <span class="legend-cell level-2"></span>
                <span class="legend-cell level-3"></span>
                <span class="legend-cell level-4"></span>
                <span>Mehr</span>
            </div>
        </div>

        <!-- Day Popup: list + add/edit tour for a single day -->
        <div class="popup-overlay" id="dayPopup">
            <div class="popup">
                <span class="close" onclick="closeDayPopup()">&times;</span>

                <h3 id="dayPopupTitle">Touren</h3>

                <p class="error" id="dayPopupError" style="display: none;"></p>

                <ul class="day-tour-list" id="dayTourList"></ul>

                <hr class="day-form-divider">

                <form method="post" id="tourForm" action="/dashboard/tour">
                    <input type="hidden" name="tour_id" id="formTourId" value="">
                    <input type="hidden" name="date" id="formDate" value="">

                    <div class="form-group">
                        <label for="formDistance" id="formDistanceLabel">Distanz (km)</label>
                        <input
                            type="number"
                            step="0.1"
                            id="formDistance"
                            name="distance"
                            min="0"
                            placeholder="z.B. 15.5"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary" id="formSubmit">Hinzufügen</button>
                    <button type="button" class="btn btn-secondary" id="formCancelEdit"
                            style="display: none; margin-top: var(--space-sm);"
                            onclick="resetTourForm()">Abbrechen</button>
                </form>

                <form id="deleteForm" method="post" action="/dashboard/tour/delete" style="display: none;">
                    <input type="hidden" id="deleteTourId" name="tour_id">
                </form>
            </div>
        </div>
    </div>

    <script>
        const toursByDate = <?= json_encode($toursByDate, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const flashError = <?= json_encode($tourError ?: null) ?>;
        const reopenDate = <?= json_encode($tourPopupDate ?: null) ?>;

        const fmtKm = (n) => Number(n).toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        const fmtDate = (iso) => {
            const [y, m, d] = iso.split('-');
            return `${d}.${m}.${y}`;
        };

        function renderTourList(date) {
            const list = document.getElementById('dayTourList');
            list.innerHTML = '';
            const tours = toursByDate[date] || [];
            tours.forEach((tour) => {
                const li = document.createElement('li');

                const dist = document.createElement('span');
                dist.className = 'tour-dist';
                dist.textContent = fmtKm(tour.distance) + ' km';

                const actions = document.createElement('div');
                actions.className = 'day-tour-actions';

                const edit = document.createElement('button');
                edit.type = 'button';
                edit.className = 'edit-tour';
                edit.textContent = 'Bearbeiten';
                edit.onclick = () => startEdit(tour.id, tour.distance);

                const del = document.createElement('button');
                del.type = 'button';
                del.className = 'delete-tour';
                del.textContent = 'Löschen';
                del.onclick = () => deleteTour(tour.id);

                actions.appendChild(edit);
                actions.appendChild(del);
                li.appendChild(dist);
                li.appendChild(actions);
                list.appendChild(li);
            });
        }

        function openDayPopup(date) {
            document.getElementById('dayPopupTitle').textContent = 'Touren am ' + fmtDate(date);
            document.getElementById('formDate').value = date;
            renderTourList(date);
            resetTourForm();
            document.getElementById('dayPopup').style.display = 'block';
            document.getElementById('formDistance').focus();
        }

        function closeDayPopup() {
            document.getElementById('dayPopup').style.display = 'none';
            document.getElementById('dayPopupError').style.display = 'none';
        }

        // Switch the form into "edit existing tour" mode
        function startEdit(tourId, distance) {
            document.getElementById('formTourId').value = tourId;
            document.getElementById('formDistance').value = distance;
            document.getElementById('tourForm').action = '/dashboard/tour/update';
            document.getElementById('formSubmit').textContent = 'Speichern';
            document.getElementById('formDistanceLabel').textContent = 'Distanz bearbeiten (km)';
            document.getElementById('formCancelEdit').style.display = 'inline-flex';
            document.getElementById('formDistance').focus();
        }

        // Back to "add new tour" mode
        function resetTourForm() {
            document.getElementById('formTourId').value = '';
            document.getElementById('formDistance').value = '';
            document.getElementById('tourForm').action = '/dashboard/tour';
            document.getElementById('formSubmit').textContent = 'Hinzufügen';
            document.getElementById('formDistanceLabel').textContent = 'Distanz (km)';
            document.getElementById('formCancelEdit').style.display = 'none';
        }

        function deleteTour(tourId) {
            if (confirm('Möchtest du diese Tour wirklich löschen?')) {
                document.getElementById('deleteTourId').value = tourId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Close popup when clicking the backdrop
        document.getElementById('dayPopup').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDayPopup();
            }
        });

        // Close popup with Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDayPopup();
            }
        });

        // Re-open the relevant day after a server-side validation error
        if (reopenDate) {
            openDayPopup(reopenDate);
            if (flashError) {
                const err = document.getElementById('dayPopupError');
                err.textContent = flashError;
                err.style.display = 'block';
            }
        }
    </script>
</body>
</html>
