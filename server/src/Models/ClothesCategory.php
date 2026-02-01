<?php

namespace App\Models;

class ClothesCategory extends AbstractCategory
{
    protected function buildQuery(): array
    {
        return [
            "SELECT * FROM products WHERE category = ?",
            [$this->name]
        ];
    }
}
