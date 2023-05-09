<?php

require_once dirname(__FILE__) . '/model/mediascanner/mediascanner.class.php';
abstract class MediaScannerBaseManagerController extends modExtraManagerController
{
    /** @var \MediaScanner $medaiScanner */
    public $mediaScanner;

    public function initialize(): void
    {
        if ($this->modx->version['version'] > 3) {
            $this->mediaScanner = $this->modx->services->get('mediascanner');
        } else {
            $corePath = $this->modx->getOption('mediascanner.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mediascanner/');
            $this->mediaScanner = $this->modx->getService(
                'mediascanner',
                'MediaScanner',
                $corePath . 'model/mediascanner/',
                array(
                    'core_path' => $corePath
                )
            );
        }
        $this->addCss($this->mediaScanner->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/mediascanner.js');
        $this->mediaScanner->options['allowRegenerate'] = (bool) $this->mediaScanner->getOption('allow_regenerate_button');
        $this->mediaScanner->options['modx3'] = ($this->modx->version['version'] >= 3);

        $this->addHtml('
            <script type="text/javascript">
                Ext.onReady(function() {
                    mediaScanner.config = ' . $this->modx->toJSON($this->mediaScanner->options) . ';
                });
            </script>
        ');

        parent::initialize();
    }

    public function getLanguageTopics(): array
    {
        return array('mediascanner:default');
    }

    public function checkPermissions(): bool
    {
        return true;
    }

    public function addLastJavascript($script)
    {
        $this->head['lastjs'][] = $script . '?v=1.1.0';
    }
}
