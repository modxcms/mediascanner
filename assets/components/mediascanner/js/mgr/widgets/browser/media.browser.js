mediaScanner.browser.Media = function(config) {
    config = config || {};
    config.source = config.source || MODx.config.default_media_source;

    this.tree = MODx.load({
        xtype: 'modx-tree-directory',
        scope: this,
        source: config.source,
        hideFiles: true,
        openTo: config.openTo || '',
        rootId: config.rootId || '/',
        rootName: _('files'),
        rootVisible: true,
        hideSourceCombo: false,
        useDefaultToolbar: false,
        tbar: [],
        listeners: {
            afterUpload: {
                fn: function() {
                    this.view.run();
                },
                scope: this
            },
            changeSource: {
                fn: function(s) {
                    this.config.source = s;
                    this.load('/');
                },
                scope: this
            },
            nodeclick: {
                fn: function(n, e) {
                    n.select();
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                },
                scope: this
            },
            afterrender: {
                fn: function(tree) {
                    tree.root.expand();
                },
                scope: this
            }
        }
    });

    var detailPanel = new Ext.BoxComponent({
        region: 'east',
        width: 250,
        cls: 'modx-browser-details-ct',
        split: true
    });

    this.grid = MODx.load({
        xtype: 'mediascanner-grid-files',
        cls: 'main-wrapper',
        tree: this.tree,
        detailPanel: detailPanel
    });

    this.tree.on('click', function(node, e) {
        this.load(node.id);
    }, this);

    Ext.applyIf(config, {
        cls: 'modx-browser container',
        layout: 'border',
        width: '98%',
        height: '95%',
        items: [
            {
                region: 'west',
                width: 250,
                items: this.tree,
                cls: 'modx-browser-tree shadowbox',
                autoScroll: true,
                split: true
            },
            {
                region: 'center',
                layout: 'fit',
                items: this.grid,
                cls: 'modx-browser-view-ct',
                autoScroll: true,
                border: false
            },
            detailPanel
        ]
    });
    mediaScanner.browser.Media.superclass.constructor.call(this, config);
    this.config = config;
};
Ext.extend(mediaScanner.browser.Media, Ext.Container, {
    load: function(dir) {
        this.grid.run({
            dir: dir || (Ext.isEmpty(this.config.openTo) ? '/' : this.config.openTo),
            source: this.config.source,
            allowedFileTypes: this.config.allowedFileTypes || ''
        });
    }
});
Ext.reg('mediascanner-browser-media', mediaScanner.browser.Media);
