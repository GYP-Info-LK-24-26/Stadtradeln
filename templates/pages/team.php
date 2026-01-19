<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <style>
        .team-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .team-name {
            display: inline-flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .team-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            border-radius: var(--radius-md);
            margin-right: var(--space-sm);
        }

        .team-badge svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .team-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-xl);
        }

        .stat-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            text-align: center;
        }

        .stat-card-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--forest-light);
            line-height: 1;
            margin-bottom: var(--space-xs);
        }

        .stat-card-label {
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }

        .members-section h3 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: var(--space-sm);
            margin-bottom: var(--space-lg);
        }

        .members-section h3 a {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .member-badge {
            display: inline-block;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: var(--radius-sm);
            margin-left: var(--space-xs);
        }

        .member-badge.you {
            background: var(--mint-pale);
            color: var(--forest-deep);
        }

        .member-badge.captain {
            background: var(--sun-gold);
            color: var(--forest-deep);
        }

        .no-team-container {
            text-align: center;
            padding: var(--space-2xl);
        }

        .no-team-container svg {
            width: 80px;
            height: 80px;
            fill: var(--color-border);
            margin-bottom: var(--space-lg);
        }

        .no-team-container h2 {
            margin-bottom: var(--space-md);
        }

        .no-team-container p {
            color: var(--color-text-muted);
            margin-bottom: var(--space-xl);
        }

        .team-actions {
            margin-top: var(--space-xl);
            padding-top: var(--space-xl);
            border-top: 1px solid var(--color-border);
            text-align: center;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <?php if (!$hasTeam): ?>
            <div class="no-team-container">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                <h2>Noch kein Team</h2>
                <p>Du bist noch nicht Mitglied eines Teams. Tritt einem bestehenden Team bei oder erstelle dein eigenes!</p>
                <a href="/team/join" class="btn btn-primary btn-lg">Team beitreten oder erstellen</a>
            </div>
        <?php elseif ($team): ?>
            <div class="team-header">
                <h1>
                    <span class="team-badge">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </span>
                    <?= htmlspecialchars($team->name) ?>
                </h1>
            </div>

            <div class="team-stats-grid">
                <div class="stat-card">
                    <div class="stat-card-value"><?= number_format($stats['totalDistance'], 1) ?></div>
                    <div class="stat-card-label">Kilometer gesamt</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value"><?= $stats['totalTours'] ?></div>
                    <div class="stat-card-label">Touren</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value"><?= number_format($stats['averageDistance'], 1) ?></div>
                    <div class="stat-card-label">km pro Tour</div>
                </div>
            </div>

            <div class="members-section">
                <h3>
                    <span>Teammitglieder (<?= count($members) ?>)</span>
                    <a href="/leaderboard?type=team">Zur Teamrangliste</a>
                </h3>

                <ul class="stat-list">
                    <?php foreach ($members as $member): ?>
                        <li>
                            <span class="name">
                                <?= htmlspecialchars($member->username) ?>
                                <?php if ($member->id === $userId): ?>
                                    <span class="member-badge you">Du</span>
                                <?php endif; ?>
                                <?php if ($team && $member->id === $team->captainId): ?>
                                    <span class="member-badge captain">Captain</span>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="team-actions">
                <form method="post" action="/team/leave" onsubmit="return confirm('Möchtest du das Team wirklich verlassen?');">
                    <button type="submit" class="btn btn-danger">Team verlassen</button>
                </form>
            </div>
        <?php else: ?>
            <div class="no-team-container">
                <p class="error">Teamdaten konnten nicht geladen werden.</p>
                <a href="/team/join" class="btn btn-primary">Anderes Team wählen</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
