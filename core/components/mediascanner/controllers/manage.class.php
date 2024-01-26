<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

class MediaScannerManageManagerController extends MediaScannerBaseManagerController
{

    public function process(array $scriptProperties = []): void
    {
    }

    public function getPageTitle(): string
    {
        return $this->modx->lexicon('mediascanner');
    }

    public function loadCustomCssJs(): void
    {
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/utils/combos.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/browser/files.grid.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/browser/media.browser.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/links/explore.grid.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/links/explore.window.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/media.grid.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/widgets/manage.panel.js');
        $this->addLastJavascript($this->mediaScanner->getOption('jsUrl') . 'mgr/sections/manage.js');

        $this->addHtml(
            '
            <script type="text/javascript">
                Ext.onReady(function() {
                    MODx.load({ xtype: "mediascanner-page-manage"});
                });
            </script>
        '
        );
    }

    public function getTemplateFile(): string
    {
        return $this->mediaScanner->getOption('templatesPath') . 'manage.tpl';
    }

}
