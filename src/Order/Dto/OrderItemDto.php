<?php

namespace App\Order\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

readonly class OrderItemDto
{
    public function __construct(
        #[OA\Property(description: "ID du produit commandé")]
        #[Groups(['order:list', 'order:detail'])]
        public int $id,

        #[OA\Property(description: "Nom du produit commandé")]
        #[Groups(['order:list'])]
        public string $name,

        #[OA\Property(description:"Quantité du produit commandé")]
        #[Groups(['order:list'])]
        public int $quantity,

        #[OA\Property(description:"Prix du produit commandé", example:"19.99")]
        #[Groups(['order:list'])]
        public string $price
    ) {
    }
}
