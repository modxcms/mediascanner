<?php

class MediaScannerMediaGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MediaScannerMediaResources';
    public $languageTopics = array('mediascanner:default');
    public $defaultSortField = 'resource_pagetitle';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'mediascanner.mediaresources';

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
        $media = (int)$this->getProperty('media');
        if (!empty($media)) {
            $c->where(['medium' => $media]);
        }
        $c->leftJoin('modResource', 'Resource');
        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where([
                'Resource.pagetitle:LIKE' => "%$query%"
            ]);
        }
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select($this->modx->getSelectColumns('modResource', 'Resource', 'resource_', ['pagetitle', 'longtitle', 'menutitle', 'id']));

        return $c;
    }

    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('export')) {
            $first = $array[0];
            if (is_object($first)) {
                $first = $first->toArray();
            }
            $filename = 'media_links_'.time() .'.csv';
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
