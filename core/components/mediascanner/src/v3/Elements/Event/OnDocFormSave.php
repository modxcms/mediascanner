<?php

namespace MediaScanner\v3\Elements\Event;


use MediaScanner\v3\Scanner;

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
