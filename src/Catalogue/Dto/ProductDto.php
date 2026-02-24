<?php

namespace App\Catalogue\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

readonly class ProductDto
{
    public function __construct(
        #[Groups(['product:list', 'product:detail'])]
        #[OA\Property(description: "ID du produit")]
        public string $id,

        #[Groups(['product:create','product:list'])]
        #[OA\Property(description: "Nom du produit")]
        public string $name,

        #[Groups(['product:create', 'product:list'])]
        #[OA\Property(description: "Prix du produit")]
        public int $price,

        #[Groups(['product:list'])]
        public int $quantity,

        #[Groups(['product:create'])]
        #[OA\Property(description: "Indique si le produit est disponible à la vente (en stock)", type: "boolean")]
        public bool $on_hand = false,
    ) {
    }
}