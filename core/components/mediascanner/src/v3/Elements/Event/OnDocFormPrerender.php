<?php

namespace MediaScanner\Elements\Event;

use MediaScanner\v2\Element\Event\Event;
use MODX\Revolution\modSystemEvent;

class OnDocFormPrerender extends Event
{

    public function run()
    {
        $mode = $this->getOption('mode');
        $resource =  $this->getOption('resource');
        if (($mode === modSystemEvent::MODE_NEW) || !$resource) {
            return;
        }
        $this->modx->controller->addLexiconTopic('mediascanner:default');
        $this->modx->regClientCSS($this->mediaScanner->config['cssUrl'] . 'mgr.css');
        $this->modx->regClientStartupScript($this->mediaScanner->config['jsUrl'] . 'mgr/mediascanner.js');
        $this->modx->regClientStartupScript($this->mediaScanner->config['jsUrl'] . 'mgr/utils/combos.js');
        $this->modx->regClientStartupScript($this->mediaScanner->config['jsUrl'] . 'mgr/widgets/media.grid.js');

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
