<?php
namespace Barberry\Plugin\Webcapture;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testNoParamsByDefault()
    {
        $this->assertNull(self::command()->viewportSize());
        $this->assertNull(self::command()->paperFormat());
    }

    public function testCreatesImagemagickCommand()
    {
        $command = self::command('10x10_z05_a9~100x200');
        $this->assertEquals('100x200', $command->commandForImagemagick());
    }

    public function testExtractsZoomParameter()
    {
        $command = self::command('10x10_z02~100x200');
        $this->assertEquals(0.2, $command->zoom());
    }

    public function testExtractsViewportSizeParameter()
    {
        $command = self::command('10x10_z12_a5~500x500');
        $this->assertEquals('10x10', $command->viewportSize());
    }

    public function testSetsViewportSizeToNullIfIncompleteViewportSizeParameter()
    {
        $command = self::command('100x_z1_a3~10x10');
        $this->assertNull($command->viewportSize());
    }

    public function testExtractsPaperSizeParameter()
    {
        $command = self::command('z10_a5~100x200');
        $this->assertEquals('a5', $command->paperFormat());
    }

    public function testAmbiguityCommand()
    {
        $this->assertFalse(self::command('105x105_z025d%_a5~100x200')->conforms('105x105_z025_a5~100x200'));
    }

    /**
     * @dataProvider commandsWithZoomFactor
     */
    public function testDifferentZoomFactors($command, $expectedZoom)
    {
        $this->assertEquals($expectedZoom, self::command($command)->zoom());
    }

    public static function commandsWithZoomFactor()
    {
        return array(
            array('z1', 1),
            array('z1~100x100', 1),
            array('z01', 0.1),
            array('z025', 0.25),
            array('z25', null),
            array('z12', null),
            array('100x_z1_a3~10x10', 1),
        );
    }

    /**
     * @param null $commandString
     * @return Command
     */
    private static function command($commandString = null)
    {
        $command = new Command();
        return $command->configure($commandString);
    }
}
