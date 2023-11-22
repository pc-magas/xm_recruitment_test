<?php

namespace App\Entity;

use App\Repository\CompanySymbolRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanySymbolRepository::class)]
class CompanySymbol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $symbol = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = strtoupper(trim($symbol));

        return $this;
    }

    public function getName(): ?string
    {
        return trim($this->name);
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
