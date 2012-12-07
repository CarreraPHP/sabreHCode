Ext.define('MyApp.store.quizResultStore', {
    extend : 'Ext.data.Store',
    requires : ['MyApp.model.quizResultModel'],

    constructor : function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            autoSync : true,
            storeId : 'SabUserAccessStore',
            model : 'MyApp.model.quizResultModel',
            proxy : {
                type : 'ajax',
                url : 'data/QuizData.php',
                extraParams: {
                    module: 'resultQuiz',
                    type : 'result'
                },
                reader : {
                    type : 'json',
                    root : 'data'
                }
            }
        }, cfg)]);
    }
});
