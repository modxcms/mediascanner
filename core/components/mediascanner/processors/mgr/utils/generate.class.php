<?php

use MediaScanner\v2\Scanner;

class MediaScannerUtilsGenerateProcessor extends modProcessor
{
    public $languageTopics = ['mediascanner:default'];
    public $objectType = 'mediascanner.generate';
    protected $resource;

    public function process()
    {
        $count = $this->generate();
        return $this->success($this->modx->lexicon('mediascanner.scan.complete', ['count' => $count]));
    }

    public function generate()
    {
        $c = $this->modx->newQuery('modResource');
        $c->where([
            'contentType' => 'text/html',
            [
                [
                    'class_key:!=' => 'modWebLink'
                ],
                [
                    'class_key:!=' => 'modSymLink'
                ],
            ]
        ]);

        $count = $this->modx->getCount('modResource', $c);

        $scanner = new Scanner($this->modx);

        /** @var modResource[] $resources */
        $resources = $this->modx->getIterator('modResource', $c);

        foreach ($resources as $resource) {
            $scanner->scan($resource);

            $this->modx->log(
                \modX::LOG_LEVEL_INFO,
                $this->modx->lexicon('mediascanner.scan.status', [
                    'id' => $this->resource->id,
                    'pagetitle' => $this->resource->pagetitle,
                ])
            );
        }

        Scanner::purgeUnlinkedMedia($this->modx);

        return $count;
    }

}
return 'MediaScannerUtilsGenerateProcessor';