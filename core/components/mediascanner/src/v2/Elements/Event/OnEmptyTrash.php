<?php

namespace MediaScanner\v2\Elements\Event;

use MediaScanner\v2\Scanner;

class OnEmptyTrash extends Event
{
    public function run()
    {
        $ids = $this->getOption('ids');
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        $this->modx->removeCollection('MediaResources', [
            'resource:IN' => $ids
        ]);

        Scanner::purgeUnlinkedMedia($this->modx);
    }
}