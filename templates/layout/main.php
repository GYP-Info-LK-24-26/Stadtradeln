<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Stadtradeln' ?></title>
    <link rel="stylesheet" href="/css/main.css">
    <?php if (isset($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="/css/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php if ($showNav ?? false): ?>
        <?php require __DIR__ . '/nav.php'; ?>
    <?php endif; ?>
    
    <main>
        <?= $content ?? '' ?>
    </main>

    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="/js/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
