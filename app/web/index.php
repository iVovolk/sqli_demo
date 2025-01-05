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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="roboto-regular">
<section class="progress">
    <?= $renderedStepProgress($currentStep) ?>
</section>
<section class="container">
    <h3 class="roboto-bold"><?= $stepTitle($currentStep) ?></h3>
    <div class="step-content">
        <?= $renderedStep($currentStep) ?>
    </div>
</section>
</body>
</html>
<?php ob_get_flush(); ?>
