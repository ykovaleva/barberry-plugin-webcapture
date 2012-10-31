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

    public function __construct(ContentType $targetContentType, $tempDir)
    {
        $this->tempDir = $tempDir;
        $this->jsScriptFile = realpath(__DIR__ . '/../../../../scripts/run-phantomjs.js');
        $this->targetContentType = $targetContentType;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null)
    {
        if (!is_null($command->commandForImagemagick())) {
            // get png for imagemagick
            $bin = $this->runPhantomJs(
                 'png', $bin, $command->viewportSize(), $command->zoom(), $command->paperFormat()
            );
            $bin = $this->resizeWithImagemagick($bin, $command->commandForImagemagick());
        } else {
            $extension = $this->targetContentType->standartExtention();
            $bin = $this->runPhantomJs(
                $extension, $bin, $command->viewportSize(), $command->zoom(), $command->paperFormat()
            );
        }

        if (is_null($bin)) {
            throw new PhantomJsException('can\'t create destination file.');
        }

        return $bin;
    }

    protected function runPhantomJs($extension, $bin, $viewportSize, $zoom, $paperFormat)
    {
        $tempFile = $this->createTempFile($extension);

        // undefined failure with phantom js, so try to create file with phantomjs 5 times
        $i = 0;
        do {
            exec(
                'phantomjs ' . escapeshellarg($this->jsScriptFile) . ' '
                    . escapeshellarg($bin) . ' '
                    . escapeshellarg($tempFile) . ' '
                    . escapeshellarg($viewportSize) . ' '
                    . escapeshellarg($zoom). ' '
                    . escapeshellarg($paperFormat)
            );
            $i++;
        } while ($i < 5 && !filesize($tempFile));

        $result = filesize($tempFile) ? file_get_contents($tempFile) : null;
        unlink($tempFile);

        return $result;
    }

    /**
     * @param $bin file to be processed
     * @param $commandString
     * @return string resized image
     */
    protected function resizeWithImagemagick($bin, $commandString)
    {
        $from = ucfirst(ContentType::byString($bin)->standartExtention());
        $to = ucfirst($this->targetContentType->standartExtention());
        $directionClass = '\\Barberry\\Direction\\' . $from . 'To' . $to . 'Direction';

        if (class_exists($directionClass)) {
            $direction = new $directionClass($commandString);
            return $direction->convert($bin);
        } else {
            throw new \Exception('Can\'t convert to requested direction.', 404);
        }
    }

    /**
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
