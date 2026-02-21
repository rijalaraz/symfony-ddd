<?php

namespace App\Catalogue\Infrastructure\Persistence\Doctrine\Repository;

use App\Catalogue\Domain\Entity\Product;
use App\Catalogue\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsAlias(id: ProductRepositoryInterface::class)]
class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
    )
    {
    }

    public function get(string $productId): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $productId]);
    }

    public function getCollection(bool $onlyAvailable, ?int $maxPrice): array
    {
        $qb = $this->entityManager->createQueryBuilder()->select("p")->from(Product::class, "p");

        if($this->requestStack->getCurrentRequest()->query->has('only_available')) {
            if ($onlyAvailable) {
                $qb->andWhere("p.onHand - p.onHold > 0");
            } else {
                $qb->andWhere("p.onHand - p.onHold <= 0");
            }
        }

        if ($maxPrice) {
            $qb->andWhere("p.price <= :maxPrice")->setParameter("maxPrice", $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }

    public function add(Product $product): void
    {
        $this->entityManager->persist($product);
    }

}
