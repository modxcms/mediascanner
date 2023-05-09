mediaScanner.page.Manage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        formpanel: "mediascanner-panel-manage",
        buttons: this.getButtons(),
        components: [
        {
            xtype: "mediascanner-panel-manage",
            renderTo: "mediascanner-panel-manage-div"
        }
        ]
    });
    mediaScanner.page.Manage.superclass.constructor.call(this, config);
};
Ext.extend(mediaScanner.page.Manage, MODx.Component, {
    getButtons: function () {
        var buttons = [];
        if (mediaScanner.config.allowRegenerate) {
            buttons.push({
                text: "<i class=\"icon icon-refresh\"></i> " + _("mediascanner.scan"),
                cls: "secondary-button",
                method: "remote",
                process: mediaScanner.getAction('Utils\\Generate'),
            });
        }
        return buttons;
    }
});
Ext.reg("mediascanner-page-manage", mediaScanner.page.Manage);
