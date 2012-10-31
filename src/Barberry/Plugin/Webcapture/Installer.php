<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin;
use Barberry\Direction;
use Barberry\Monitor\ComposerInterface as MonitorComposerInterface;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    private $tempDir;

    public function __construct($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    public function install(Direction\ComposerInterface $directionComposer, MonitorComposerInterface $monitorComposer,
                            $pluginParams = array())
    {
        foreach ($this->directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                eval('return ' . $pair[1] . ';'),
                <<<PHP
new Plugin\\Webcapture\\Converter ($pair[1], '{$this->tempDir}');
PHP
                ,
                'new Plugin\\Webcapture\\Command'
            );
        }

        $monitorComposer->writeClassDeclaration('Webcapture', "parent::_construct('{$this->tempDir}')");
    }

    public static function directions()
    {
        return array(
            array(ContentType::url(), '\Barberry\ContentType::png()'),
            array(ContentType::url(), '\Barberry\ContentType::jpeg()'),
            array(ContentType::url(), '\Barberry\ContentType::gif()'),
            array(ContentType::url(), '\Barberry\ContentType::pdf()')
        );
    }
}
