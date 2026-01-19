<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <link rel="stylesheet" href="/css/components/popup.css">
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>
    
    <div class="page-content">
        <div class="user-info">
            <p>User: <?= htmlspecialchars($username) ?>#<?= $userId ?></p>
            <?php if ($teamId === -1): ?>
                <p class="warning">
                    Du bist nicht in einem Team: 
                    <a href="/team/join">Team erstellen/beitreten</a>
                </p>
            <?php endif; ?>
        </div>

        <div class="tour-display">
            <h2>Gesamte Kilometer: <?= number_format($totalDistance, 1) ?></h2>
            
            <button class="btn btn-primary" onclick="openTourPopup()">
                Tour hinzufügen
            </button>

            <ul class="stat-list">
                <?php foreach ($tours as $tour): ?>
                    <li>
                        <span class="small-right"><?= number_format($tour->distance, 1) ?> km</span>
                        <span class="small"><?= htmlspecialchars($tour->date) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
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
                        <input type="number" step="0.1" id="distance" name="distance" min="0" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTourPopup() {
            document.getElementById('tourPopup').style.display = 'block';
        }

        function closeTourPopup() {
            document.getElementById('tourPopup').style.display = 'none';
        }

        // Close popup when clicking outside
        document.getElementById('tourPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTourPopup();
            }
        });
    </script>
</body>
</html>
