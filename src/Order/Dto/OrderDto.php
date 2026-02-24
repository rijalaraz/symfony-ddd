<?php

namespace App\Order\Dto;

use App\Catalogue\Dto\ProductDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

readonly class OrderDto
{
    public function __construct(
        #[Groups(['order:detail'])]
        #[OA\Property(description: "ID de la commande")]
        public string $id,

        #[Groups(["order:create"])]
        public int $amount_to_pay,

        public string $status,

        #[Groups(['order:create'])]
        #[OA\Property(type:'array', description: "Liste des produits commandés", items: new OA\Items(ref:new Model(type:ProductDto::class, groups: ['order:create'])))]
        public array $products,
    ) {
    }
}
