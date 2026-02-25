<?php

namespace App\Order\Mapper;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderItem;
use App\Order\Dto\OrderDto;
use App\Order\Dto\OrderItemDto;

final class OrderMapper
{
    public function map(Order $order)
    {
        return new OrderDto(
            $order->getId(),
            $order->getAmountToPay(),
            $order->getStatus(),
            [],
            array_map(
                fn(OrderItem $orderItem) => new OrderItemDto(
                    $orderItem->getId(),
                    $orderItem->getName(),
                    $orderItem->getQuantity(),
                    $orderItem->getPrice(),
                ),
                iterator_to_array($order->getItems())
            ),
        );
    }

    public function toDto(array $orders)
    {
        return array_map(
            fn(Order $order) => $this->map($order),
            $orders
        );
    }
}
