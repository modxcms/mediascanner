<?php

namespace MediaScanner\v2\Elements\Event;

use MediaScanner\v2\Scanner;

class OnDocFormSave extends Event
{
    public function run()
    {
        $resource = $this->getOption('resource');
        $scanOnSave = $this->mediaScanner->getOption('scan_on_save') === '1';
        if (empty($resource) || !$scanOnSave
        ) {
            return;
        }

        $scanner = new Scanner($this->modx);
        $scanner->scan($resource);
        Scanner::purgeUnlinkedMedia($this->modx);
    }
}