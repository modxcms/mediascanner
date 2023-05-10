<?php

namespace MediaScanner\v3\Elements\Event;

use MediaScanner\v3\MediaScanner;
use MODX\Revolution\modX;

abstract class Event
{
    /**
     * A reference to the modX object.
     * @var modX $modx
     */
    public $modx = null;

    protected MediaScanner $mediaScanner;

    /** @var array */
    protected $sp = [];

    public function __construct(MediaScanner $mediaScanner, array $scriptProperties)
    {
        $this->mediaScanner =& $mediaScanner;
        $this->modx =& $this->mediaScanner->modx;
        $this->sp = $scriptProperties;
    }

    abstract public function run();

    protected function getOption($key, $default = null, $skipEmpty = false)
    {
        return $this->modx->getOption($key, $this->sp, $default, $skipEmpty);
    }
}
