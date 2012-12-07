
Ext.define('MyApp.model.quizCorrAnsModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'answer',
        type : 'string',
        mapping : 'ans_name'
    },{
        name : 'ans_id',
        type : 'string'
    },{
        name : 'ques_id',
        type : 'int'
    },{
        name : 'quiz_id',
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
        name: 'result',
        type : 'string'
    }],
    belongsTo: 'MyApp.model.quizAnsModel'
});
