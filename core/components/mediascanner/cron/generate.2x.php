<?php

$tStart= microtime(true);
// Core Path
$coreConfig = dirname(__FILE__, 5) . '/config.core.php';

require_once $coreConfig;
if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', dirname(__FILE__, 5) . '/core/');
}
/* include the modX class */
if (!@include(MODX_CORE_PATH . "model/modx/modx.class.php")) {
    exit();
}
/* start output buffering */
ob_start();

/* Create an instance of the modX class */
$modx= new modX();
if (!is_object($modx) || !($modx instanceof modX)) {
    ob_get_level() && @ob_end_flush();
    exit();
}

$modx->setOption('log_target', 'ECHO');

$modx->initialize('mgr');

$modx->startTime= $tStart;

$corePath = $modx->getOption('mediascanner.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mediascanner/');
/** @var \MediaScanner $mediaScanner */
$mediaScanner = $modx->getService(
    'mediascanner',
    'MediaScanner',
    $corePath . 'model/mediascanner/',
    [
        'core_path' => $corePath
    ]
);
return $modx->runProcessor('mgr/utils/generate', [], ['processors_path' => $mediaScanner->options['processorsPath']]);
