<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MegSEO\DTO\AnalysisResult analyze(\MegSEO\DTO\AnalysisContext $context)
 * @method static void register(\MegSEO\Contracts\Check $check)
 * @method static void registerCheck(\MegSEO\Contracts\Check $check)
 * @method static int count()
 * @method static bool has(string $id)
 * @method static array all()
 */
final class MegSEO extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'megseo.engine';
    }
}
