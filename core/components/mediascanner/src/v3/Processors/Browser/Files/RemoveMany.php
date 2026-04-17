<?php
namespace MediaScanner\v3\Processors\Browser\Files;

use MODX\Revolution\Processors\Processor;
use MODX\Revolution\Sources\modFileMediaSource;
use MODX\Revolution\Sources\modMediaSource;

class RemoveMany extends Processor
{
    /** @var modMediaSource|modFileMediaSource $source */
    public $source;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('file_remove');
    }

    public function getLanguageTopics()
    {
        return ['file', 'mediascanner:default'];
    }

    public function process()
    {
        $files = $this->getProperty('files');
        if (empty($files)) {
            return $this->modx->error->failure($this->modx->lexicon('file_err_ns'));
        }

        $loaded = $this->getSource();
        if (!($this->source instanceof modMediaSource)) {
            return $loaded;
        }

        if (!$this->source->checkPolicy('remove')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }

        foreach ($files as $file) {
            $file = preg_replace('/[\.]{2,}/', '', $file);
            $file = urldecode($file);
            $success = $this->source->removeObject($file);

            if (empty($success)) {
                $errors = $this->source->getErrors();
                $msg = implode("\n", $errors);

                return $this->failure($msg);
            }
        }

        return $this->success();
    }

    /**
     * @return boolean|string
     */
    public function getSource()
    {
        $source = $this->getProperty('source', null);
        if (empty($source)) {
            return $this->modx->lexicon('mediascanner.err.source_required');
        }

        /** @var modMediaSource $source */
        $this->source = modMediaSource::getDefaultSource($this->modx, $source);
        if (!$this->source->getWorkingContext()) {
            return $this->modx->lexicon('permission_denied');
        }
        $this->source->setRequestProperties($this->getProperties());
        return $this->source->initialize();
    }
}
