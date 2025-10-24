<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package mediascanner
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('mediascanner.core_path', null, $modx->getOption('core_path') . 'components/mediascanner/') . 'model/';
            
            $modx->addPackage('mediascanner', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('MediaScannerMedia');
            $manager->createObjectContainer('MediaScannerMediaResources');

            break;
    }
}

return true;