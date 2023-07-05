<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="exchange_rate")
 */
class ExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $baseCurrency;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $targetCurrency;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    // Getters and Setters for the properties

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseCurrency(): ?string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): self
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getTargetCurrency(): ?string
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(string $targetCurrency): self
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}