<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin\InterfaceCommand;

class Command implements InterfaceCommand
{
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
        $params = explode('_', $commandString);

        foreach ($params as $param) {
            if (preg_match('/^([\d]*)x([\d]*)$/', $param, $match)) {
                $this->commandForImagemagick = $match[0];
            }
            if (preg_match('/^(0.[\d]+)zoom*$/', $param, $match)) {
                $this->zoom = (float)$match[1];
            }
            $formats = implode('|', self::$allowedPaperFormats);
            if (preg_match('/^(' . $formats . ')-format*$/', $param, $match)) {
                $this->paperFormat = $match[1];
            }
        }
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
        return $this->zoom;
    }

    public function paperFormat()
    {
        return in_array($this->paperFormat, self::$allowedPaperFormats) ? $this->paperFormat : null;
    }

    public function __toString()
    {
        $string = is_null($this->commandForImagemagick()) ? '' : $this->commandForImagemagick();
        $string .= is_null($this->zoom()) ? '' : '_' . $this->zoom() . 'zoom';
        $string .= is_null($this->paperFormat()) ? '' : '_' . $this->paperFormat() . '-format';

        return $string;
    }
}
