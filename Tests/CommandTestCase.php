<?php

namespace MakG\UserBundle\Tests;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends TestCase
{
    /**
     * Returns Symfony's command tester.
     */
    public function createCommandTester(string $name, Command $command): CommandTester
    {
        $application = new Application();
        $application->add($command);

        return new CommandTester($application->find($name));
    }
}