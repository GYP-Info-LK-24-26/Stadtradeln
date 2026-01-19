<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>
        
        <form name="loginForm" method="post" action="/login">
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($email ?? '') ?>" 
                    autocomplete="username"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="password">Passwort</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    autocomplete="current-password"
                    required
                >
            </div>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Einloggen</button>
                <a href="/" class="btn btn-secondary">Abbrechen</a>
            </div>

            <p class="form-footer">
                Du hast noch keinen Account? 
                <a href="/register<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
                    Registrieren
                </a>
            </p>
        </form>
    </div>
</body>
</html>
