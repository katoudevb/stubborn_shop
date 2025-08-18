<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private float $price;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $stocks;

    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product')]
    private Collection $cartItems;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }
    public function isFeatured(): bool
    {
        return $this->featured;
    }
    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;
        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }
    public function getImageUrl(): string
    {
        return $this->image ? '/uploads/' . $this->image : '/images/default-product.png';
    }

    public function getStocks(): Collection
    {
        return $this->stocks;
    }
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProduct($this);
        }
        return $this;
    }
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            if ($stock->getProduct() === $this) $stock->setProduct(null);
        }
        return $this;
    }

    public function getTotalStock(): int
    {
        return array_sum($this->stocks->map(fn($stock) => $stock->getQuantity())->toArray());
    }
    public function isAvailable(): bool
    {
        return $this->getTotalStock() > 0;
    }

    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }
    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setProduct($this);
        }
        return $this;
    }
    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            if ($cartItem->getProduct() === $this) $cartItem->setProduct(null);
        }
        return $this;
    }

    // ------------------------------
    // Gestion des tailles
    // ------------------------------
    public function getAvailableSizes(): array
    {
        return $this->stocks->map(fn($stock) => $stock->getSize())->toArray();
    }

    public function getStockBySize(string $size): int
    {
        foreach ($this->stocks as $stock) {
            if ($stock->getSize() === $size) return $stock->getQuantity();
        }
        return 0;
    }
}
