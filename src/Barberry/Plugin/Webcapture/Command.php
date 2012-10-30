<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{
    const DEFAULT_ZOOM = 1;

    private $zoom;
    private $paperFormat;
    private $commandForImagemagick;

    private static $allowedPaperFormats = array(
        'a3',
        'a4',
        'a5',
        'legal',
        'latter',
        'tabloid'
    );

    /**
     * @param string $commandString
     * @return InterfaceCommand
     */
    public function configure($commandString)
    {
        $commands = explode('~', $commandString);

        $params = explode('_', $commands[0]);
        foreach ($params as $param) {
            if (preg_match('/^(0.[\d]+)$/', $param, $match)) {
                $this->zoom = (float)$match[1];
            }
            $formats = implode('|', self::$allowedPaperFormats);
            if (preg_match('/^(' . $formats . ')$/', $param, $match)) {
                $this->paperFormat = $match[1];
            }
        }

        $this->commandForImagemagick = isset($commands[1]) ? $commands[1] : null;

        return $this;
    }

    /**
     * Command should have only one string representation
     *
     * @param string $commandString
     * @return boolean
     */
    public function conforms($commandString)
    {
        return strval($this) === $commandString;
    }

    public function commandForImagemagick()
    {
        return $this->commandForImagemagick;
    }

    public function zoom()
    {
        return is_null($this->zoom) ? self::DEFAULT_ZOOM : $this->zoom;
    }

    public function paperFormat()
    {
        return in_array($this->paperFormat, self::$allowedPaperFormats) ? $this->paperFormat : '';
    }

    public function __toString()
    {
        $string = is_null($this->zoom()) ? '' : '_' . $this->zoom();
        $string .= is_null($this->paperFormat()) ? '' : '_' . $this->paperFormat();
        $string .= is_null($this->commandForImagemagick()) ? '' : '~' . $this->commandForImagemagick();

        return $string;
    }
}
