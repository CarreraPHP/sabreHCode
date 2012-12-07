
Ext.define('MyApp.model.matchQuesModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'ques_name',
        type : 'string'
    }, {
        name : 'ques_id',
        type : 'int'
    },{
        name : 'ans_name',
        type : 'string'
    }, {
        name : 'ans_id',
        type : 'int'
    }]
});
