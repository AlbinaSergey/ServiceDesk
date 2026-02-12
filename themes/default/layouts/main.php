<?php
/** @var string $pageFile */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'ЕРЗСС ServiceDesk') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">ЕРЗСС ServiceDesk</span>
    </div>
</nav>

<main class="container py-2">
    <?php require $pageFile; ?>
</main>
</body>
</html>
