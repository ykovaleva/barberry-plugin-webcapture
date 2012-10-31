<?php

class PngToJpgDirection extends Barberry\Direction\DirectionAbstract
{

    public static $hasBeenUtilized = false;

    protected function init($commandString = null)
    {
        return true;
    }

    public function convert()
    {
        self::$hasBeenUtilized = true;
        return 'fakeFile';
    }
}
