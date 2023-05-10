<?php

namespace MediaScanner\v3\Elements\Event;


use MediaScanner\v3\Model\MediaResources;
use MediaScanner\v3\Scanner;

class OnEmptyTrash extends Event
{
    public function run()
    {
        $ids = $this->getOption('ids');
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        $this->modx->removeCollection(MediaResources::class, [
            'resource:IN' => $ids
        ]);

        Scanner::purgeUnlinkedMedia($this->modx);
    }
}
