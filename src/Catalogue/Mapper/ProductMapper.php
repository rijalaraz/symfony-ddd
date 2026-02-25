<?php

namespace App\Catalogue\Mapper;

use App\Catalogue\Domain\Entity\Product;
use App\Catalogue\Dto\ProductDto;

final class ProductMapper
{
    public function map(Product $product)
    {
        return new ProductDto(
            $product->getId(),
            $product->getId(),
            $product->getName(),
            $product->getAvailable(),
            $product->getPrice()->toMinor(),
            $product->getPrice()->toMajorString(),
            $product->getAvailable(),
            $product->getOnHand(),
        );
    }

    public function toDto(array $products)
    {
        return array_map(
            fn(Product $product) => $this->map($product),
            $products
        );
    }
}
