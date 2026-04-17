<?php
namespace MediaScanner\v3\Processors\Utils;

use MediaScanner\v3\Scanner;
use MODX\Revolution\modContentType;
use MODX\Revolution\modResource;
use MODX\Revolution\modSymLink;
use MODX\Revolution\modWebLink;
use MODX\Revolution\modX;
use MODX\Revolution\Processors\Processor;

class Generate extends Processor
{
    public $languageTopics = ['mediascanner:default'];
    public $objectType = 'mediascanner.generate';
    protected $resource;

    public function getLanguageTopics()
    {
        $total = parent::getLanguageTopics();
        $total[] = 'mediascanner:default';
        return $total;
    }

    public function process()
    {
        $count = $this->generate();
        return $this->success($this->modx->lexicon('mediascanner.scan.complete', ['count' => $count]));
    }

    public function generate(): int
    {
        ini_set('max_execution_time', 300);
        $c = $this->modx->newQuery(modResource::class);
        $c->leftJoin(modContentType::class, 'ContentType', 'ContentType.id = modResource.content_type');
        $c->where([
            'ContentType.mime_type' => 'text/html',
            'class_key:NOT IN' => [modWebLink::class, modSymLink::class]
        ]);

        $count = $this->modx->getCount(modResource::class, $c);

        $scanner = new Scanner($this->modx);

        /** @var modResource[] $resources */
        $resources = $this->modx->getIterator(modResource::class, $c);

        foreach ($resources as $resource) {
            if ($scanner->scan($resource)) {
                $this->modx->log(
                    modX::LOG_LEVEL_INFO,
                    $this->modx->lexicon('mediascanner.scan.status', [
                        'id' => $this->resource->id,
                        'pagetitle' => $this->resource->pagetitle,
                    ])
                );
            } else {
                $this->modx->log(
                    modX::LOG_LEVEL_ERROR,
                    'Error rendering resource: ' . $resource->id
                );
            }
        }

        Scanner::purgeUnlinkedMedia($this->modx);

        return $count;
    }

}