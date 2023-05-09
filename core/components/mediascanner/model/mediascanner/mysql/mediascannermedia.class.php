<?php
/**
 * @package mediascanner
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/mediascannermedia.class.php');
class MediaScannerMedia_mysql extends MediaScannerMedia {}
?>