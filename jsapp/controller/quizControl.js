/*
 * File: jsapp/controller/abControl.js
 * 'userAccessModel',
 * 'userAccessStore',
 */

Ext.define('MyApp.controller.quizControl', {
    extend : 'Ext.app.Controller',

    models : [],
    stores : [],
    views : ['SingleChoiceQuizView', 'quiz.form.NewQuestionForm'],
    requires: ['MyApp.view.quiz.form.NewQuestionForm'],
    refs : [{
        ref : 'squiz',
        selector : 'singlechoicequizview'
    },{
        ref : 'newQuestionWindow',
        selector : 'newquestionform',
        autoCreate: true,
        xtype: 'newquestionform'
    }],
    /*views : ['UserAccessView'],*/
    init : function() {
        this.control({
            'singlechoiceview' : {
                render : this.onLoadQuizView
            },
            'singlechoiceview button[itemId="Leave-Quiz"]' : {
                click : this.onLeaveQuiz
            },
            'contentquizview button[iconCls="back-button"]' : {
                click : this.onLeaveQuiz
            },
            'singlechoiceview button[itemId="next-button"]' : {
                click : this.onNextView
            },
            'singlechoiceview button[itemId="prev-button"]' : {
                click : this.onPrevView
            },
            'editsinglechoicequizview #editQuizGrid' :{
                select : this.onQuizGridSelect
            },
            'editsinglechoicequizview #addAnswerField' :{
                click : this.onAddAnswerField
            },
            'editsinglechoicequizview #save-singleQuiz-but' :{
                click : this.onAddSingleQuiz
            },
            'editsinglechoicequizview #save-multiQuiz-but' :{
                click : this.onAddMultipleQuiz
            },
            'editsinglechoicequizview #save-matchQuiz-but' :{
                click : this.onAddMatchQuiz
            },
            'editsinglechoicequizview #quizType' : {
                change : this.onChangeQuizType
            },
            'editsinglechoicequizview [action=add-new-question]' : {
                click: this.promptNewQuestionForm
            },
            'newquestionform #Add-Quiz-Question' : {
                click : this.onAddQuestion
            }

        });
    },
    onLoadQuizView : function(me, opts){
        //console.log(me);
        var b = Ext.ComponentQuery.query('contentquizview #prev-button'), button = b[0];
        button.hide();
    },
    onAddQuestion : function(me, e, opts){
        
        var form = Ext.ComponentQuery.query('editsinglechoicequizview #formPanel')[0];
        var item = form.getLayout().getActiveItem();
        item.getForm().reset();
        var values = me.up('window').down('form').getValues();
        var f = Ext.ComponentQuery.query('editsinglechoicequizview #formPanel'), form = f[0];
        var combo = Ext.ComponentQuery.query('singlechoicequizformview #quizType')[0];
        if(values.quiz_type == 4){
            form.getLayout().setActiveItem(0);
            combo.setValue(values.quiz_type);
        }else if(values.quiz_type == 3){
            form.getLayout().setActiveItem(1);
            combo.setValue(values.quiz_type);
        }else if(values.quiz_type == 2){
            form.getLayout().setActiveItem(2);
            combo.setValue(values.quiz_type);
        }
        me.up('window').hide();
        var grid = Ext.ComponentQuery.query('editsinglechoicequizview #editQuizGrid')[0];
        
        grid.getSelectionModel().select(-1);
        
    },
    onChangeQuizType : function(me, newValue, oldValue, eOpts){
        
        var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
        var quiz = parent.down('#edit-quiz').down('#formPanel');
        var quizContent = Ext.ComponentQuery.query('contentquizview');
        var childComponent = quizContent[0];
        var rec = childComponent.SelectedRecord;
        var record =rec.data;
        if(newValue == 3){
            var hiddenField = quiz.down('#addMultipleQuiz').down('hidden[name="course_id"]');
            hiddenField.setValue(record.course_id);
            hiddenField = quiz.down('#addMultipleQuiz').down('hidden[name="topic_id"]');
            hiddenField.setValue(record.topic_id);
            hiddenField = quiz.down('#addMultipleQuiz').down('hidden[name="content_id"]');
            hiddenField.setValue(record.id);
            quiz.down('#addMultipleQuiz').down('combobox[name="quiz_type"]').setValue(newValue);
            quiz.getLayout().setActiveItem(1);  
        }else if(newValue == 4){
            var hiddenField = quiz.down('#addSingleQuiz').down('hidden[name="course_id"]');
            hiddenField.setValue(record.course_id);
            hiddenField = quiz.down('#addSingleQuiz').down('hidden[name="topic_id"]');
            hiddenField.setValue(record.topic_id);
            hiddenField = quiz.down('#addSingleQuiz').down('hidden[name="content_id"]');
            hiddenField.setValue(record.id);
            quiz.down('#addSingleQuiz').down('combobox[name="quiz_type"]').setValue(newValue);
            quiz.getLayout().setActiveItem(0);  
        }else if(newValue == 2){
            var hiddenField = quiz.down('#addMatchingQuiz').down('hidden[name="course_id"]');
            hiddenField.setValue(record.course_id);
            hiddenField = quiz.down('#addMatchingQuiz').down('hidden[name="topic_id"]');
            hiddenField.setValue(record.topic_id);
            hiddenField = quiz.down('#addMatchingQuiz').down('hidden[name="content_id"]');
            hiddenField.setValue(record.id);
            quiz.down('#addMatchingQuiz').down('combobox[name="quiz_type"]').setValue(newValue);
            quiz.getLayout().setActiveItem(2);  
        }
    },
    onQuizGridSelect : function(me, record, index, opts){
        var data = record.raw;
        var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
        var quiz = parent.down('#edit-quiz').down('#formPanel');
        if(data.quiz_id == '4'){
            var o = Ext.ComponentQuery.query('contentquizview #addSingleQuiz'), form = o[0];
            form.getForm().reset();
            form.down('combobox[name="quiz_type"]').setValue(data.quiz_id);
            form.down('textfield[name="question"]').setValue(data.name);
            form.down('textfield[name="answer1"]').setValue(data.ans[0].ans_name);
            form.down('textfield[name="answer2"]').setValue(data.ans[1].ans_name);
            form.down('textfield[name="answer3"]').setValue(data.ans[2].ans_name);
            form.down('textfield[name="answer4"]').setValue(data.ans[3].ans_name);
            form.down('hiddenfield[name="id"]').setValue(data.ques_id);
            if(data.ans[0].ans_id == data.correct[0].ans_id){
                form.down('radiofield[itemId="ans1"]').setValue(true);
            }
            if(data.ans[1].ans_id == data.correct[0].ans_id){
                form.down('radiofield[itemId="ans2"]').setValue(true);
            }
            if(data.ans[2].ans_id == data.correct[0].ans_id){
                form.down('radiofield[itemId="ans3"]').setValue(true);
            }
            if(data.ans[3].ans_id == data.correct[0].ans_id){
                form.down('radiofield[itemId="ans4"]').setValue(true);
            }
        }else if(data.quiz_id == '3'){
            var o = Ext.ComponentQuery.query('contentquizview #addMultipleQuiz'), form = o[0];
            form.getForm().reset();
            form.down('combobox[name="quiz_type"]').setValue(data.quiz_id);
            form.down('textfield[name="question"]').setValue(data.name);
            form.down('textfield[name="answer1"]').setValue(data.ans[0].ans_name);
            form.down('textfield[name="answer2"]').setValue(data.ans[1].ans_name);
            form.down('textfield[name="answer3"]').setValue(data.ans[2].ans_name);
            form.down('textfield[name="answer4"]').setValue(data.ans[3].ans_name);
            form.down('hiddenfield[name="id"]').setValue(data.ques_id);
            for(var i = 0; i < data.ans.length; i++){
                for(var j = 0; j < data.correct.length; j++){
                    if(data.ans[i].ans_id == data.correct[j].ans_id){
                        form.down('checkboxfield[id="ans'+(i+1)+'"]').setValue(true);
                    }
                }
            }
            quiz.getLayout().setActiveItem(1);
        }else if(data.quiz_id == '2'){
            var o = Ext.ComponentQuery.query('contentquizview #addMatchingQuiz'), form = o[0];
            form.getForm().reset();
            form.down('combobox[name="quiz_type"]').setValue(data.quiz_id);
            form.down('textfield[name="p_question"]').setValue(data.name);
            form.down('textfield[name="ques1"]').setValue(data.ques_name[0].ques_name);
            form.down('textfield[name="answer1"]').setValue(data.ans_name[0].ans_name);
            form.down('textfield[name="c_answer1"]').setValue(data.cor_ans[0].cor_ans);
            form.down('textfield[name="ques2"]').setValue(data.ques_name[1].ques_name);
            form.down('textfield[name="answer2"]').setValue(data.ans_name[1].ans_name);
            form.down('textfield[name="c_answer2"]').setValue(data.cor_ans[1].cor_ans);
            form.down('textfield[name="ques3"]').setValue(data.ques_name[2].ques_name);
            form.down('textfield[name="answer3"]').setValue(data.ans_name[2].ans_name);
            form.down('textfield[name="c_answer3"]').setValue(data.cor_ans[2].cor_ans);
            form.down('textfield[name="ques4"]').setValue(data.ques_name[3].ques_name);
            form.down('textfield[name="answer4"]').setValue(data.ans_name[3].ans_name);
            form.down('textfield[name="c_answer4"]').setValue(data.cor_ans[3].cor_ans);
            form.down('textfield[name="ques5"]').setValue(data.ques_name[4].ques_name);
            form.down('textfield[name="answer5"]').setValue(data.ans_name[4].ans_name);
            form.down('textfield[name="c_answer5"]').setValue(data.cor_ans[4].cor_ans);
            form.down('hiddenfield[name="id"]').setValue(data.p_id);
            quiz.getLayout().setActiveItem(2);
            
        }
    },
    onAddAnswerField : function(me, e, opts){
        var form = me.up('form');
        var count = form.items.length;
        count = count-1;
        var textField = Ext.create('Ext.form.field.Text', {
            fieldLabel : 'Answer '+count ,
            labelWidth : 70,
            anchor : '100%',
            name : 'answer'+count,
            allowBlank : false
        });
        form.add(textField);
    },
    onAddSingleQuiz : function(me, e, opts){
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();
        if(form.isValid()){
            Ext.Ajax.request({
                url : 'data/QuizData.php',
                method : 'post',
                type : 'json',
                params : values,
                scope : this,
                failure : function(response) {
                    this.createNotification(lang.logout, lang.failure.logout, 'error', 't');
                },
                success : function(response) {
                    var res = Ext.decode(response.responseText);
                    var o = Ext.ComponentQuery.query('contentquizview #editQuizGrid'), grid = o[0];
                    grid.store.proxy.extraParams.course_id = values.course_id;
                    grid.store.proxy.extraParams.topic_id = values.topic_id;
                    grid.store.proxy.extraParams.content_id = values.content_id;
                    grid.store.load();
                    me.up('form').getForm().reset();
                    var form = me.up('form');
                    form.setTitle('Add Quiz');
                    form.down('hiddenfield[name="id"]').setValue('new');
                    form.down('hiddenfield[name="course_id"]').setValue(values.course_id);
                    form.down('hiddenfield[name="topic_id"]').setValue(values.topic_id);
                    form.down('hiddenfield[name="content_id"]').setValue(values.content_id);
                    
                }
            });
        }
    },
    onAddMultipleQuiz : function(me, e, opts){
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();
        if(values.CorrectAns.length > 1 && values.CorrectAns.length < 3){
            values.CorrectAns1 = values.CorrectAns[0];
            values.CorrectAns2 = values.CorrectAns[1];
        }else if(values.CorrectAns.length > 2 &&  values.CorrectAns.length < 4){
            values.CorrectAns1 = values.CorrectAns[0];
            values.CorrectAns2 = values.CorrectAns[1];
            values.CorrectAns3 = values.CorrectAns[2];
        }else if(values.CorrectAns.length > 3){
            values.CorrectAns1 = values.CorrectAns[0];
            values.CorrectAns2 = values.CorrectAns[1];
            values.CorrectAns3 = values.CorrectAns[2];
            values.CorrectAns4 = values.CorrectAns[3];
        }else{
            values.CorrectAns1 = values.CorrectAns;
        }
        if(form.isValid()){
            Ext.Ajax.request({
                url : 'data/QuizData.php',
                method : 'post',
                type : 'json',
                params : values,
                scope : this,
                failure : function(response) {
                    this.createNotification(lang.logout, lang.failure.logout, 'error', 't');
                },
                success : function(response) {
                    var res = Ext.decode(response.responseText);
                    var o = Ext.ComponentQuery.query('contentquizview #editQuizGrid'), grid = o[0];
                    grid.store.proxy.extraParams.course_id = values.course_id;
                    grid.store.proxy.extraParams.topic_id = values.topic_id;
                    grid.store.proxy.extraParams.content_id = values.content_id;
                    grid.store.load();
                    me.up('form').getForm().reset();
                    var form = me.up('form');
                    form.setTitle('Add Quiz');
                    form.down('hiddenfield[name="id"]').setValue('new');
                    form.down('hiddenfield[name="course_id"]').setValue(values.course_id);
                    form.down('hiddenfield[name="topic_id"]').setValue(values.topic_id);
                    form.down('hiddenfield[name="content_id"]').setValue(values.content_id);
                    
                }
            });
        }
    },
    onAddMatchQuiz : function(me, e, opts){
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();
        if(form.isValid()){
            Ext.Ajax.request({
                url : 'data/QuizData.php',
                method : 'post',
                type : 'json',
                params : values,
                scope : this,
                failure : function(response) {
                    this.createNotification(lang.logout, lang.failure.logout, 'error', 't');
                },
                success : function(response) {
                    var res = Ext.decode(response.responseText);
                    var o = Ext.ComponentQuery.query('contentquizview #editQuizGrid'), grid = o[0];
                    grid.store.proxy.extraParams.course_id = values.course_id;
                    grid.store.proxy.extraParams.topic_id = values.topic_id;
                    grid.store.proxy.extraParams.content_id = values.content_id;
                    grid.store.load();
                    me.up('form').getForm().reset();
                    var form = me.up('form');
                    form.setTitle('Question information');
                    form.down('hiddenfield[name="id"]').setValue('new');
                    form.down('hiddenfield[name="course_id"]').setValue(values.course_id);
                    form.down('hiddenfield[name="topic_id"]').setValue(values.topic_id);
                    form.down('hiddenfield[name="content_id"]').setValue(values.content_id);
                    
                }
            });
        }
    },
    onNextView : function(me, e, opts){
        var values = me.up('form').getValues();
        var form = me.up('form').getForm();
        if(form.isValid()){
            Ext.Ajax.request({
                url : 'data/QuizData.php',
                method : 'post',
                type : 'json',
                params : values,
                scope : this,
                failure : function(response) {
                    this.createNotification(lang.logout, lang.failure.logout, 'error', 't');
                },
                success : function(response) {
                    var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
                    quiz = parent.getLayout().getActiveItem();
                    var indexArr = quiz.itemId.split("-");
                    index = parseInt(indexArr[1]);
                    var count = quiz.data.length;
                    var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
                    if(count > index && count-1 != index){
                        parent.getLayout().setActiveItem('quiz-'+(index+1));
                    }else{
                        me.hide();
                        var quizContent = Ext.ComponentQuery.query('contentquizview'), childComponent = quizContent[0];
                        var data = childComponent.SelectedRecord.data;
                        var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
                        var ResultComponent;
                        //ResultComponent = parent.down('#singleQuizResult');
                        if(!ResultComponent) {
                            ResultComponent = Ext.create('MyApp.view.SingleChoiceResultView', {
                                itemId : 'singleQuizResult'
                            });
                            parent.add(ResultComponent);
                        }
                        ResultComponent.doLayout();
                        var r = Ext.ComponentQuery.query('#resultlist'), result = r[0];
                        result.store.proxy.extraParams.content_id = data.id;
                        result.store.proxy.extraParams.topic_id = data.topic_id;
                        result.store.proxy.extraParams.course_id = data.course_id;
                        result.store.load();
                        result.store.on('load', function(){
                            var o = Ext.ComponentQuery.query("singlechoiceresultview #resultChart"), pane = o[0];
                            pane.store.proxy.extraParams.content_id = data.id;
                            pane.store.proxy.extraParams.topic_id = data.topic_id;
                            pane.store.proxy.extraParams.course_id = data.course_id;
                            pane.store.load();
                        });
                        
                        parent.getLayout().setActiveItem('singleQuizResult');
                    }
                }
            });
        }
        
       
        
    },
    onPrevView : function(me, e, opts){
        var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
        quiz = parent.getLayout().getActiveItem();
        var indexArr = quiz.itemId.split("-");
        index = parseInt(indexArr[1]);
        var count = quiz.data.length;
        var o = Ext.ComponentQuery.query('contentquizview'), parent = o[0];
        if(count > index){
            parent.getLayout().setActiveItem('quiz-'+(index-1));
        }else{
            me.hide();
        }
    },
    onLeaveQuiz : function(me, e, opts) {
        var appArr = Ext.ComponentQuery.query('workspaceview container[region="west"]');
        var appViewInstance = appArr[0];
        appViewInstance.show();
        var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
        currCon[0].getLayout().setActiveItem('content-panel');
    },
    createNotification : function(title, html, type, position) {
        var iconCls = (type == 'error') ? 'ux-notification-icon-error' : 'ux-notification-icon-information';
        Ext.create('Ext.ux.window.Notification', {
            hideDuration : 500,
            autoHideDelay : 7000,
            slideInAnimation : 'bounceOut',
            slideBackAnimation : 'easeIn',
            cls : 'ux-notification-light',
            stickOnClick : true,
            stickWhileHover : true,
            iconCls : iconCls,
            title : title,
            position : position,
            spacing : 20,
            html : html
        }).show();
    },
    promptNewQuestionForm: function(){
        
        var nq = this.getNewQuestionWindow();
        nq.show();
        
        
    }
});
