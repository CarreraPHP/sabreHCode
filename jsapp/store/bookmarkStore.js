/*
 * File: jsapp/store/base/courseStore.js
 */

Ext.define('MyApp.store.bookmarkStore', {
    extend: 'Ext.data.Store',
    requires: [
    'MyApp.model.courseModel'
    ],

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            autoLoad: true,
            autoSync: true,
            storeId: 'SabBookmarkStore',
            model: 'MyApp.model.courseModel',
            proxy: {
                type: 'ajax',
                url: 'data/DataList.php?module=accessAllBookmark',
                reader: {
                    type: 'json',
                    idProperty: 'id',
                    root: 'data'
                }
            }
        }, cfg)]);
    }
});
