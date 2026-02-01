<?php

namespace App\Models;

class AllCategory extends AbstractCategory
{
    protected function buildQuery(): array
    {
        return [
            "SELECT * FROM products",
            []
        ];
    }

}
