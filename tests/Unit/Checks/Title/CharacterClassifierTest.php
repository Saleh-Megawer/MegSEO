<?php

declare(strict_types=1);

use MegSEO\Checks\Title\Support\TitleCharacterClassifier;

test('TitleCharacterClassifier detects meaningful text', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText('Hello World'))->toBeTrue();
    expect($classifier->containsMeaningfulText('عنوان'))->toBeTrue();
});

test('TitleCharacterClassifier rejects punctuation-only text', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->isPunctuationOnly('!!!...???;;;'))->toBeTrue();
    expect($classifier->containsMeaningfulText('!!!...???;;;'))->toBeFalse();
});

test('TitleCharacterClassifier rejects whitespace-only text', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText('     '))->toBeFalse();
    expect($classifier->containsMeaningfulText("\t\n  "))->toBeFalse();
});

test('TitleCharacterClassifier rejects separator-only text', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText('---===___'))->toBeFalse();
});

test('TitleCharacterClassifier handles mixed scripts', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText('Hello World مرحبا'))->toBeTrue();
    expect($classifier->containsMeaningfulText('Test123@#$'))->toBeTrue();
});

test('TitleCharacterClassifier accepts Arabic text', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText('عنوان الصفحة الرئيسية'))->toBeTrue();
    expect($classifier->isPunctuationOnly('عنوان الصفحة الرئيسية'))->toBeFalse();
});

test('TitleCharacterClassifier empty string', function () {
    $classifier = new TitleCharacterClassifier();

    expect($classifier->containsMeaningfulText(''))->toBeFalse();
    expect($classifier->isPunctuationOnly(''))->toBeFalse();
});
