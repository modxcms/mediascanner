var Mediascanner = function (config) {
    config = config || {};
    Mediascanner.superclass.constructor.call(this, config);
};
Ext.extend(Mediascanner, Ext.Component, {

    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    browser: {},

    getConnector() {
        return mediaScanner.config.modx3 ? MODx.config.connector_url : mediaScanner.config.connectorUrl;
    },

    getAction(processor) {
        if (mediaScanner.config.modx3) {
            return `MediaScanner\\${processor}`;
        }

        return `mgr/${processor.split('\\').join('/').toLowerCase()}`
    },

    menuItemTpl: new Ext.XTemplate('<a id="{id}" class="{cls} x-unselectable" hidefocus="true" unselectable="on" href="{href}"<tpl if="hrefTarget"> target="{hrefTarget}"</tpl>><i class="x-menu-item-icon icon {iconCls}" style="text-align: center;margin-top:1px;"></i><span class="x-menu-item-text">{text}</span></a>')

});
Ext.reg('mediaScanner', Mediascanner);
mediaScanner = new Mediascanner();
