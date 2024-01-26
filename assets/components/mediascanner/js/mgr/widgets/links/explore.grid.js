mediaScanner.grid.LinksExplore = function (config) {
  config = config || {};

  Ext.applyIf(config, {
    url: mediaScanner.getConnector(),
    baseParams: {
      action:  mediaScanner.getAction('Media\\Links\\GetList'),
      media: config.record.id,
    },
    autosave: false,
    preventSaveRefresh: true,
    fields: [
      "medium",
      "resource",
      "resource_id",
      "resource_menutitle",
      "resource_pagetitle",
    ],
    paging: true,
    remoteSort: true,
    emptyText: _("mediascanner.global.no_records"),
    showActionsColumn: false,
    grouping: false,
    groupBy: "text",
    sortBy: "text",
    singleText: _("mediascanner.global.resource"),
    pluralText: _("mediascanner.global.resources"),
    columns: [
      {
        header: _("id"),
        dataIndex: "medium",
        sortable: false,
        hidden: true,
      },
      {
        header: _("resource"),
        dataIndex: "resource",
        sortable: true,
        hidden: true,
      },
      {
        header: _("mediascanner.global.resource"),
        dataIndex: "resource_pagetitle",
        sortable: true,
        width: 60,
        renderer: function (value, metaData, record) {
          var linktitle = record.data.resource_menutitle
            ? record.data.resource_menutitle
            : record.data.resource_pagetitle;
          return (
            '<a href="' +
            MODx.config.manager_url +
            "?a=resource/update&id=" +
            record.data.resource +
            '">' +
            linktitle +
            "</a>"
          );
        },
      },
    ],
    tbar: this.getTbar(config),
  });
  mediaScanner.grid.LinksExplore.superclass.constructor.call(this, config);
};
Ext.extend(mediaScanner.grid.LinksExplore, MODx.grid.Grid, {
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

  exportFilters: function (comp, search) {
    var s = this.getStore();
    var filters = "export=true&HTTP_MODAUTH=" + MODx.siteId;
    Object.keys(s.baseParams).forEach((key) => {
      filters += "&" + key + "=" + s.baseParams[key];
    });
    window.location = this.config.url + "?" + filters;
  },

  filterSearch: function (comp, search) {
    var s = this.getStore();
    s.baseParams[comp.filterName] = search;
    this.getBottomToolbar().changePage(1);
  },

  clearFilters: function (btn, e) {
    this.getTopToolbar().items.items.forEach(function (item) {
      if (item.filterName) {
        item.reset();
      }
    });
    var s = this.getStore();
    s.baseParams = {
      action: s.baseParams.action,
      link: this.config.record.id,
    };
    this.getBottomToolbar().changePage(1);
  },
});
Ext.reg("mediascanner-grid-links-explore", mediaScanner.grid.LinksExplore);
