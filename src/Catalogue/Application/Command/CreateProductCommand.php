<?php

namespace App\Catalogue\Application\Command;

use App\Validator\NotZero;

readonly class CreateProductCommand
{
    public function __construct(
        public string $id,
        public string $name,
        #[NotZero]
        public int $price,
        public int $onHand,
    ) {
    }
}
