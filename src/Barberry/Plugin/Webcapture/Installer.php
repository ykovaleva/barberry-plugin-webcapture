<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin;
use Barberry\Direction;
use Barberry\Monitor\ComposerInterface as MonitorComposerInterface;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    public function install(Direction\ComposerInterface $directionComposer, MonitorComposerInterface $monitorComposer,
                            $pluginParams = array())
    {
        foreach ($this->directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                $pair[1],
                'new Plugin\\Webcapture\\Converter',
                'new Plugin\\Webcapture\\Command'
            );
        }

        $monitorComposer->writeClassDeclaration('Webcapture');
    }

    public static function directions()
    {
        return array(
            array(ContentType::url(), \Barberry\ContentType::png()),
            array(ContentType::url(), \Barberry\ContentType::jpeg()),
            array(ContentType::url(), \Barberry\ContentType::gif()),
            array(ContentType::url(), \Barberry\ContentType::pdf())
        );
    }
}
