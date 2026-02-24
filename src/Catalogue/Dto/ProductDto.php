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

        #[Groups(["order:create"])]
        #[OA\Property(description: "ID du produit à commander")]
        public string $product_id,

        #[Groups(['product:create','product:list', 'order:create'])]
        #[OA\Property(description: "Nom du produit")]
        public string $name,

        #[Groups(['order:create'])]
        #[OA\Property(description:'Quantité du produit à commander')]
        public int $quantity,

        #[Groups(['product:create', 'order:create'])]
        #[OA\Property(description: "Prix du produit")]
        public int $price,

        #[Groups(['product:list'])]
        #[OA\Property(description: "Prix du produit en valeur majeure", example: "19.99")]
        public string $majorPrice,

        #[Groups(['product:list'])]
        #[OA\Property(description: "Quantité disponible en stock (onHand - onHold)")]
        public int $availableQuantity,

        #[Groups(['product:create'])]
        #[OA\Property(description: "Indique si le produit est disponible à la vente (en stock)", type: "boolean")]
        public bool $on_hand = false,
    ) {
    }
}