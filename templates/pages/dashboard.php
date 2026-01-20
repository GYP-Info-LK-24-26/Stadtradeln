<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <link rel="stylesheet" href="/css/components/popup.css">
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

        .dashboard-actions {
            display: flex;
            justify-content: center;
            gap: var(--space-md);
            margin-bottom: var(--space-xl);
        }

        .tour-history h2 {
            text-align: center;
            margin-bottom: var(--space-lg);
        }

        .empty-state {
            text-align: center;
            padding: var(--space-2xl);
            color: var(--color-text-muted);
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            fill: var(--color-border);
            margin-bottom: var(--space-md);
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="dashboard-header">
            <h1>Dein Dashboard</h1>
            <?php if ($teamId === -1): ?>
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

        <div class="dashboard-actions">
            <button class="btn btn-primary btn-lg" onclick="openTourPopup()">
                + Tour hinzufügen
            </button>
        </div>

        <div class="tour-history">
            <h2>Deine Touren</h2>

            <?php if (empty($tours)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 6a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7-8h3l2 4h-4l-1-4zm-2 0L8 8H5V6h4l1-2zm3 4l2 4H9l-1-4h5z"/>
                    </svg>
                    <p>Du hast noch keine Touren eingetragen.</p>
                    <p>Klicke auf "Tour hinzufügen" um deine erste Fahrt zu erfassen!</p>
                </div>
            <?php else: ?>
                <ul class="stat-list">
                    <?php foreach ($tours as $tour): ?>
                        <li onclick="openEditPopup(<?= $tour->id ?>, '<?= htmlspecialchars($tour->date) ?>', <?= $tour->distance ?>)" style="cursor: pointer;">
                            <span class="small-right"><?= number_format($tour->distance, 1) ?> km</span>
                            <span class="small"><?= htmlspecialchars($tour->getFormattedDate()) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Add Tour Popup -->
        <div class="popup-overlay" id="tourPopup">
            <div class="popup">
                <span class="close" onclick="closeTourPopup()">&times;</span>

                <h3>Neue Tour hinzufügen</h3>

                <form method="post" action="/dashboard/tour">
                    <div class="form-group">
                        <label for="date">Datum</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="distance">Distanz (km)</label>
                        <input
                            type="number"
                            step="0.1"
                            id="distance"
                            name="distance"
                            min="0"
                            placeholder="z.B. 15.5"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>

        <!-- Edit Tour Popup -->
        <div class="popup-overlay" id="editTourPopup">
            <div class="popup">
                <span class="close" onclick="closeEditPopup()">&times;</span>

                <h3>Tour bearbeiten</h3>

                <form method="post" action="/dashboard/tour/update">
                    <input type="hidden" id="edit_tour_id" name="tour_id">

                    <div class="form-group">
                        <label for="edit_date">Datum</label>
                        <input type="date" id="edit_date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_distance">Distanz (km)</label>
                        <input
                            type="number"
                            step="0.1"
                            id="edit_distance"
                            name="distance"
                            min="0"
                            placeholder="z.B. 15.5"
                            required
                        >
                    </div>

                    <div style="display: flex; gap: var(--space-sm); justify-content: space-between;">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                        <button type="button" class="btn btn-danger" onclick="deleteTour()">Löschen</button>
                    </div>
                </form>

                <form id="deleteForm" method="post" action="/dashboard/tour/delete" style="display: none;">
                    <input type="hidden" id="delete_tour_id" name="tour_id">
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set default date to today
        document.getElementById('date').valueAsDate = new Date();

        function openTourPopup() {
            document.getElementById('tourPopup').style.display = 'block';
        }

        function closeTourPopup() {
            document.getElementById('tourPopup').style.display = 'none';
        }

        function openEditPopup(tourId, date, distance) {
            document.getElementById('edit_tour_id').value = tourId;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_distance').value = distance;
            document.getElementById('editTourPopup').style.display = 'block';
        }

        function closeEditPopup() {
            document.getElementById('editTourPopup').style.display = 'none';
        }

        // Close popup when clicking outside
        document.getElementById('tourPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTourPopup();
            }
        });

        document.getElementById('editTourPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditPopup();
            }
        });

        // Close popup with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTourPopup();
                closeEditPopup();
            }
        });

        function deleteTour() {
            if (confirm('Möchtest du diese Tour wirklich löschen?')) {
                document.getElementById('delete_tour_id').value = document.getElementById('edit_tour_id').value;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
