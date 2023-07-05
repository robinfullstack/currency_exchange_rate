<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRateControllerTest extends WebTestCase
{
    /**
     * @group legacy
     */
    public function testGetExchangeRates(): void
    {
        $client = static::createClient([], [
            'base_uri' => 'http://localhost:8000',
        ]);

        // Define the base currency and target currencies for testing
        $baseCurrency = 'EUR';
        $targetCurrencies = ['USD', 'GBP'];

        // Simulate a request to the endpoint
        $client->request(
            'GET',
            '/api/exchange-rates',
            ['base_currency' => $baseCurrency, 'target_currencies' => implode(',', $targetCurrencies)]
        );

        // Check the response status code
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Add assertions to test the response content or other desired behavior
        // Check if the rates are returned correctly

        $responseContent = $client->getResponse()->getContent();
        $responseJson = json_decode($responseContent, true);

        // Assert that the response contains the expected target currencies
        foreach ($targetCurrencies as $targetCurrency) {
            $this->assertArrayHasKey($targetCurrency, $responseJson);
        }
    }
}