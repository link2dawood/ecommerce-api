<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
class ProductObserver
{
        public function saved(Product $product)
    {
        Cache::tags(['products'])->flush();
    }

    public function deleted(Product $product)
    {
        Cache::tags(['products'])->flush();
    }
}
