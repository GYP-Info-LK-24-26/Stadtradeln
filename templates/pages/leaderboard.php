<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rangliste</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>
    
    <div class="page-content">
        <ul class="stat-list type-switcher" onclick="changeType()">
            <li style="cursor: pointer">
                <span class="name"><?= $viewUsers ? 'Benutzername' : 'Team' ?></span>
                <span class="big">Rang</span>
            </li>
        </ul>

        <ul class="stat-list" id="leaderboard">
            <?php if ($viewUsers): ?>
                <?php foreach ($users as $index => $user): ?>
                    <li>
                        <span class="name"><?= htmlspecialchars($user->username) ?></span>
                        <span class="big">#<?= $index + 1 ?></span>
                        <span class="small"><?= number_format($user->totalDistance, 1) ?> km</span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($teams as $index => $team): ?>
                    <li>
                        <span class="name"><?= htmlspecialchars($team->name) ?></span>
                        <span class="big">#<?= $index + 1 ?></span>
                        <span class="small"><?= number_format($team->totalDistance, 1) ?> km</span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        const currentType = '<?= $currentType ?>';

        function changeType() {
            const url = new URL(window.location.href);
            
            if (currentType === 'global' || !currentType) {
                url.searchParams.set('type', 'team');
            } else if (currentType === 'team') {
                url.searchParams.set('type', 'all');
            } else {
                url.searchParams.set('type', 'global');
            }

            window.location.href = url.toString();
        }
    </script>
</body>
</html>
