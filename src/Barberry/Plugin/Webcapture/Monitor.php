<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin\InterfaceMonitor;

class Monitor implements InterfaceMonitor
{
    const PHANTOMJS_REQUIRED_VERSION = '1.5.0';

    private $tempDir;

    public function __construct($tempDir = '')
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return array of error messages
     */
    public function reportUnmetDependencies()
    {
        $errors = array();

        if (!$this->phantomjsInstalled()) {
            $errors[] = 'Please install phantomjs (expected version >= 1.5.0)';
        }

        if (version_compare($this->phantomjsVersion(), self::PHANTOMJS_REQUIRED_VERSION, '<')) {
            $errors[] = 'Insufficient phantomjs version: expected >= 1.5.0';
        }

        return $errors;
    }

    /**
     * @return array of error messages
     */
    public function reportMalfunction()
    {
        $report = $this->reportDirectoryIsWritable($this->tempDir);
        return (!is_null($report)) ? array($report) : array();
    }

    /**
     * @return bool whether phantomjs is installed
     */
    protected function phantomjsInstalled()
    {
        $testCommand = exec('phantomjs');
        return (strstr($testCommand, 'command not found')) ? true : false;
    }

    /**
     * @return bool whether proper phantomjs version is installed
     */
    protected function phantomjsVersion()
    {
        return exec('phantomjs --version');
    }

    /**
     * @param $dir directory to be tested
     * @return null|string error message
     */
    private function reportDirectoryIsWritable($dir)
    {
        return (is_writable($dir)) ? null : 'ERROR: Temporary directory is not writable (Webcapture plugin)';
    }
}
