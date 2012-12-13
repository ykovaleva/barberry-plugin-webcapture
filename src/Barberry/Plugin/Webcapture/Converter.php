<?php

namespace Barberry\Plugin\Webcapture;

use Barberry\Plugin;
use Barberry\Direction;
use Barberry\ContentType;

class Converter implements Plugin\InterfaceConverter
{
    private $tempDir;
    private $jsScriptFile;
    private $targetContentType;

    public function configure(ContentType $targetContentType, $tempDir)
    {
        $this->tempDir = $tempDir;
        $this->jsScriptFile = realpath(__DIR__ . '/../../../../scripts/run-phantomjs.js');
        $this->targetContentType = $targetContentType;
        return $this;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        $bin = $this->runPhantomJs(
            is_null($command->commandForImagemagick()) ? $this->targetContentType->standardExtension() : 'png',
            $bin,
            $command->viewportSize(),
            $command->zoom(),
            $command->paperFormat()
        );

        if (is_null($bin)) {
            throw new PhantomJsException('can\'t create destination file.');
        }

        if (!is_null($command->commandForImagemagick())) {
            $bin = $this->resizeWithImagemagick($bin, $command->commandForImagemagick());
        }

        return $bin;
    }

    protected function runPhantomJs($extension, $bin, $viewportSize, $zoom, $paperFormat)
    {
        // undefined failure with phantom js, so try to create file with phantomjs 5 times
        $i = 0;
        do {
            $tempFile = $this->createTempFile($extension);

            session_id('singleton');
            session_start();
            $phantomJs = exec(
                'phantomjs ' . escapeshellarg($this->jsScriptFile) . ' '
                    . escapeshellarg($bin) . ' '
                    . escapeshellarg($tempFile) . ' '
                    . escapeshellarg($viewportSize) . ' '
                    . escapeshellarg($zoom). ' '
                    . escapeshellarg($paperFormat)
            );
            session_write_close();

            $result = filesize($tempFile) ? file_get_contents($tempFile) : null;
            unlink($tempFile);

            if (strlen($phantomJs)) {
                throw new PhantomJsException('phantom js failed to execute');
            }

            if ($result) {
                return $result;
            }

            $i++;
        } while ($i < 5);

        return null;
    }

    /**
     * @param string $bin file to be processed
     * @param string $commandString
     * @throws \Exception
     * @return string resized image
     */
    protected function resizeWithImagemagick($bin, $commandString)
    {
        $from = ucfirst(ContentType::byString($bin)->standardExtension());
        $to = ucfirst($this->targetContentType->standardExtension());
        $directionClass = '\\Barberry\\Direction\\' . $from . 'To' . $to . 'Direction';
        $direction = new $directionClass($commandString);
        return $direction->convert($bin);
    }

    /**
     * @param string $extension
     * @return string temporary file name
     */
    private function createTempFile($extension)
    {
        $destinationTemp = tempnam($this->tempDir, 'webcapture_');
        chmod($destinationTemp, 0664);
        $destination = $destinationTemp . '.' . $extension;
        rename($destinationTemp, $destination);
        return $destination;
    }
}
