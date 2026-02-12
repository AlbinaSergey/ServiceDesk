<div class="alert alert-danger" role="alert">
    <h1 class="h4 mb-2">Ошибка <?= e((string) ($status ?? 500)) ?></h1>
    <p class="mb-0"><?= e($message ?? 'Произошла ошибка.') ?></p>
</div>
