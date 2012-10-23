<?php
namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{

    /**
     * @param string $commandString
     * @return InterfaceCommand
     */
    public function configure($commandString)
    {
        // TODO: Implement configure() method.
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString)
    {
        return true;
    }
}
