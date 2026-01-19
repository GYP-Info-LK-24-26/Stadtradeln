<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Einstellungen</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="page-content">
        <h1>Einstellungen</h1>

        <div class="settings-section">
            <h2>Kontoinformationen</h2>
            <p><strong>Benutzername:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>E-Mail:</strong> <?= htmlspecialchars($email) ?></p>
        </div>

        <div class="settings-section">
            <h2>Passwort ändern</h2>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="post" action="/settings">
                <div class="form-group">
                    <label for="current_password">Aktuelles Passwort</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Neues Passwort bestätigen</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Passwort ändern</button>
            </form>
        </div>
    </div>
</body>
</html>
