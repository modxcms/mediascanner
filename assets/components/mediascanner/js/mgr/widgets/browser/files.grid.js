mediaScanner.grid.Files = function (config) {
  config = config || {};

  this._initTemplates();
  this._initComponents();


  Ext.applyIf(config, {
    url: mediaScanner.getConnector(),
    baseParams: {
      action: mediaScanner.getAction('Browser\\Directory\\GetFiles'),
      source: config.source || MODx.config.default_media_source,
    },
    autosave: false,
    preventSaveRefresh: true,
    sm: this.sm,
    fields: [
      "cls",
      "disabled",
      "ext",
      "fullRelativeUrl",
      "id",
      "image",
      "image_height",
      "image_width",
      "leaf",
      "pathRelative",
      "pathname",
      "perms",
      "preview",
      "relativeUrl",
      "thumb",
      "thumb_height",
      "thumb_width",
      "url",
      "msUsed",
      { name: 'name', sortType: Ext.data.SortTypes.asUCString },
      { name: 'size', type: 'float' },
      { name: 'lastmod', type: 'date', dateFormat: 'timestamp' }
    ],
    paging: false,
    remoteSort: false,
    emptyText: _("mediascanner.global.no_records"),
    columns: [
      this.sm,
      {
        header: _("id"),
        dataIndex: "id",
        sortable: true,
        hidden: true,
      },
      {
        header: _("mediascanner.global.url"),
        dataIndex: "name",
        sortable: true,
        width: 80,
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
          if (record.data.msUsed) {
            metaData.css = 'ms-used';
          }

          return '<i class="' + record.data.cls + '"></i> ' + value + '';
        }
      }
    ],
    tbar: this.getTbar(config),
    bbar: this.getPathbar()
  });
  mediaScanner.grid.Files.superclass.constructor.call(this, config);

  this.store.on('load', () => {
    this.pathBarInput.setValue('');
    this.getSelectionModel().selectFirstRow();
    this.bulkActions.updateStatus(this.getSelectionModel().getCount());
  });

  this.getSelectionModel().on('selectionchange', this.showDetails, this, { buffer: 100 });
};
Ext.extend(mediaScanner.grid.Files, MODx.grid.Grid, {
  templates: {},
  selectedRecords: [],

  _initTemplates: function() {
    this.templates.details = new Ext.XTemplate(
        '<div class="details">'
        ,'  <tpl for=".">'
        ,'  <tpl if="preview === 1">'
        ,'      <div class="modx-browser-detail-thumb preview">'
        ,'          <img src="{image:htmlEncode}" loading="lazy" width="{image_width}" height="{image_height}" alt="{name:htmlEncode}" title="{name:htmlEncode}" style="height:auto;" />'
        ,'      </div>'
        ,'  </tpl>'
        ,'  <div class="modx-browser-details-info">'
        ,'      <b>'+_('file_name')+':</b>'
        ,'      <span>{name:htmlEncode}</span>'
        ,'  <tpl if="sizeString !== 0">'
        ,'      <b>'+_('file_size')+':</b>'
        ,'      <span>{sizeString}</span>'
        ,'  </tpl>'
        ,'  <tpl if="imageSizeString !== 0">'
        ,'      <b>'+_('image_size')+':</b>'
        ,'      <span>{imageSizeString}</span>'
        ,'  </tpl>'
        ,'  <tpl if="dateString !== 0">'
        ,'      <b>'+_('last_modified')+':</b>'
        ,'      <span>{dateString}</span>'
        ,'  </tpl>'
        ,'  </div>'
        ,'  </tpl>'
        ,'</div>'
    );
    this.templates.details.compile();
  },

  _initComponents: function() {
    this.sm = new Ext.grid.CheckboxSelectionModel({
      listeners: {
        rowselect: {
          fn: function (sm, rowIndex, record) {
            this.rememberRow(record);
          },
          scope: this,
        },
        rowdeselect: {
          fn: function (sm, rowIndex, record) {
            this.forgotRow(record);
          },
          scope: this,
        },
      },
    });

    this.pathBarInput = new Ext.form.TextField({
      cls: 'modx-browser-filepath'
    });

    this.bulkActions = new Ext.Button({
      text: _("mediascanner.media_browser.select_files"),
      disabled: true,
      updateStatus: function (count) {
        if (count === 0) {
          this.setText(_("mediascanner.media_browser.select_files"));
          this.disable();
          return;
        }

        this.setText(
            _("mediascanner.media_browser.bulk_actions", { count: count })
        );
        this.enable();
      },
      menu: [
        {
          text: _("mediascanner.media_browser.delete"),
          iconCls: "icon-trash",
          itemTpl: mediaScanner.menuItemTpl,
          handler: this.selectedDelete.bind(this),
        }
      ]
    });
  },

  selectedDelete: function() {
    MODx.msg.confirm({
      text: _('file_confirm_remove'),
      url: mediaScanner.getConnector(),
      params: {
        action: mediaScanner.getAction('Browser\\Files\\RemoveMany'),
        'files[]': this.selectedRecords,
        source: this.getStore().baseParams.source
      },
      listeners: {
        success: {
          fn: this.run,
          scope: this
        }
      }
    });
  },

  singleDelete: function() {
    console.log(this);
    MODx.msg.confirm({
      text: _('file_confirm_remove'),
      url: mediaScanner.getConnector(),
      params: {
        action: mediaScanner.getAction('Browser\\Files\\Remove'),
        'file': this.menu.record.pathRelative,
        source: this.store.baseParams.source
      },
      listeners: {
        success: {
          fn: this.run,
          scope: this
        }
      }
    });
  },

  rememberRow: function (record) {
    if (this.selectedRecords.indexOf(record.data.pathRelative) === -1) {
      this.selectedRecords.push(record.data.pathRelative);
      this.bulkActions.updateStatus(this.getSelectionModel().getCount());
    }
  },

  forgotRow: function (record) {
    this.selectedRecords.remove(record.data.pathRelative);
    this.bulkActions.updateStatus(this.getSelectionModel().getCount());
  },

  run: function(params) {
    this.getSelectionModel().clearSelections();
    params = params || {};

    const store = this.getStore();
    Ext.apply(store.baseParams, params);
    store.load();
  },

  getTbar: function (config) {
    return [
      this.bulkActions
    ];
  },

  formatData: function(data) {
    var formatSize = (size) => {
      if (size < 1024) {
        return size + " bytes";
      }

      return (Math.round(((size*10) / 1024))/10) + " KB";
    };

    data.shortName = Ext.util.Format.ellipsis(data.name,18);
    data.sizeString = data.size != 0 ? formatSize(data.size) : 0;
    data.imageSizeString = data.preview != 0 ? data.image_width + "x" + data.image_height + "px": 0;
    data.imageSizeString = data.imageSizeString === "xpx" ? 0 : data.imageSizeString;
    data.dateString = !Ext.isEmpty(data.lastmod) ? new Date(data.lastmod).format(MODx.config.manager_date_format + " " + MODx.config.manager_time_format) : 0;

    return data;
  },

  showDetails: function() {
    var sm = this.getSelectionModel();
    var detailPanel = this.config.detailPanel.el;

    if (sm.getCount() > 1) {
      detailPanel.update('');
      return;
    }

    var row = sm.getSelected();
    if (!row) {
      detailPanel.update('');
      return;
    }

    var data = this.formatData(row.data);
    // sync the selected file in browser view and tree
    // we have to take care of the tree loosing sync after a file is deleted
    // and this.config.tree.getNodeById(data.pathRelative) being undefined
    if (this.config.tree.getNodeById(data.pathRelative)) {
      // this is necessary to prevent the whole tree from refreshing
      // e.g. like this we set the correct activeNode which is then used to determine the parent node
      this.config.tree.cm.activeNode = this.config.tree.getNodeById(data.pathRelative);
      // and this to have the visual syncing of selected items in browser view and tree
      this.config.tree.getSelectionModel().select(this.config.tree.getNodeById(data.pathRelative));
    }
    // keeps the bottom filepath bar in sync with the selected file
    this.pathBarInput.setValue(((data.fullRelativeUrl.indexOf('http') === -1 ? '/' : '') + data.fullRelativeUrl).replace(/^\/*/, '/'));

    detailPanel.hide();
    this.templates.details.overwrite(detailPanel, data);
    detailPanel.slideIn('l', {
      stopFx: true,
      duration: '.2'
    });
  },

  getPathbar: function() {
    return {
      cls: 'modx-browser-pathbbar',
      items: [this.pathBarInput]
    };
  },

  getMenu: function () {
    var m = [];
    if (!this.menu.record.msUsed) {
        m.push({
            text: _("mediascanner.media_browser.delete"),
            iconCls: "icon-trash",
            handler: this.singleDelete,
            scope: this,
        });
    }
    return m;
  },
});
Ext.reg("mediascanner-grid-files", mediaScanner.grid.Files);
