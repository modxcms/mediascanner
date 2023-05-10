<?php
namespace MediaScanner\v3\Model\mysql;

use xPDO\xPDO;

class Media extends \MediaScanner\v3\Model\Media
{

    public static $metaMap = array (
        'package' => 'MediaScanner\\v3\\Model\\',
        'version' => '3.0',
        'table' => 'mediascanner_media',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'url' => NULL,
        ),
        'fieldMeta' => 
        array (
            'url' => 
            array (
                'dbtype' => 'text',
                'phptype' => 'string',
                'null' => false,
            ),
        ),
        'indexes' => 
        array (
            'url' => 
            array (
                'alias' => 'url',
                'primary' => false,
                'unique' => true,
                'type' => 'BTREE',
                'columns' => 
                array (
                    'url' => 
                    array (
                        'length' => '254',
                        'collation' => 'A',
                        'null' => false,
                    ),
                ),
            ),
        ),
        'composites' => 
        array (
            'Resources' => 
            array (
                'class' => 'MediaScanner\\v3\\Model\\MediaResources',
                'local' => 'id',
                'foreign' => 'medium',
                'cardinality' => 'many',
                'owner' => 'local',
            ),
        ),
    );

}
