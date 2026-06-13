<?php

declare(strict_types=1);

use MegSEO\Support\ImmutableMap;
use MegSEO\Support\OrderedChecks;
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;

test('ImmutableMap stores and retrieves values', function () {
    $map = new ImmutableMap(['key' => 'value', 'count' => 42]);

    expect($map->get('key'))->toBe('value');
    expect($map->get('count'))->toBe(42);
    expect($map->get('missing'))->toBeNull();
    expect($map->get('missing', 'default'))->toBe('default');
});

test('ImmutableMap has and count', function () {
    $map = new ImmutableMap(['a' => 1, 'b' => 2]);

    expect($map->has('a'))->toBeTrue();
    expect($map->has('c'))->toBeFalse();
    expect($map->count())->toBe(2);
});

test('ImmutableMap cannot be mutated via array access', function () {
    $map = new ImmutableMap(['key' => 'value']);

    $map['key'] = 'new-value';
})->throws(\BadMethodCallException::class);

test('ImmutableMap cannot unset via array access', function () {
    $map = new ImmutableMap(['key' => 'value']);

    unset($map['key']);
})->throws(\BadMethodCallException::class);

test('ImmutableMap toArray returns all items', function () {
    $map = new ImmutableMap(['a' => 1, 'b' => 2]);

    expect($map->toArray())->toBe(['a' => 1, 'b' => 2]);
});

test('ImmutableMap is iterable', function () {
    $map = new ImmutableMap(['a' => 1, 'b' => 2]);
    $result = [];

    foreach ($map as $key => $value) {
        $result[$key] = $value;
    }

    expect($result)->toBe(['a' => 1, 'b' => 2]);
});

test('ImmutableMap offset exists', function () {
    $map = new ImmutableMap(['key' => 'value']);

    expect(isset($map['key']))->toBeTrue();
    expect(isset($map['missing']))->toBeFalse();
});

test('OrderedChecks preserves insertion order', function () {
    $checks = new OrderedChecks();

    $check1 = createStubCheck('check.one');
    $check2 = createStubCheck('check.two');
    $check3 = createStubCheck('check.three');

    $checks->add($check1);
    $checks->add($check2);
    $checks->add($check3);

    $all = $checks->all();

    expect($all)->toHaveCount(3);
    expect($all[0]->ref()->id)->toBe('check.one');
    expect($all[1]->ref()->id)->toBe('check.two');
    expect($all[2]->ref()->id)->toBe('check.three');
});

test('OrderedChecks does not add duplicate check with same id', function () {
    $checks = new OrderedChecks();

    $check1 = createStubCheck('check.dupe');
    $check2 = createStubCheck('check.dupe');

    $checks->add($check1);
    $checks->add($check2);

    expect($checks->count())->toBe(1);
});

test('OrderedChecks can remove check by id', function () {
    $checks = new OrderedChecks();

    $check = createStubCheck('check.remove');
    $checks->add($check);
    $checks->remove('check.remove');

    expect($checks->count())->toBe(0);
});

test('OrderedChecks has check by id', function () {
    $checks = new OrderedChecks();

    $check = createStubCheck('check.exist');
    $checks->add($check);

    expect($checks->has('check.exist'))->toBeTrue();
    expect($checks->has('check.none'))->toBeFalse();
});

test('OrderedChecks is iterable', function () {
    $checks = new OrderedChecks();
    $checks->add(createStubCheck('check.a'));
    $checks->add(createStubCheck('check.b'));

    $ids = [];
    foreach ($checks as $check) {
        $ids[] = $check->ref()->id;
    }

    expect($ids)->toBe(['check.a', 'check.b']);
});

test('OrderedChecks count returns zero for empty', function () {
    $checks = new OrderedChecks();

    expect($checks->count())->toBe(0);
});

/**
 * Create a stub check with the given id for testing.
 */
function createStubCheck(string $id): Check
{
    return new class($id) implements Check
    {
        private readonly string $id;

        public function __construct(string $id) {
            $this->id = $id;
        }

        public function ref(): CheckReference
        {
            return new CheckReference($this->id, "Stub: {$this->id}");
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };
}
