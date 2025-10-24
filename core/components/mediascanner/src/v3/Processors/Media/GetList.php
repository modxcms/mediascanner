<?php
namespace MediaScanner\v3\Processors\Media;

use MediaScanner\v3\Model\Media;
use MediaScanner\v3\Model\MediaResources;
use MODX\Revolution\Processors\Model\GetListProcessor;
use xPDOQuery;

class GetList extends GetListProcessor
{
    public $classKey = Media::class;
    public $languageTopics = ['mediascanner:default'];
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'mediascanner.media';

    public function initialize()
    {
        if ($this->getProperty('export')) {
            $this->setProperty('start', 0);
            $this->setProperty('limit', 0);
        }

        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where([
                'url:LIKE' => "%$query%"
            ]);
        }

        $resource = (int)$this->getProperty('resource');
        if (!empty($resource)) {
            $c->leftJoin(MediaResources::class, 'Resources');
            $c->where(['Resources.resource' => $resource]);
        }

        return $c;
    }

    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('export')) {
            ob_flush();
            ob_start();
            $filename = 'media_'.time() .'.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition:attachment; filename="'.$filename.'"');
            $first = $array[0];
            if (is_object($first)) {
                $first = $first->toArray();
            }
            ob_end_clean();
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($first));
            foreach ($array as $arr) {
                fputcsv($fp, array_values($arr));
            }
            fclose($fp);
            exit;
        }

        return parent::outputArray($array, $count);
    }

}