<?php

namespace App\Command;

use App\Entity\ExchangeRate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Predis\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class FetchExchangeRatesCommand extends Command
{
    protected static $defaultName = 'app:currency:rates';

    //private $httpClient;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ClientInterface $redisClient, string $apiKey)
    {
        //$this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->redisClient = $redisClient;
		$this->apiKey = $apiKey;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetches exchange rates from the CurrencyFreaks API and saves them into the database.')
            ->setHelp('This command allows you to fetch exchange rates and store them in the database.')
            ->addArgument('base_currency', InputArgument::REQUIRED, 'Base currency')
            ->addArgument('target_currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Target currencies (space-separated)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseCurrency = $input->getArgument('base_currency');
        $targetCurrencies = $input->getArgument('target_currencies');

        $url = 'https://api.currencyfreaks.com/latest?base=' . $baseCurrency . '&apikey=' . $this->apiKey;
        $httpClient = new Client();
        $response = $httpClient->request('GET', $url);
        $data = json_decode($response->getBody(), true);

        $exchangeRates = [];

        foreach ($data['rates'] as $currency => $rate) {
            if (in_array($currency, $targetCurrencies)) {
                $exchangeRate = new ExchangeRate();
                $exchangeRate->setBaseCurrency($baseCurrency);
                $exchangeRate->setTargetCurrency($currency);
                $exchangeRate->setRate($rate);
                $exchangeRate->setCreatedAt(new DateTime());

                $this->entityManager->persist($exchangeRate);

                $exchangeRates[$currency] = $rate;
            }
        }

        $this->entityManager->flush();

        $this->redisClient->set('exchange_rates', json_encode($exchangeRates));
        $this->redisClient->expire('exchange_rates', 3600); // Set an expiry time (e.g., 1 hour)

        $output->writeln('Exchange rates fetched, saved in MySQL, and stored in Redis.');

        return Command::SUCCESS;
    }
}