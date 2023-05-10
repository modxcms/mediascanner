<?php
namespace MediaScanner\v3\Processors\Utils;

use MediaScanner\v3\Scanner;
use MODX\Revolution\modResource;
use MODX\Revolution\modX;
use MODX\Revolution\Processors\Processor;

class Generate extends Processor
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
        $c = $this->modx->newQuery(modResource::class);
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

        $count = $this->modx->getCount(modResource::class, $c);

        $scanner = new Scanner($this->modx);

        /** @var modResource[] $resources */
        $resources = $this->modx->getIterator(modResource::class, $c);

        foreach ($resources as $resource) {
            $scanner->scan($resource);

            $this->modx->log(
                modX::LOG_LEVEL_INFO,
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