<?php

require 'vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;

$engine = Engine::make();

$context = new AnalysisContext(
    subject: 'test-page',
);

$result = $engine->analyze($context);

echo "Score: ";
var_dump($result->score());

echo "\nIssues: ";
var_dump($result->issues());

echo "\nWarnings: ";
var_dump($result->warnings());

echo "\nSuggestions: ";
var_dump($result->suggestions());

echo "\nFailures: ";
var_dump($result->failures);
