<?php
// src/Entity/Clothing.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Clothing
{
    /** 
     * @ORM\Id 
     * @ORM\GeneratedValue 
     * @ORM\Column(type="integer") 
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255) 
     */
    private $name;

    /** 
     * @ORM\Column(type="float") 
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $highlighted = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    // ---------------- Getters & Setters ----------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    public function setHighlighted(bool $highlighted): self
    {
        $this->highlighted = $highlighted;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
