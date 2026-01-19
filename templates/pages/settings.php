<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Einstellungen - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <style>
        .settings-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .account-info {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-lg);
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-xl);
        }

        .account-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .account-avatar svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .account-details {
            flex: 1;
            min-width: 0;
        }

        .account-name {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 600;
            color: light-dark(var(--forest-deep), var(--mint-soft));
            margin-bottom: var(--space-xs);
        }

        .account-email {
            color: var(--color-text-muted);
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .settings-grid {
            display: grid;
            gap: var(--space-lg);
        }

        @media (min-width: 768px) {
            .settings-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .settings-section.full-width {
                grid-column: 1 / -1;
            }
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="settings-header">
            <h1>Einstellungen</h1>
        </div>

        <div class="account-info">
            <div class="account-avatar">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <div class="account-details">
                <div class="account-name"><?= htmlspecialchars($username) ?></div>
                <div class="account-email"><?= htmlspecialchars($email) ?></div>
            </div>
        </div>

        <div class="settings-grid">
            <div class="settings-section">
                <h2>Name ändern</h2>

                <?php if ($success && strpos($success, 'Name') !== false): ?>
                    <p class="success"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>

                <form method="post" action="/settings/name">
                    <div class="form-group">
                        <label for="first_name">Vorname</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= htmlspecialchars($firstName) ?>"
                            placeholder="Dein Vorname"
                        >
                    </div>

                    <div class="form-group">
                        <label for="last_name">Nachname</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars($lastName) ?>"
                            placeholder="Dein Nachname"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Name speichern</button>
                </form>
            </div>

            <div class="settings-section">
                <h2>Passwort ändern</h2>

                <?php if ($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if ($success && strpos($success, 'Passwort') !== false): ?>
                    <p class="success"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>

                <form method="post" action="/settings">
                    <div class="form-group">
                        <label for="current_password">Aktuelles Passwort</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="new_password">Neues Passwort</label>
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            autocomplete="new-password"
                            placeholder="Mindestens 6 Zeichen"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Neues Passwort bestätigen</label>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Passwort ändern</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
