
Ext.define('MyApp.model.quizResultModel', {
    extend : 'Ext.data.Model',
    fields : [{
        name : 'ques_name',
        type : 'string'
    }, {
        name : 'ques_id',
        type : 'int'
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
        name : 'sel_id',
        type : 'int'
    },{
        name : 'cor_id',
        type : 'int'
    },{
        name : 'icon',
        type : 'string'
    }],
    hasMany: [{
        model: 'MyApp.model.quizSelModel', 
        name: 'sel_ans'
    },{
        model: 'MyApp.model.quizCorModel', 
        name: 'cor_ans'        
    }]
});
