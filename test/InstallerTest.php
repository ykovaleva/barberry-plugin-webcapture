<?php

namespace Barberry\Plugin\Webcapture;

use Mockery as m;
use Barberry\ContentType;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatesCreationOfDirections()
    {
        $composer = m::mock('Barberry\\Direction\\ComposerInterface');

        foreach (Installer::directions() as $direction) {
            $composer->shouldReceive('writeClassDeclaration')->with(
                equalTo($direction[0]), equalTo(eval('return ' . $direction[1] . ';')), anything(), anything()
            )->once();
        }

        $installer = new Installer(__DIR__ . '/../tmp/');
        $installer->install($composer, $this->getMock('Barberry\\Monitor\\ComposerInterface'));
    }

    public function testDelegatesCreationOfWebcaptureMonitor()
    {
        $composer = m::mock('Barberry\\Monitor\\ComposerInterface');
        $composer->shouldReceive('writeClassDeclaration')->with(
            'Webcapture',
            anything()
        )->once();

        $installer = new Installer(__DIR__ . '/../tmp/');
        $installer->install($this->getMock('Barberry\\Direction\\ComposerInterface'), $composer);
    }
}
