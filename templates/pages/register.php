<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrieren</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1>Registrieren</h1>
        
        <form name="registerForm" method="post" action="/register">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($data['email'] ?? '') ?>" 
                    autocomplete="username"
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
                    required
                >
            </div>

            <div class="form-group">
                <label for="first_name">Vorname</label>
                <input 
                    type="text" 
                    id="first_name" 
                    name="first_name" 
                    value="<?= htmlspecialchars($data['first_name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="last_name">Nachname</label>
                <input 
                    type="text" 
                    id="last_name" 
                    name="last_name" 
                    value="<?= htmlspecialchars($data['last_name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Passwort bestätigen</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password"
                    required
                >
            </div>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrieren</button>
                <button type="reset" class="btn btn-secondary">Zurücksetzen</button>
                <a href="/" class="btn btn-danger">Abbrechen</a>
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
