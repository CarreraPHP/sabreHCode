
Ext.define('MyApp.model.quizSelModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'sel_ans',
        type : 'string'
    },{
        name : 'sel_id',
        type : 'string'
    },{
        name : 'quiz_type',
        type : 'int'
    }, {
        name : 'course_id',
        type : 'int'
    },{
        name : 'topic_id',
        type : 'int'
    },{
        name : 'content_id',
        type : 'int'
    },{
        name : 'icon',
        type : 'string'
    }],    
    belongsTo: 'MyApp.model.quizResultModel'
});
