<?php

namespace App\Controller;

use App\Entity\ExchangeRate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Predis\Client;

class ExchangeRateController extends AbstractController
{
    /**
     * @Route("/api/exchange-rates", name="exchange_rates", methods={"GET"})
     */
    public function getExchangeRates(Request $request): JsonResponse
    {
        $baseCurrency = $request->query->get('base_currency');
        $targetCurrencies = $request->query->get('target_currencies');
    
        // Convert targetCurrencies to an array if it is a string
        if (is_string($targetCurrencies)) {
            $targetCurrencies = explode(',', $targetCurrencies);
        }
    
        // Check Redis for the requested rates
        $redisKey = $this->generateRedisKey($baseCurrency, $targetCurrencies);
        $redis = new \Predis\Client();
        if ($redis->exists($redisKey)) {
            //$redis->flushall();
            $exchangeRates = $redis->hgetall($redisKey);
        } else {
            $redis->flushall();
            // Fetch rates from MySQL Database
            $exchangeRates = $this->fetchRatesFromDatabase($baseCurrency, $targetCurrencies);
    
            if(!empty($exchangeRates)){
                // Store rates in Redis
                $redis->hmset($redisKey, $exchangeRates);
            }else{
                return $this->json('It seems that there is a lack of information regarding the exchange rate at the moment. Please execute the command php bin/console app:currency:rates [base_currency] [target_currency_1] [target_currency_2] ... [target_currency_n] by console');
            }
        }
    
        return $this->json($exchangeRates);    
    }

    private function generateRedisKey(string $baseCurrency, array $targetCurrencies): string
    {
        return 'exchange_rates:' . $baseCurrency . ':' . implode(':', $targetCurrencies);
    }

    private function fetchRatesFromDatabase(string $baseCurrency, array $targetCurrencies): array
    {
        $entityManager = $this->getDoctrine()->getManager();
        $exchangeRateRepository = $entityManager->getRepository(ExchangeRate::class);
    
        $exchangeRates = [];
    
        foreach ($targetCurrencies as $targetCurrency) {
            $exchangeRate = $exchangeRateRepository->findOneBy([
                'baseCurrency' => $baseCurrency,
                'targetCurrency' => $targetCurrency,
            ]);
    
            if ($exchangeRate) {
                $exchangeRates[$targetCurrency] = $exchangeRate->getRate();
            }
        }
    
        return $exchangeRates;
    }    
}
