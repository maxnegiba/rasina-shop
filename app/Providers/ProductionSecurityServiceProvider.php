<?php

namespace App\Providers;

use App\Support\ProductionSecurity;
use Illuminate\Support\ServiceProvider;
use LogicException;

class ProductionSecurityServiceProvider extends ServiceProvider
{
    public function boot(ProductionSecurity $security): void
    {
        if (! app()->isProduction()) {
            return;
        }

        $violations = $security->violations();

        if ($violations !== []) {
            throw new LogicException("Unsafe production configuration:\n- ".implode("\n- ", $violations));
        }
    }
}
