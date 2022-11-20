<?php

namespace App\Model;

class HostelSearch
{
    private ?string $name = '';

    private ?array $equipments = [];

    private ?array $price = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEquipments(): ?array
    {
        return $this->equipments;
    }

    public function setEquipments(?array $equipments): self
    {
        $this->equipments = $equipments;

        return $this;
    }

    public function getPrice(): ?array
    {
        return $this->price;
    }

    public function setPrice(?array $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'equipments' => $this->getEquipments(),
            'price' => $this->getPrice(),
        ];
    }


    /*private ?string $category = null;

    private ?string $name = null;

    private ?int $star = null;

    private ?bool $offer = null;

    private ?string $rating = null;

    private ?string $equipment = null;

    private ?string $roomEquipment = null;

    private ?int $priceMin = null;

    private ?int $priceMax = null;

    private ?string $order = null;

    private ?string $priceOrder = null;

    private ?string $starOrder = null;

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStar(): ?int
    {
        return $this->star;
    }

    public function setStar(?int $star): self
    {
        $this->star = $star;

        return $this;
    }

    public function getOffer(): ?bool
    {
        return $this->offer;
    }

    public function setOffer(?bool $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(?string $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getRoomEquipment(): ?string
    {
        return $this->roomEquipment;
    }

    public function setRoomEquipment(?string $roomEquipment): self
    {
        $this->roomEquipment = $roomEquipment;

        return $this;
    }

    public function getPriceMin(): ?int
    {
        return $this->priceMin;
    }

    public function setPriceMin(?int $priceMin): self
    {
        $this->priceMin = $priceMin;

        return $this;
    }

    public function getPriceMax(): ?int
    {
        return $this->priceMax;
    }

    public function setPriceMax(?int $priceMax): self
    {
        $this->priceMax = $priceMax;

        return $this;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function setOrder(?string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getStarOrder(): ?string
    {
        return $this->starOrder;
    }

    public function setStarOrder(?string $starOrder): self
    {
        $this->starOrder = $starOrder;

        return $this;
    }

    public function getPriceOrder(): ?string
    {
        return $this->priceOrder;
    }

    public function setPriceOrder(?string $priceOrder): self
    {
        $this->priceOrder = $priceOrder;

        return $this;
    }*/
}
