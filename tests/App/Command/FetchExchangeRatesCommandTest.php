<?php

namespace App\Tests\Command;

use App\Command\FetchExchangeRatesCommand;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Predis\ClientInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class FetchExchangeRatesCommandTest extends TestCase
{
    public function testExecute()
    {
        // Mocking dependencies
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $redisClient = $this->createMock(ClientInterface::class);

        // Creating the command and application
        $command = new FetchExchangeRatesCommand($entityManager, $redisClient, 'b2a058141a8b4d8697be61422f6a4578', 'https://api.currencyfreaks.com/latest');
        $application = new Application();
        $application->add($command);

        // Creating the command tester
        $commandTester = new CommandTester($command);

        // Executing the command
        $commandTester->execute([
            'command' => $command->getName(),
            'base_currency' => 'USD',
            'target_currencies' => ['EUR', 'GBP', 'JPY'],
        ]);

        // Assertions
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Exchange rates fetched, saved in MySQL, and stored in Redis.', $output);
    }
}
