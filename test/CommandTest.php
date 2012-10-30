<?php

use Barberry\Plugin\Webcapture\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testReplacesEmptyParametersWithDefaultParams()
    {
        $this->assertEquals(Command::DEFAULT_ZOOM, self::command()->zoom());
        $this->assertEquals('', self::command()->paperFormat());
    }

    public function testCreatesImagemagickCommand()
    {
        $command = self::command('0.5_a9~100x200');
        $this->assertEquals('100x200', $command->commandForImagemagick());
    }

    public function testExtractsZoomParameter()
    {
        $command = self::command('0.2~100x200');
        $this->assertEquals(0.2, $command->zoom());
    }

    public function testExtractsPaperSizeParameter()
    {
        $command = self::command('10_a5~100x200');
        $this->assertEquals('a5', $command->paperFormat());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(self::command('25d%_a5~100x200')->conforms('0.25_a5~100x200'));
    }

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
