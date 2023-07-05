<?php

namespace App\Command;

use App\Entity\ExchangeRate;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchExchangeRatesCommand extends Command
{
    protected static $defaultName = 'app:currency:rates';

    private $httpClient;
    private $entityManager;

    public function __construct(ClientInterface $httpClient, EntityManagerInterface $entityManager, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
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
        $response = $this->httpClient->get($url);
        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data['rates'] as $currency => $rate) {
            if (in_array($currency, $targetCurrencies)) {
                $exchangeRate = new ExchangeRate();
                $exchangeRate->setBaseCurrency($baseCurrency);
                $exchangeRate->setTargetCurrency($currency);
                $exchangeRate->setRate($rate);
                $exchangeRate->setCreatedAt(new DateTime());

                $this->entityManager->persist($exchangeRate);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Exchange rates fetched and saved successfully.');

        return Command::SUCCESS;
    }
}