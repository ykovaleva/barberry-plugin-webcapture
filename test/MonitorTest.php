<?php

namespace Barberry\Plugin\Webcapture;

class MonitorTest extends \PHPUnit_Framework_TestCase
{
    private $testDirWritable;
    private $testDirNotWritable;

    protected function setUp()
    {
        $path = realpath(__DIR__) . '/../tmp';

        $this->testDirWritable = $path . '/testdir-writable/';
        $this->testDirNotWritable = $path . '/testdir-notwritable/';

        @mkdir($this->testDirWritable, 0777, true);
        @mkdir($this->testDirNotWritable, 0444, true);
    }

    protected function tearDown()
    {
        exec('rm -rf ' . $this->testDirWritable);
        exec('rm -rf ' . $this->testDirNotWritable);
    }

    public function testReportsIfPhantomJsIsNotInstalled()
    {
        $monitor = $this->getMock('Barberry\\Plugin\\Webcapture\\Monitor', array('phantomjsInstalled'));
        $monitor->expects($this->any())->method('phantomjsInstalled')->will($this->returnValue(false));

        $this->assertContains(
            'Please install phantomjs (expected version >= 1.5.0)',
            $monitor->reportUnmetDependencies()
        );
    }

    public function testReportsInsufficientVersionOfPhantomJs()
    {
        $monitor = $this->getMock('Barberry\\Plugin\\Webcapture\\Monitor', array('phantomjsVersion'));
        $monitor->expects($this->any())->method('phantomjsVersion')->will($this->returnValue('1.4.0'));

        $this->assertContains(
            'Insufficient phantomjs version: expected >= 1.5.0',
            $monitor->reportUnmetDependencies()
        );
    }

    public function testReportsNoErrorsIfDirectoryIsWritable()
    {
        $monitor = self::monitor($this->testDirWritable);
        $this->assertEquals(array(), $monitor->reportMalfunction());
    }

    public function testReportsErrorsIfDirectoryIsNotWritable()
    {
        $monitor = self::monitor($this->testDirNotWritable);
        $this->assertEquals(
            array(
                'ERROR: Temporary directory is not writable (Webcapture plugin)'
            ),
            $monitor->reportMalfunction()
        );
    }

    private static function monitor($tempDir)
    {
        $monitor = new Monitor;
        return $monitor->configure($tempDir);
    }
}
