mediaScanner.window.LinksExplore = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _("mediaScanner.links.explore", { url: config.record.url }),
        closeAction: "close",
        width: 800,
        height: 600,
        autoHeight: false,
        fields: [
        {
            html: _("mediaScanner.links.explore_desc"),
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
