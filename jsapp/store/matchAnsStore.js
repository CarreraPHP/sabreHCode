Ext.define('MyApp.store.matchAnsStore', {
    extend : 'Ext.data.Store',
    requires : ['MyApp.model.matchQuesModel'],

    constructor : function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            autoSync : true,
            autoLoad : false,
            model : 'MyApp.model.matchQuesModel',
            proxy : {
                type : 'ajax',
                url : 'data/QuizData.php',
                extraParams: {
                    module: 'matchQuiz'
                },
                reader : {
                    type : 'json',
                    root : 'data'
                }
            }
        }, cfg)]);
    }
});
