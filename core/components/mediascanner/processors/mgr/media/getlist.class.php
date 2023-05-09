<?php

class MediaScannerMediaGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MediaScannerMedia';
    public $languageTopics = array('mediascanner:default');
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
            $c->leftJoin('MediaScannerMediaResources', 'Resources');
            $c->where(['Resources.resource' => $resource]);
        }

        return $c;
    }

    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('export')) {
            $first = $array[0];
            if (is_object($first)) {
                $first = $first->toArray();
            }
            $filename = 'media_'.time() .'.csv';
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($first));
            foreach ($array as $arr) {
                fputcsv($fp, array_values($arr));
            }
            fclose($fp);
            header('Content-type: text/csv');
            header('Content-disposition:attachment; filename="'.$filename.'"');
            readfile($filename);
            return '';
        }

        return parent::outputArray($array, $count);
    }

}

return 'MediaScannerMediaGetListProcessor';