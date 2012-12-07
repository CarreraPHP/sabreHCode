Ext.define('MyApp.store.userAccessStore', {
    extend : 'Ext.data.Store',
    requires : ['MyApp.model.userAccessModel'],

    constructor : function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            autoLoad : true,
            autoSync : true,
            storeId : 'SabUserAccessStore',
            model : 'MyApp.model.userAccessModel',
            proxy : {
                type : 'ajax',
                url : 'data/DataList.php',
                extraParams: {
                    module: 'userAccess',
                    display: 'list'
                },
                reader : {
                    type : 'json'
                }
            }
        }, cfg)]);
    }
});
