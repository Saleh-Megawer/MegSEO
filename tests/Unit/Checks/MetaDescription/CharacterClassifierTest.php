<?php

declare(strict_types=1);

use MegSEO\Checks\MetaDescription\Support\MetaDescriptionCharacterClassifier;

test('Classifier detects meaningful text', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->containsMeaningfulText('Hello Description'))->toBeTrue();
    expect($c->containsMeaningfulText('وصف الصفحة'))->toBeTrue();
});

test('Classifier rejects punctuation-only text', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->isPunctuationOnly('!!!...???;;;'))->toBeTrue();
    expect($c->containsMeaningfulText('!!!...???;;;'))->toBeFalse();
});

test('Classifier rejects whitespace-only text', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->containsMeaningfulText('     '))->toBeFalse();
});

test('Classifier rejects separator-only text', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->containsMeaningfulText('---===___'))->toBeFalse();
});

test('Classifier handles mixed scripts', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->containsMeaningfulText('Hello مرحبا Description'))->toBeTrue();
});

test('Classifier handles empty string', function () {
    $c = new MetaDescriptionCharacterClassifier();
    expect($c->containsMeaningfulText(''))->toBeFalse();
    expect($c->isPunctuationOnly(''))->toBeFalse();
});
