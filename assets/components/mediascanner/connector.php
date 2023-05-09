<?php
/**
 * LinkStrategy Connector
 *
 * @package mediascanner
 *
 * @var modX $modx
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('mediascanner.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mediascanner/');
/** @var MediaScanner $mediaScanner */
$mediaScanner = $modx->getService(
    'mediascanner',
    'MediaScanner',
    $corePath . 'model/mediascanner/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$path = $modx->getOption('processorsPath', $modx->linkstrategy->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
