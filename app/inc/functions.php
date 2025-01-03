<?php

require_once "steps.php";
require_once 'db.php';

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'strict']);
$_SESSION['step'] = $_SESSION['step'] ?? 0;
$_SESSION['solved'] = $_SESSION['solved'] ?? [];
$sessionStep = $_SESSION['step'];

$stepIsSolved = static fn (int $step) => in_array($step, $_SESSION['solved'], true);

$mayBeStep = (int)($_GET['s'] ?? $sessionStep);
if ($sessionStep === $mayBeStep || $stepIsSolved($mayBeStep)) {
    $currentStep = $mayBeStep;
} else {
    header('Location: /', true, 302);
    exit();
}

$isLastStep = static fn (int $step) => ++$step === count(STEPS);

$renderedStepProgress = static function (int $currentStep) use ($stepIsSolved, $sessionStep): string {
    $rendered = '';
    foreach (STEPS as $i => $step) {
        if ($stepIsSolved($i)) {
            $rendered .= "<a class='step done' href='?s=$i'>&#10003;</a>";
        } elseif ($i === $currentStep) {
            $rendered .= "<div class='step {$step['level']} current'></div>";
        } elseif ($i === $sessionStep) {
            $rendered .= "<a class='step {$step['level']} session' href='?s=$i'></a>";
        } else {
            $rendered .= "<div class='step {$step['level']}'></div>";
        }
    }
    return $rendered;
};

$stepTitle = static fn (int $currentStep) => $isLastStep($currentStep) && $stepIsSolved($currentStep)
    ? "Всё победил, больше пока нету."
    : STEPS[$currentStep]['title'] ?? 'Не знаю такого уровня';

$nextStep = static function () use ($currentStep, $stepIsSolved): void {
    if ($stepIsSolved($currentStep)) {
        return;
    }
    $_SESSION['solved'][] = $currentStep;
    $numSteps = count(STEPS);
    ++$currentStep;
    if ($currentStep >= $numSteps) {
        return;
    }
    $_SESSION['step'] = $currentStep;
};

/** @var mysqli $mysqli */
$renderedStep = static function (int $currentStep) use ($nextStep, $isLastStep, $stepIsSolved, $mysqli): string {
    if ($isLastStep($currentStep) && $stepIsSolved($currentStep)) {
        return "Ну красавчик, а!";
    }

    $stepHandler = STEPS[$currentStep]['handler'] ?? '';
    if (function_exists($stepHandler)) {
        $stepRes = $stepHandler($mysqli);
        if (isset($stepRes['solved']) && $stepRes['solved'] === true) {
            $nextStep();
            header("Location: /", true, 303);
            exit();
        }
        return $stepRes['content'];
    }
    return "Тут пока еще не наговнокодили.";
};

