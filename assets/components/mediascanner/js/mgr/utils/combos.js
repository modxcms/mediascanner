mediaScanner.combo.Context = function (config) {
    config = config || {};

    var baseParams = Ext.applyIf(config.baseParams || {}, {
        action: mediaScanner.config.modx3 ? "MediaScanner\\Processors\\Utils\\Contexts" : "mgr/utils/contexts",
    });

    Ext.applyIf(config, {
        name: "context",
        hiddenName: "context",
        displayField: "name",
        editable: false,
        valueField: "key",
        value: MODx.config.default_context,
        fields: ["key","name"],
        emptyText: _("mediascanner.context_key"),
        pageSize: 20,
        url: mediaScanner.config.modx3 ? MODx.config.connector_url : mediaScanner.config.connectorUrl,
        baseParams: baseParams,
    });
    mediaScanner.combo.Context.superclass.constructor.call(this, config);
};
Ext.extend(mediaScanner.combo.Context, MODx.combo.ComboBox);
Ext.reg("mediascanner-combo-context", mediaScanner.combo.Context);
