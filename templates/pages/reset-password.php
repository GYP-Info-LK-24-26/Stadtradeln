<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Neues Passwort - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/components/nav.css">
    <style>
        .auth-wrapper {
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-xl) var(--space-md);
            background:
                radial-gradient(ellipse at 30% 20%, rgba(82, 183, 136, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(212, 165, 116, 0.06) 0%, transparent 50%);
        }

        .auth-container {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-logo {
            width: 64px;
            height: 64px;
            margin: 0 auto var(--space-lg);
            background: linear-gradient(135deg, var(--forest-light), var(--mint-fresh));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(64, 145, 108, 0.25);
        }

        .auth-logo svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .auth-description {
            color: var(--color-text-muted);
            text-align: center;
            margin-bottom: var(--space-lg);
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../layout/nav.php'; ?>

    <div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 6a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7-8h3l2 4h-4l-1-4zm-2 0L8 8H5V6h4l1-2zm3 4l2 4H9l-1-4h5z"/>
            </svg>
        </div>

        <h1>Neues Passwort</h1>

        <?php if ($valid): ?>
            <p class="auth-description">
                Gib dein neues Passwort ein.
            </p>

            <form method="post" action="/reset-password">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label for="password">Neues Passwort</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="Mindestens 6 Zeichen"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password">Passwort bestätigen</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        autocomplete="new-password"
                        placeholder="Passwort wiederholen"
                        required
                    >
                </div>

                <?php if (!empty($error)): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Passwort ändern</button>
                </div>
            </form>
        <?php else: ?>
            <p class="auth-description">
                <?= htmlspecialchars($error) ?>
            </p>

            <div class="form-actions">
                <a href="/forgot-password" class="btn btn-primary">Neuen Link anfordern</a>
                <a href="/login" class="btn btn-secondary">Zur Anmeldung</a>
            </div>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>
