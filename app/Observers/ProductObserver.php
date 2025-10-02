<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
class ProductObserver
{
        public function saved(Product $product)
    {
       Cache::forget('all_products');

    }

    public function deleted(Product $product)
    {
       Cache::forget('all_products');

    }
}
