<?php
/**
 * @var MODX\Revolution\modX $modx
 * @var array $scriptProperties
 *
 */

if (empty($modx->version)) {
    $modx->getVersionData();
}
$version = $modx->version['version'] < 3 ? 'v2' : 'v3';

if ($version === 'v2') {
    $corePath = $modx->getOption('mediascanner.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mediascanner/');
    $mediaScanner = $modx->getService(
        'mediascanner',
        'MediaScanner',
        $corePath . 'model/mediascanner/',
        [
            'core_path' => $corePath
        ]
    );
} else {
    $mediaScanner = $modx->services->get('mediascanner');
}

$className = "\\MediaScanner\\$version\\Elements\\Event\\{$modx->event->name}";
if (class_exists($className)) {
    /** @var $event */
    $event = new $className($mediaScanner, $scriptProperties);
    $event->run();
} else {
    $modx->log(\xPDO::LOG_LEVEL_ERROR, "Class {$className} not found");
}