<?php

namespace App\Order\Dto;

use App\Catalogue\Dto\ProductDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

readonly class OrderDto
{
    public function __construct(
        #[Groups(['order:detail', 'order:list'])]
        #[OA\Property(description: "ID de la commande")]
        public string $id,

        #[Groups(["order:create"])]
        #[OA\Property(description: "Montant total de la commande")]
        public int $amount_to_pay,

        #[Groups(["order:list"])]
        #[OA\Property(description: "Statut de la commande")]
        public string $status,

        #[Groups(['order:create'])]
        #[OA\Property(type:'array', description: "Liste des produits commandés", items: new OA\Items(ref:new Model(type:ProductDto::class, groups: ['order:create'])))]
        public array $products,

        #[Groups(['order:list'])]
        #[OA\Property(type:'array', description: "Liste des produits commandés", items: new OA\Items(ref:new Model(type:OrderItemDto::class, groups: ['order:list'])))]
        public array $items,
    ) {
    }
}
