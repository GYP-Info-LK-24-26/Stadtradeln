<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>
    
    <div class="page-content">
        <?php if (!$hasTeam): ?>
            <div class="no-team">
                <p>Du bist nicht Mitglied eines Teams</p>
                <a href="/team/join" class="btn btn-primary">Team erstellen/beitreten</a>
            </div>
        <?php else: ?>
            <div class="team-overview">
                <h2><?= htmlspecialchars($team->name) ?></h2>
                
                <div class="team-stats">
                    <p>
                        Dein Team hat <strong><?= number_format($stats['totalDistance'], 1) ?></strong> Kilometer<br>
                        in <strong><?= $stats['totalTours'] ?></strong> Touren gesammelt<br>
                        mit einem Schnitt von <strong><?= number_format($stats['averageDistance'], 1) ?></strong> km/Tour
                    </p>
                </div>

                <h3>
                    Team (<?= count($members) ?> Mitglieder)
                    <a href="/leaderboard?type=team">Zur Teamrangliste</a>
                </h3>

                <ul class="stat-list">
                    <?php foreach ($members as $member): ?>
                        <li>
                            <span class="name">
                                <?= htmlspecialchars($member->userName) ?>
                                <?= $member->userID === $userId ? ' (Du)' : '' ?>
                                <?= $member->userID === $team->captainId ? ' (Captain)' : '' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
