<?php

use Barberry\Plugin\Webcapture\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testAllTheCommandsAreAccepted()
    {
        $command = new Command();
        $this->assertTrue($command->conforms('test'));
    }
}
