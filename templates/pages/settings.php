<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Einstellungen - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <style>
        .settings-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .settings-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .account-card {
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            padding: var(--space-xl);
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-xl);
        }

        .account-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .account-avatar svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .account-details {
            flex: 1;
            min-width: 0;
        }

        .account-name {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 600;
            color: light-dark(var(--forest-deep), var(--mint-soft));
            margin-bottom: var(--space-xs);
        }

        .account-email {
            color: var(--color-text-muted);
            font-size: 0.95rem;
        }

        .settings-section {
            margin-bottom: var(--space-lg);
        }

        .settings-section h2 {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted);
            margin-bottom: var(--space-md);
            padding-bottom: var(--space-sm);
            border-bottom: 1px solid var(--color-border);
        }

        .setting-item {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-sm);
            overflow: hidden;
        }

        .setting-item summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-md) var(--space-lg);
            cursor: pointer;
            list-style: none;
        }

        .setting-item summary::-webkit-details-marker {
            display: none;
        }

        .setting-item summary::marker {
            display: none;
        }

        .setting-info {
            flex: 1;
            min-width: 0;
        }

        .setting-label {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin-bottom: 2px;
        }

        .setting-value {
            font-weight: 500;
            color: var(--color-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .setting-value.empty {
            color: var(--color-text-muted);
            font-style: italic;
        }

        .setting-value.masked {
            letter-spacing: 0.1em;
        }

        .edit-btn {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            padding: var(--space-xs) var(--space-sm);
            font-size: 0.85rem;
            color: light-dark(var(--forest-medium), var(--mint-fresh));
            background: transparent;
            border: 1px solid currentColor;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .edit-btn:hover {
            background: light-dark(var(--forest-medium), var(--mint-fresh));
            color: white;
        }

        .edit-btn svg {
            width: 14px;
            height: 14px;
            fill: currentColor;
        }

        .setting-item[open] .edit-btn {
            display: none;
        }

        .setting-form {
            padding: 0 var(--space-lg) var(--space-lg);
            border-top: 1px solid var(--color-border);
            background: light-dark(rgba(0,0,0,0.02), rgba(255,255,255,0.02));
        }

        .setting-form .form-group {
            margin-top: var(--space-md);
        }

        .setting-form .form-group:first-child {
            margin-top: var(--space-lg);
        }

        .form-actions {
            display: flex;
            gap: var(--space-sm);
            margin-top: var(--space-md);
        }

        .form-actions .btn {
            flex: 1;
        }

        .btn-cancel {
            background: transparent;
            border: 1px solid var(--color-border);
            color: var(--color-text-muted);
        }

        .btn-cancel:hover {
            background: var(--color-bg-card);
            border-color: var(--color-text-muted);
        }

        .setting-form .success {
            margin-top: var(--space-md);
            margin-bottom: 0;
        }

        .setting-form .error {
            margin-top: var(--space-md);
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <div class="settings-container">
            <div class="settings-header">
                <h1>Einstellungen</h1>
            </div>

            <div class="account-card">
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

            <div class="settings-section">
                <h2>Profil</h2>

                <details class="setting-item" <?= ($success && strpos($success, 'Name') !== false) || ($error && strpos($error, 'Name') !== false) ? 'open' : '' ?>>
                    <summary>
                        <div class="setting-info">
                            <div class="setting-label">Name</div>
                            <div class="setting-value <?= empty($firstName) && empty($lastName) ? 'empty' : '' ?>">
                                <?= !empty($firstName) || !empty($lastName)
                                    ? htmlspecialchars(trim($firstName . ' ' . $lastName))
                                    : 'Nicht angegeben' ?>
                            </div>
                        </div>
                        <span class="edit-btn">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Bearbeiten
                        </span>
                    </summary>
                    <div class="setting-form">
                        <?php if ($success && strpos($success, 'Name') !== false): ?>
                            <p class="success"><?= htmlspecialchars($success) ?></p>
                        <?php endif; ?>
                        <form method="post" action="/settings/name">
                            <div class="form-group">
                                <label for="first_name">Vorname</label>
                                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($firstName) ?>" placeholder="Dein Vorname">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Nachname</label>
                                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($lastName) ?>" placeholder="Dein Nachname">
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </details>

                <details class="setting-item" <?= ($success && strpos($success, 'Benutzername') !== false) || ($error && strpos($error, 'Benutzername') !== false) ? 'open' : '' ?>>
                    <summary>
                        <div class="setting-info">
                            <div class="setting-label">Benutzername</div>
                            <div class="setting-value"><?= htmlspecialchars($username) ?></div>
                        </div>
                        <span class="edit-btn">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Bearbeiten
                        </span>
                    </summary>
                    <div class="setting-form">
                        <?php if ($success && strpos($success, 'Benutzername') !== false): ?>
                            <p class="success"><?= htmlspecialchars($success) ?></p>
                        <?php endif; ?>
                        <?php if ($error && strpos($error, 'Benutzername') !== false): ?>
                            <p class="error"><?= htmlspecialchars($error) ?></p>
                        <?php endif; ?>
                        <form method="post" action="/settings/username">
                            <div class="form-group">
                                <label for="username">Neuer Benutzername</label>
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Mindestens 3 Zeichen" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>

            <div class="settings-section">
                <h2>Sicherheit</h2>

                <details class="setting-item" <?= ($success && strpos($success, 'E-Mail') !== false) || ($error && (strpos($error, 'E-Mail') !== false || strpos($error, 'Bestätigung') !== false)) ? 'open' : '' ?>>
                    <summary>
                        <div class="setting-info">
                            <div class="setting-label">E-Mail-Adresse</div>
                            <div class="setting-value"><?= htmlspecialchars($email) ?></div>
                        </div>
                        <span class="edit-btn">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Bearbeiten
                        </span>
                    </summary>
                    <div class="setting-form">
                        <?php if ($success && strpos($success, 'E-Mail') !== false): ?>
                            <p class="success"><?= htmlspecialchars($success) ?></p>
                        <?php endif; ?>
                        <?php if ($error && (strpos($error, 'E-Mail') !== false || strpos($error, 'Bestätigung') !== false)): ?>
                            <p class="error"><?= htmlspecialchars($error) ?></p>
                        <?php endif; ?>
                        <form method="post" action="/settings/email">
                            <div class="form-group">
                                <label for="email">Neue E-Mail-Adresse</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email_password">Passwort zur Bestätigung</label>
                                <input type="password" id="email_password" name="password" autocomplete="current-password" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </details>

                <details class="setting-item" <?= ($success && strpos($success, 'Passwort') !== false) || ($error && (strpos($error, 'Aktuelles') !== false || strpos($error, 'Neue') !== false || strpos($error, 'alle Felder') !== false)) ? 'open' : '' ?>>
                    <summary>
                        <div class="setting-info">
                            <div class="setting-label">Passwort</div>
                            <div class="setting-value masked">••••••••</div>
                        </div>
                        <span class="edit-btn">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Bearbeiten
                        </span>
                    </summary>
                    <div class="setting-form">
                        <?php if ($success && strpos($success, 'Passwort') !== false): ?>
                            <p class="success"><?= htmlspecialchars($success) ?></p>
                        <?php endif; ?>
                        <?php if ($error && (strpos($error, 'Aktuelles') !== false || strpos($error, 'Neue') !== false || strpos($error, 'alle Felder') !== false)): ?>
                            <p class="error"><?= htmlspecialchars($error) ?></p>
                        <?php endif; ?>
                        <form method="post" action="/settings">
                            <div class="form-group">
                                <label for="current_password">Aktuelles Passwort</label>
                                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">Neues Passwort</label>
                                <input type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="Mindestens 6 Zeichen" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Neues Passwort bestätigen</label>
                                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </div>
</body>
</html>
