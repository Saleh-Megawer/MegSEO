<?php

declare(strict_types=1);

use MegSEO\Contracts\Check;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Exceptions\DuplicateCheckIdentifierException;

test('Check contract exposes stable identifier via ref()', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('stable.id', 'Stable Check', '1.0.0');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $ref = $check->ref();

    expect($ref)->toBeInstanceOf(CheckReference::class);
    expect($ref->id)->toBe('stable.id');
    expect($ref->label)->toBe('Stable Check');
    expect($ref->version)->toBe('1.0.0');
});

test('Check identifier is stable across multiple calls to ref()', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('immutable.id', 'Immutable');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $ref1 = $check->ref();
    $ref2 = $check->ref();

    expect($ref1->id)->toBe($ref2->id);
    expect($ref1->id)->toBe('immutable.id');
});

test('RegistersChecks contract supports register, all, and count', function () {
    $engine = Engine::make();

    expect($engine)->toBeInstanceOf(RegistersChecks::class);
    expect($engine->count())->toBe(0);

    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('reg.one', 'One');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $engine->register($check);

    expect($engine->count())->toBe(1);
    expect($engine->all())->toHaveCount(1);
    expect($engine->all()[0]->ref()->id)->toBe('reg.one');
});

test('Duplicate check registration with same identifier throws exception', function () {
    $engine = Engine::make();

    $first = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('duplicate.id', 'First');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $second = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('duplicate.id', 'Second');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $engine->register($first);

    expect(fn () => $engine->register($second))
        ->toThrow(DuplicateCheckIdentifierException::class);
});

test('DuplicateCheckIdentifierException contains the conflicting identifier', function () {
    $engine = Engine::make();

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('conflict.id', 'Original');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    });

    try {
        $engine->register(new class implements Check
        {
            public function ref(): CheckReference
            {
                return new CheckReference('conflict.id', 'Duplicate');
            }

            public function analyze(AnalysisContext $context): CheckOutcome
            {
                return new CheckOutcome(check: $this->ref());
            }
        });
    } catch (DuplicateCheckIdentifierException $e) {
        expect($e->identifier)->toBe('conflict.id');
        expect($e->getMessage())->toContain('conflict.id');
    }
});

test('Check identifiers are unique per registry instance', function () {
    $engine = Engine::make();

    $ids = ['check.a', 'check.b', 'check.c'];

    foreach ($ids as $id) {
        $engine->register(new class($id) implements Check
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
        });
    }

    $all = $engine->all();
    $registeredIds = array_map(fn (Check $c) => $c->ref()->id, $all);

    expect($registeredIds)->toBe($ids);
    expect(count(array_unique($registeredIds)))->toBe(count($ids));
});

test('has() returns true for registered identifiers and false for unknowns', function () {
    $engine = Engine::make();

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('has.test', 'Has Test');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    });

    expect($engine->has('has.test'))->toBeTrue();
    expect($engine->has('nonexistent'))->toBeFalse();
});
