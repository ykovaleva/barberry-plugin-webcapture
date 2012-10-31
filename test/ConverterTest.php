<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\ContentType;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $monitor = new Monitor('/tmp');
        self::assertEquals(array(), $monitor->reportUnmetDependencies());
    }

    public function testThrowsExceptionWhenPhantomJsFailsToCreateDestinationFile()
    {
        $this->setExpectedException('Barberry\\Plugin\\Webcapture\\PhantomJsException');

        $converter = $this->getMock(
            'Barberry\\Plugin\\Webcapture\\Converter',
            array('runPhantomJs'),
            array(ContentType::jpeg(), null)
        );
        $converter->convert(self::bin(), self::command());
    }

    public function testThrowsExceptionWhenPhantomJsOutputsMessages()
    {
        $this->setExpectedException('Barberry\\Plugin\\Webcapture\\PhantomJsException');

        $converter = self::converter(ContentType::png());
        $converter->convert(self::bin(false), self::command());
    }

    public function testConvertsUrlToPng()
    {
        $converter = self::converter(ContentType::png());
        $result = $converter->convert(self::bin(), self::command());
        $this->assertEquals(ContentType::png(), ContentType::byString($result));
    }

    public function testConvertsUrlToJpeg()
    {
        $converter = self::converter(ContentType::jpeg());
        $result = $converter->convert(self::bin(), self::command());
        $this->assertEquals(ContentType::jpeg(), ContentType::byString($result));
    }

    public function testConvertsUrlToGif()
    {
        $converter = self::converter(ContentType::gif());
        $result = $converter->convert(self::bin(), self::command());
        $this->assertEquals(ContentType::gif(), ContentType::byString($result));
    }

    public function testConvertsUrlToPdf()
    {
        $converter = self::converter(ContentType::pdf());
        $result = $converter->convert(self::bin(), self::command());
        $this->assertEquals(ContentType::pdf(), ContentType::byString($result));
    }

    public function testUtilizesExistingDirectionToExecuteImagemagickCommand()
    {
        include_once __DIR__ . '/FakePngToJpgDirection.php';
        self::converter(ContentType::jpeg())->convert(self::bin(), self::commandWithImagemagickPart());
        $this->assertTrue(\Barberry\Direction\PngToJpgDirection::$hasBeenUtilized);
    }

    private static function converter($targetDirection)
    {
        return new Converter($targetDirection, __DIR__ . '/../tmp/');
    }

    private static function command()
    {
        $command = new Command();
        return $command->configure('z0.9_tabloid');
    }

    private static function commandWithImagemagickPart()
    {
        $command = new Command();
        return $command->configure('1055x10_z0.9_tabloid~100x200');
    }

    private static function bin($realUrl = true)
    {
        $dir = __DIR__ . '/data';
        $fileName = $realUrl ? 'hiRcrQ' : 'iSyDze';
        return file_get_contents($dir . '/' .  $fileName);
    }
}
