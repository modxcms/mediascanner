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
            ob_flush();
            ob_start();
            $filename = 'media_links_'.time() .'.csv';
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

return 'MediaScannerMediaGetListProcessor';
