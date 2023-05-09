<?php

namespace MediaScanner\Elements\Event;

use MediaScanner\Model\ResourceLinks;
use MediaScanner\v2\Element\Event\Event;

class OnEmptyTrash extends Event
{
    public function run()
    {
        $ids = $this->getOption('ids');
        if (empty($ids) || !is_array($ids)) {
            return;
        }
        $this->modx->removeCollection(ResourceLinks::class, [
            'resource:IN' => $ids
        ]);
        $this->modx->removeCollection(ResourceLinksText::class, [
            'resource:IN' => $ids
        ]);
    }
}
