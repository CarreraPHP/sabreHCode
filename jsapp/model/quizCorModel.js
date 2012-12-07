
Ext.define('MyApp.model.quizCorModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'cor_ans',
        type : 'string'
    },{
        name : 'cor_id',
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
        name : 'ques_id',
        type : 'int'
    }],
    belongsTo: 'MyApp.model.quizSelModel'
});
