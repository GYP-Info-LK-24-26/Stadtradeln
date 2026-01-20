<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rangliste - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <style>
        .leaderboard-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .leaderboard-header h1 {
            margin-bottom: var(--space-sm);
        }

        .view-toggle {
            display: inline-flex;
            gap: var(--space-xs);
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-xs);
            margin-bottom: var(--space-xl);
        }

        .view-toggle button {
            padding: var(--space-sm) var(--space-lg);
            border: none;
            background: transparent;
            font-family: var(--font-display);
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-text-muted);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .view-toggle button:hover {
            color: light-dark(var(--forest-deep), #ffffff);
            background: light-dark(rgba(64, 145, 108, 0.1), rgba(116, 198, 157, 0.15));
        }

        .view-toggle button.active {
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(64, 145, 108, 0.25);
        }

        .leaderboard-info {
            text-align: center;
            color: var(--color-text-muted);
            font-size: 0.9rem;
            margin-bottom: var(--space-lg);
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="leaderboard-header">
            <h1>Rangliste</h1>
            <p class="leaderboard-info">
                Klicke auf einen Eintrag, um zwischen Benutzer- und Teamansicht zu wechseln
            </p>
        </div>

        <div style="text-align: center;">
            <div class="view-toggle">
                <button
                    class="<?= $currentType === 'users' || !$currentType ? 'active' : '' ?>"
                    onclick="setType('users')"
                >
                    Alle Benutzer
                </button>
                <button
                    class="<?= $currentType === 'teams' ? 'active' : '' ?>"
                    onclick="setType('teams')"
                >
                    Teams
                </button>
                <?php if ($isLoggedIn): ?>
                <button
                    class="<?= $currentType === 'my-team' ? 'active' : '' ?>"
                    onclick="setType('my-team')"
                >
                    Mein Team
                </button>
                <?php endif; ?>
            </div>
        </div>

        <ul class="stat-list" id="leaderboard">
            <?php if ($viewUsers): ?>
                <?php foreach ($users as $index => $user): ?>
                    <li>
                        <span class="name"><?= htmlspecialchars($user->getDisplayName()) ?></span>
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
        function setType(type) {
            const url = new URL(window.location.href);
            url.searchParams.set('type', type);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
