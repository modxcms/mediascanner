<?php
namespace MediaScanner\v3\Processors\Browser\Directory;

use MediaScanner\v3\Model\Media;
use MODX\Revolution\Processors\Processor;
use MODX\Revolution\Sources\modFileMediaSource;
use MODX\Revolution\Sources\modMediaSource;

class GetFiles extends Processor
{
    /** @var modMediaSource|modFileMediaSource $source */
    public $source;
    public function checkPermissions() {
        return $this->modx->hasPermission('file_list');
    }

    public function getLanguageTopics() {
        return array('file');
    }

    public function initialize() {
        $this->setDefaultProperties(array(
            'dir' => '',
        ));
        $dir = rawurldecode($this->getProperty('dir'));
        $dir = preg_replace('/[\.]{2,}/', '', htmlspecialchars($dir));
        if ($dir === 'root') {
            $dir = '';
        }
        $this->setProperty('dir', $dir);

        return true;
    }

    public function process() {
        if (!$this->getSource()) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }
        $allowedFileTypes = $this->getProperty('allowedFileTypes');
        if (empty($allowedFileTypes)) {
            // Prevent overriding media source configuration
            unset($this->properties['allowedFileTypes']);
        }
        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('list')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }

        $base = rtrim(preg_replace('/\/{2,}/', '/', $this->source->getBaseUrl()), '/') . '/';

        $list = $this->source->getObjectsInContainer($this->getProperty('dir'));
        $filesToScan = [];

        foreach ($list as &$item) {
            $url = $base . urldecode($item['pathRelative']);
            $filesToScan[$url] =& $item;
        }

        if (empty($filesToScan)) {
            return $this->outputArray($list);
        }

        /** @var \MediaScannerMedia[] $media */
        $media = $this->modx->getIterator(Media::class, ['url:IN' => array_keys($filesToScan)]);

        foreach ($media as $medium) {
            $filesToScan[$medium->url]['msUsed'] = true;
        }

        return $this->outputArray($list);
    }

    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource() {
        $this->source = modMediaSource::getDefaultSource($this->modx,$this->getProperty('source'));
        if (empty($this->source) || !$this->source->getWorkingContext()) {
            return false;
        }
        return $this->source;
    }
}