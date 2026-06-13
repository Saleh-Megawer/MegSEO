<?php

declare(strict_types=1);

use MegSEO\Contracts\Check;
use MegSEO\Contracts\ExecutionPolicy;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ExecutionDecision;
use MegSEO\Policy\FailFastExecutionPolicy;
use MegSEO\Policy\IsolateFailuresExecutionPolicy;
use MegSEO\Policy\StandardExecutionPolicies;

test('FailFastExecutionPolicy returns abort decision', function () {
    $policy = new FailFastExecutionPolicy();
    $check = epMakeStubCheck('test.check');
    $error = new \RuntimeException('Check failed');

    $decision = $policy->evaluate($error, $check, new AnalysisContext('test'));

    expect($decision)->toBeInstanceOf(ExecutionDecision::class);
    expect($decision->action)->toBe('abort');
    expect($decision->recordFailure)->toBeTrue();
    expect($decision->reason)->toContain('test.check');
    expect($decision->reason)->toContain('Check failed');
});

test('IsolateFailuresExecutionPolicy returns continue decision', function () {
    $policy = new IsolateFailuresExecutionPolicy();
    $check = epMakeStubCheck('test.check');
    $error = new \RuntimeException('Check failed');

    $decision = $policy->evaluate($error, $check, new AnalysisContext('test'));

    expect($decision)->toBeInstanceOf(ExecutionDecision::class);
    expect($decision->action)->toBe('continue');
    expect($decision->recordFailure)->toBeTrue();
    expect($decision->reason)->toContain('test.check');
});

test('StandardExecutionPolicies failFast returns correct type', function () {
    $policy = StandardExecutionPolicies::failFast();

    expect($policy)->toBeInstanceOf(FailFastExecutionPolicy::class);
    expect($policy)->toBeInstanceOf(ExecutionPolicy::class);
});

test('StandardExecutionPolicies isolateFailures returns correct type', function () {
    $policy = StandardExecutionPolicies::isolateFailures();

    expect($policy)->toBeInstanceOf(IsolateFailuresExecutionPolicy::class);
    expect($policy)->toBeInstanceOf(ExecutionPolicy::class);
});

test('ExecutionDecision is immutable readonly DTO', function () {
    $decision = new ExecutionDecision('continue', 'reason', true);

    expect($decision->action)->toBe('continue');
    expect($decision->reason)->toBe('reason');
    expect($decision->recordFailure)->toBeTrue();
});

function epMakeStubCheck(string $id): Check
{
    return new class($id) implements Check
    {
        private readonly string $id;

        public function __construct(string $id) { $this->id = $id; }

        public function ref(): CheckReference
        {
            return new CheckReference($this->id, "Check {$this->id}");
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };
}
