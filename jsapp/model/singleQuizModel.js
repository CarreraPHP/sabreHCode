
Ext.define('MyApp.model.singleQuizModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'question',
        type : 'string',
        mapping : 'name'
    }, {
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
    }],
    hasMany: {
        model: 'MyApp.model.quizAnsModel', 
        name: 'ans'
    }
});
