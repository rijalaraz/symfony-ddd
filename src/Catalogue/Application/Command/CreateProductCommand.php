<?php

namespace App\Catalogue\Application\Command;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class CreateProductCommand
{
    public function __construct(
        #[Groups(['product:list', 'product:detail'])]
        public string $id,
        #[Groups(['product:create'])]
        public string $name,
        #[Groups(['product:create'])]
        public int $price,
        #[Groups(['product:create'])]
        public int $onHand,
    ) {
    }
}
