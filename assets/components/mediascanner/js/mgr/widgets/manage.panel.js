mediaScanner.panel.Manage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: "mediascanner-panel-manage",
        border: false,
        cls: "container form-with-labels",
        url: mediaScanner.config.modx3 ? MODx.config.connector_url : mediaScanner.config.connectorUrl,
        bypassValidCheck: true,
        saveMsg: _("mediascanner.scan.ing"),
        baseParams: {
            action: mediaScanner.config.modx3 ? "MediaScanner\\Processors\\Utils\\Generate" : "mgr/utils/generate",
        },
        useLoadingMask: true,
        items: [
            {
                html: '<h2>' + _('mediascanner.manage.page_title') + '</h2>',
                border: false,
                xtype: "modx-header",
            },
            {
                xtype: 'modx-tabs',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                border: true,
                activeItem: 0,
                collapsible: false,
                animCollapse: false,
                itemId: "tabs",
                items: [
                    {
                        title: _('mediascanner.manage.media'),
                        layout: 'form',
                        items: [
                            {
                                html: '<p>' + _('mediascanner.manage.media_desc') + '</p>',
                                border: false,
                                cls: 'panel-desc'
                            },
                            {
                                cls: 'main-wrapper',
                                width: '100%',
                                height: '800px',
                                xtype: 'mediascanner-browser-media',
                            }
                        ]
                    },
                    {
                        title: _('mediascanner.manage.linked_media'),
                        layout: 'form',
                        items: [
                            {
                                html: '<p>' + _('mediascanner.manage.linked_media_desc') + '</p>',
                                border: false,
                                cls: 'panel-desc'
                            },
                            {
                                cls: 'main-wrapper',
                                width: '100%',
                                height: '800px',
                                xtype: 'mediascanner-grid-media',
                            }
                        ]
                    }
                ]
        }
        ],
        listeners: {
            'failure': function(f, r, o, c) {
                MODx.msg.alert(_('mediascanner.err.scan_failed'), _('mediascanner.err.scan_failed_desc'));
            }
        }
    });
    mediaScanner.panel.Manage.superclass.constructor.call(this, config);
};
Ext.extend(mediaScanner.panel.Manage, MODx.FormPanel);
Ext.reg('mediascanner-panel-manage', mediaScanner.panel.Manage, {
});
