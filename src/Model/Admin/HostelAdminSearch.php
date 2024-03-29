<?php

namespace App\Model\Admin;

use App\Entity\Category;

class HostelAdminSearch
{
    private ?Category $category = null;

    private ?string $email = null;

    private ?string $name = null;

    private ?bool $enabled = null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category$category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}