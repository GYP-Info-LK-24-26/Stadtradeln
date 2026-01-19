<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team erstellen/beitreten</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <link rel="stylesheet" href="/css/components/list.css">
    <link rel="stylesheet" href="/css/components/popup.css">
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>
    
    <div class="page-content">
        <div class="team-selector">
            <button class="btn btn-primary" onclick="openCreatePopup()">
                Team erstellen
            </button>

            <div class="form-group">
                <label for="teamSearch" class="sr-only">Team suchen</label>
                <input 
                    type="text" 
                    id="teamSearch" 
                    placeholder="Team suchen..." 
                    oninput="filterTeams()"
                >
            </div>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="/team/join" id="joinForm">
                <input type="hidden" name="team_name" id="selectedTeam">
                
                <ul class="stat-list click-list" id="teamList">
                    <?php foreach ($teams as $team): ?>
                        <li data-team="<?= htmlspecialchars($team->name) ?>">
                            <span class="name"><?= htmlspecialchars($team->name) ?></span>
                            <span class="big"><?= $team->memberCount ?> Mitglieder</span>
                            <span class="small"><?= number_format($team->totalDistance, 1) ?> km</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
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
                        <input type="text" id="team_name" name="team_name" placeholder="Team Name" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Erstellen</button>
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
        document.getElementById('teamList').addEventListener('click', function(e) {
            const li = e.target.closest('li');
            if (li) {
                const teamName = li.dataset.team;
                document.getElementById('selectedTeam').value = teamName;
                document.getElementById('joinForm').submit();
            }
        });

        // Close popup when clicking outside
        document.getElementById('createPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreatePopup();
            }
        });
    </script>
</body>
</html>
