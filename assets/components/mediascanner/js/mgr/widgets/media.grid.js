mediaScanner.grid.Media = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    url: mediaScanner.getConnector(),
    baseParams: {
      action: mediaScanner.getAction('Media\\GetList'),
      resource: config.resource || null,
    },
    autosave: false,
    preventSaveRefresh: true,
    fields: ["id", "url", "msUsed"],
    paging: true,
    remoteSort: true,
    emptyText: _("mediascanner.global.no_records"),
    columns: [
      {
        header: _("id"),
        dataIndex: "id",
        sortable: true,
        hidden: true,
      },
      {
        header: _("mediascanner.global.url"),
        dataIndex: "url",
        sortable: true,
        width: 80,
      }
    ],
    tbar: this.getTbar(config),
  });
  mediaScanner.grid.Media.superclass.constructor.call(this, config);
};
Ext.extend(mediaScanner.grid.Media, MODx.grid.Grid, {
  getTbar: function (config) {
    return [
      {
        text: _("mediascanner.global.export"),
        handler: this.exportFilters,
      },
      "->",
      {
        xtype: "textfield",
        blankText: _("mediascanner.global.search"),
        filterName: "query",
        listeners: {
          change: this.filterSearch,
          scope: this,
          render: {
            fn: function (cmp) {
              new Ext.KeyMap(cmp.getEl(), {
                key: Ext.EventObject.ENTER,
                fn: this.blur,
                scope: cmp,
              });
            },
            scope: this,
          },
        },
      },
      {
        xtype: "button",
        text: _("mediascanner.global.clear"),
        handler: this.clearFilters,
        scope: this,
      },
    ];
  },

  getMenu: function () {
    var m = [];
    m.push({
      text: _("mediascanner.global.explore"),
      handler: this.exploreLink,
    });
    return m;
  },

  exportFilters: function (comp, search) {
    const store = this.getStore();
    const urlParams = new URLSearchParams(store.baseParams);
    urlParams.set('export', 'true');
    urlParams.set('HTTP_MODAUTH', MODx.siteId);

    window.location = `${this.config.url}?${urlParams.toString()}`;
  },

  exploreLink: function (btn, e) {
    const record = this.menu.record;
    const win = MODx.load({
      xtype: "mediascanner-window-links-explore",
      record: record,
    });
    win.show(e.target);
  },

  filterSearch: function (comp, search) {
    const store = this.getStore();
    store.baseParams[comp.filterName] = search;
    this.getBottomToolbar().changePage(1);
  },

  clearFilters: function (btn, e) {
    const store = this.getStore();
    const baseParams = store.baseParams;
    this.getTopToolbar().items.items.forEach(function (item) {
      if (item.filterName) {
        item.reset();
        delete baseParams[item.filterName];
      }
    });

    store.baseParams = baseParams;
    this.getBottomToolbar().changePage(1);
  }
});
Ext.reg("mediascanner-grid-media", mediaScanner.grid.Media);
