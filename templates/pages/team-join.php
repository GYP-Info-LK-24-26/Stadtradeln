<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team beitreten - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <link rel="stylesheet" href="/css/components/popup.css">
    <style>
        .team-join-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .team-join-header h1 {
            margin-bottom: var(--space-sm);
        }

        .team-join-header p {
            color: var(--color-text-muted);
        }

        .team-actions {
            display: flex;
            gap: var(--space-md);
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: var(--space-xl);
        }

        .search-box {
            flex: 1;
            min-width: 200px;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: var(--space-sm) var(--space-md);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 1rem;
            background: var(--color-bg-card);
            color: var(--color-text);
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--mint-fresh);
            box-shadow: 0 0 0 3px rgba(82, 183, 136, 0.2);
        }

        .team-list-info {
            text-align: center;
            color: var(--color-text-muted);
            font-size: 0.9rem;
            margin-bottom: var(--space-md);
        }

        /* Override stat-list for team selection */
        .team-selector .stat-list > li {
            cursor: pointer;
        }

        .team-selector .stat-list > li:hover {
            border-color: var(--mint-fresh);
            background: light-dark(var(--cream-mid), var(--night-forest));
        }

        .empty-teams {
            text-align: center;
            padding: var(--space-2xl);
            color: var(--color-text-muted);
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="team-join-header">
            <h1>Team beitreten</h1>
            <p>Wähle ein bestehendes Team aus oder erstelle dein eigenes</p>
        </div>

        <div class="team-actions">
            <button class="btn btn-primary" onclick="openCreatePopup()">
                + Neues Team erstellen
            </button>

            <div class="search-box">
                <label for="teamSearch" class="sr-only">Team suchen</label>
                <input
                    type="text"
                    id="teamSearch"
                    placeholder="Team suchen..."
                    oninput="filterTeams()"
                >
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <p class="error" style="text-align: center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <p class="team-list-info">Klicke auf ein Team, um beizutreten</p>

        <div class="team-selector">
            <form method="post" action="/team/join" id="joinForm">
                <input type="hidden" name="team_name" id="selectedTeam">

                <?php if (empty($teams)): ?>
                    <div class="empty-teams">
                        <p>Noch keine Teams vorhanden. Sei der Erste und erstelle ein Team!</p>
                    </div>
                <?php else: ?>
                    <ul class="stat-list click-list" id="teamList">
                        <?php foreach ($teams as $team): ?>
                            <li data-team="<?= htmlspecialchars($team->name) ?>">
                                <span class="name"><?= htmlspecialchars($team->name) ?></span>
                                <span class="big"><?= $team->memberCount ?></span>
                                <span class="small"><?= number_format($team->totalDistance, 1) ?> km gefahren</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </form>
        </div>

        <!-- Create Team Popup -->
        <div class="popup-overlay" id="createPopup" <?= $showCreate ? 'style="display:block"' : '' ?>>
            <div class="popup">
                <span class="close" onclick="closeCreatePopup()">&times;</span>

                <h3>Neues Team erstellen</h3>

                <form method="post" action="/team/join">
                    <input type="hidden" name="type" value="create">

                    <div class="form-group">
                        <label for="team_name">Teamname</label>
                        <input
                            type="text"
                            id="team_name"
                            name="team_name"
                            placeholder="z.B. Die Radler"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Team erstellen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreatePopup() {
            document.getElementById('createPopup').style.display = 'block';
        }

        function closeCreatePopup() {
            document.getElementById('createPopup').style.display = 'none';
        }

        function filterTeams() {
            const term = document.getElementById('teamSearch').value.toLowerCase();
            const items = document.querySelectorAll('#teamList li');

            items.forEach(item => {
                const teamName = item.dataset.team.toLowerCase();
                item.style.display = teamName.includes(term) ? '' : 'none';
            });
        }

        // Click to select team
        document.getElementById('teamList')?.addEventListener('click', function(e) {
            const li = e.target.closest('li');
            if (li) {
                const teamName = li.dataset.team;
                if (confirm(`Möchtest du dem Team "${teamName}" beitreten?`)) {
                    document.getElementById('selectedTeam').value = teamName;
                    document.getElementById('joinForm').submit();
                }
            }
        });

        // Close popup when clicking outside
        document.getElementById('createPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreatePopup();
            }
        });

        // Close popup with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreatePopup();
            }
        });
    </script>
</body>
</html>
