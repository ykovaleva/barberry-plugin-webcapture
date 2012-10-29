<?php

use Barberry\Plugin\Webcapture\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testNoConversionParametersByDefault()
    {
        $this->assertNull(self::command()->zoom());
        $this->assertNull(self::command()->paperFormat());
    }

    public function testCreatesImagemagickCommand()
    {
        $command = self::command('100x200_0.5zoom_a9-format');
        $this->assertEquals('100x200', $command->commandForImagemagick());
    }

    public function testExtractsZoomParameter()
    {
        $command = self::command('100x500_0.2zoom_');
        $this->assertEquals(0.2, $command->zoom());
    }

    public function testExtractsPaperSizeParameter()
    {
        $command = self::command('100x200_10zoom_a5-format');
        $this->assertEquals('a5', $command->paperFormat());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(self::command('123x123d_25d%zoom_a5-format')->conforms('123x123_0.25zoom_a5-format'));
    }

    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
