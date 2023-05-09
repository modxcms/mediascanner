<?php
/**
 * @package mediascanner
 */
$xpdo_meta_map['MediaScannerMedia']= array (
  'package' => 'mediascanner',
  'version' => '0.1',
  'table' => 'mediascanner_media',
  'extends' => 'xPDOSimpleObject',
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
          'length' => '',
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
      'class' => 'MediaScannerMediaResources',
      'local' => 'id',
      'foreign' => 'medium',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
