<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrieren - Stadtradeln</title>
    <link rel="stylesheet" href="/css/main.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-xl) var(--space-md);
            background:
                radial-gradient(ellipse at 30% 20%, rgba(82, 183, 136, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(212, 165, 116, 0.06) 0%, transparent 50%),
                var(--color-bg);
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-md);
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 18a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 6a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7-8h3l2 4h-4l-1-4zm-2 0L8 8H5V6h4l1-2zm3 4l2 4H9l-1-4h5z"/>
            </svg>
        </div>

        <h1>Account erstellen</h1>

        <form name="registerForm" method="post" action="/register">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                    autocomplete="username"
                    placeholder="deine@email.de"
                    required
                >
            </div>

            <div class="form-group">
                <label for="username">Benutzername</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= htmlspecialchars($data['username'] ?? '') ?>"
                    placeholder="Dein Benutzername"
                    required
                >
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Vorname (optional)</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?= htmlspecialchars($data['first_name'] ?? '') ?>"
                        placeholder="Max"
                    >
                </div>

                <div class="form-group">
                    <label for="last_name">Nachname (optional)</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?= htmlspecialchars($data['last_name'] ?? '') ?>"
                        placeholder="Mustermann"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
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
                <button type="submit" class="btn btn-primary">Registrieren</button>
                <button type="reset" class="btn btn-secondary">Zurücksetzen</button>
            </div>

            <p class="form-footer">
                Durch das Drücken von "Registrieren" stimmst du den
                <a href="#">Nutzungsbedingungen</a> zu.
            </p>
            <p class="form-footer">
                Du hast bereits einen Account? <a href="/login">Einloggen</a>
            </p>
        </form>
    </div>
</body>
</html>
