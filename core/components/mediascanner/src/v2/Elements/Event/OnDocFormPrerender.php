<?php
namespace MediaScanner\v2\Elements\Event;

class OnDocFormPrerender extends Event
{

    public function run()
    {
        $this->modx->log(\modX::LOG_LEVEL_ERROR, '[MediaScanner] OnDocFormPrerender');
        $mode = $this->getOption('mode');
        $resource =  $this->getOption('resource');
        if (($mode === \modSystemEvent::MODE_NEW) || !$resource) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, '[MediaScanner] Resource not found');
            return;
        }

        $this->modx->controller->addLexiconTopic('mediascanner:default');
        $this->modx->regClientCSS($this->mediaScanner->options['cssUrl'] . 'mgr.css');
        $this->modx->regClientStartupScript($this->mediaScanner->options['jsUrl'] . 'mgr/mediascanner.js');
        $this->modx->regClientStartupScript($this->mediaScanner->options['jsUrl'] . 'mgr/utils/combos.js');
        $this->modx->regClientStartupScript($this->mediaScanner->options['jsUrl'] . 'mgr/widgets/media.grid.js');

        $this->mediaScanner->options['modx3'] = ($this->modx->version['version'] > 3);

        $this->modx->regClientStartupHTMLBlock('
            <script type="text/javascript">
                Ext.onReady(function() {
                    mediaScanner.config = '.$this->modx->toJSON($this->mediaScanner->options).';
                });
            </script>
        ');

        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            Ext.onReady(function() {
                var tab = Ext.getCmp("modx-resource-tabs");
                if (tab) {
                    tab.add({
                        title: _("mediascanner"),
                        cls: "modx-resource-tab",
                        layout: "form",
                        labelAlign: "top",
                        labelSeparator: "",
                        bodyCssClass: "tab-panel-wrapper main-wrapper",
                        autoHeight: true,
                        defaults: {
                            border: false,
                            msgTarget: "under",
                        },
                        items: [{
                            html: "<p>" + _("mediascanner.resource.media_desc") + "</p>",
                            cls: "panel-desc ls-panel-desc",
                        }, {
                            xtype: "mediascanner-grid-media",
                            resource: ' . $resource->get('id') . ',
                            preventRender: true
                        }]
                    })
                }
            });
        </script>');
    }
}
