<?php

namespace App\Catalogue\Domain\Entity;

use App\Catalogue\Domain\Exception\EmptyProductNameException;
use App\Catalogue\Domain\Exception\InsufficientStockException;
use App\Catalogue\Domain\Exception\NonPositiveQuantityException;
use App\Catalogue\Domain\Exception\OverCommitReservationException;
use App\SharedKernel\Domain\ValueObject\Money;
use Symfony\Component\Serializer\Annotation\Groups;

class Product
{

    #[Groups(['product:list', 'product:detail'])]
    private string $id;
    #[Groups(['product:create','product:list'])]
    private string $name;
    #[Groups(['product:create'])]
    private int $price;
    private bool $on_hand = false;
    private int $onHand = 0; // à portée de main
    private int $onHold = 0; // en attente (réservé)

    public static function create(string $id, string $name, Money $price, int $onHand): self
    {
        // if ($name === '') {
        //     throw new EmptyProductNameException('Name required.');
        // }
        $self = new self();
        $self->id = $id;
        $self->name = $name;
        $self->price = $price->toMinor();
        $self->onHand = $onHand;
        return $self;
    }

    public function hold(int $qty): void
    {
        if ($qty <= 0) {
            throw new NonPositiveQuantityException('Quantity must be > 0.');
        }
        if ($qty > $this->getAvailable()) {
            throw new InsufficientStockException('Not enough stock available to hold.');
        }
        $this->onHold += $qty;
    }

    public function commitReservation(int $qty): void
    {
        if ($qty <= 0) {
            throw new NonPositiveQuantityException('Quantity must be > 0.');
        }
        if ($qty > $this->onHold) {
            throw new OverCommitReservationException('Cannot commit more than reserved.');
        }

        $this->onHold -= $qty;
        $this->onHand -= $qty;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return Money::fromMinor($this->price);
    }

    #[Groups(['product:list'])]
    public function getPriceMinor(): int
    {
        return $this->price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOnHand(): int
    {
        return $this->onHand;
    }

    #[Groups(['product:create'])]
    public function getOn_hand(): int
    {
        return $this->on_hand;
    }

    public function getOnHold(): int
    {
        return $this->onHold;
    }


    public function getAvailable(): int
    {
        return $this->onHand - $this->onHold;
    }

    #[Groups(['product:list'])]
    public function getQuantity(): int
    {
        return $this->onHand - $this->onHold;
    }
}
