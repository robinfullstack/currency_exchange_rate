<?php

namespace App\Repository;

interface ExchangeRateRepositoryInterface
{
    public function saveRates(array $rates): void;
    public function hasSavedRates(): bool;
    public function getRates(): array;
}