mediaScanner.window.LinksExplore = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _("mediascanner.links.explore", { url: config.record.url }),
        closeAction: "close",
        width: 800,
        height: 600,
        autoHeight: false,
        fields: [{xtype: 'box'
            , cls: 'ms-preview'
            , anchor: 0
            , autoEl: {
                tag: 'img',
                src: config.record.thumbnail,
                style: {height: '100px'},
                'ext:qtip': '<img src=\'' + config.record.url + '\'/>'
            }
        }, {
            xtype: "mediascanner-grid-links-explore",
            record: config.record,
        }
        ]
    });
    mediaScanner.window.LinksExplore.superclass.constructor.call(this, config);
}
Ext.extend(mediaScanner.window.LinksExplore, MODx.Window, {

});
Ext.reg("mediascanner-window-links-explore", mediaScanner.window.LinksExplore);
