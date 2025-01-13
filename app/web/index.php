<?php

require_once "../inc/functions.php";

/**
 * @var callable(int $currentStep):string $renderedStepProgress
 * @var callable(int $currentStep):string $stepTitle
 * @var callable(int $currentStep):string $renderedStep
 * @var int $currentStep
 */
?>
<?php ob_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SQLI | Несколько заданий, чтобы разобраться и попрактиковаться с некоторыми видами SQL инъекций.</title>
    <meta name="description"
          content="Обход авторизации, UNION инъекции, обход некоторых фильтров, слепые инъекции, инъекции второго порядка">
    <meta name="author" content="https://github.com/iVovolk">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<section class="progress">
    <?= $renderedStepProgress($currentStep) ?>
</section>
<section class="container">
    <h3><?= $stepTitle($currentStep) ?></h3>
    <div class="step-content">
        <?= $renderedStep($currentStep) ?>
    </div>
</section>
<section class="footer">
    <a href="https://github.com/iVovolk/sqli_demo" target="_blank">Исходники</a>
    <a href="/help.html" target="_blank">Подсказки</a>
</section>
</body>
</html>
<?php ob_get_flush(); ?>
