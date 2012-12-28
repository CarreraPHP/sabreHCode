Ext.define('MyApp.view.quiz.form.MatchView', {
    extend : 'Ext.form.Panel',
    alias : 'widget.matchingquizformview',    
    title : 'Question information',
    flex : 1,
    bodyPadding : 5,
    initComponent : function() {
        var me = this;

        Ext.applyIf(me, {
            items:[{
                xtype : 'combobox',
                itemId : 'quizType',
                fieldLabel: 'Choose Quiz Type',
                labelWidth : 100,
                anchor : '100%',
                name : 'quiz_type',
                editable : false,
                store: Ext.create('MyApp.store.quizTypeStore'),
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id'
                        
            },{
                xtype : 'textfield',
                fieldLabel : 'Parent Question',
                labelWidth : 100,
                anchor : '100%',
                name : 'p_question',
                allowBlank : false
            },{
                xtype : 'fieldcontainer',
                layout : 'hbox',
                flex: 1,
                items : [{
                    xtype : 'textfield',
                    fieldLabel : 'Question',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'ques1',
                    flex : 1,
                    allowBlank : false  
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'answer1',
                    flex : 1,
                    allowBlank : false 
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Correct Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'c_answer1',
                    flex : 1,
                    allowBlank : false 
                }]
                    
            },{
                xtype : 'fieldcontainer',
                layout : 'hbox',
                flex: 1,
                items : [{
                    xtype : 'textfield',
                    fieldLabel : 'Question',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'ques2',
                    flex : 1,
                    allowBlank : false  
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'answer2',
                    flex : 1,
                    allowBlank : false 
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Correct Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'c_answer2',
                    flex : 1,
                    allowBlank : false 
                }]
            },{
                xtype : 'fieldcontainer',
                layout : 'hbox',
                flex: 1,
                items : [{
                    xtype : 'textfield',
                    fieldLabel : 'Question',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'ques3',
                    flex : 1,
                    allowBlank : false  
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'answer3',
                    flex : 1,
                    allowBlank : false 
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Correct Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'c_answer3',
                    flex : 1,
                    allowBlank : false 
                }]
            },{
                xtype : 'fieldcontainer',
                layout : 'hbox',
                flex: 1,
                items : [{
                    xtype : 'textfield',
                    fieldLabel : 'Question',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'ques4',
                    flex : 1,
                    allowBlank : false  
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'answer4',
                    flex : 1,
                    allowBlank : false 
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Correct Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'c_answer4',
                    flex : 1,
                    allowBlank : false 
                }]
            },{
                xtype : 'fieldcontainer',
                layout : 'hbox',
                flex: 1,
                items : [{
                    xtype : 'textfield',
                    fieldLabel : 'Question',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'ques5',
                    flex : 1,
                    allowBlank : false  
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'answer5',
                    flex : 1,
                    allowBlank : false 
                },{
                    xtype: 'splitter'
                },{
                    xtype : 'textfield',
                    fieldLabel : 'Correct Answer',
                    labelWidth : 100,
                    anchor : '100%',
                    name : 'c_answer5',
                    flex : 1,
                    allowBlank : false 
                }]
            },{
                xtype : 'hiddenfield',
                name : 'module',
                value : 'addQuiz'
            },{
                xtype : 'hiddenfield',
                name : 'course_id'
            },{
                xtype : 'hiddenfield',
                name : 'topic_id'
            },{
                xtype : 'hiddenfield',
                name : 'content_id'
            },{
                xtype : 'hiddenfield',
                name : 'id',
                value : 'new'
            }],
            dockedItems : [{
                xtype : 'toolbar',
                dock : 'bottom',
                layout : {
                    pack : 'end'
                },
                items : [{
                    xtype : 'button',
                    itemId : 'save-matchQuiz-but',
                    text : lang.submit,
                    formBind : true,
                    disabled : true

                }, {
                    xtype : 'button',
                    text : lang.cancel,
                    type : 'reset',
                    handler : function() {
                        this.up('form').getForm().reset();
                        var appArr = Ext.ComponentQuery.query('workspaceview container[region="west"]');
                        var appViewInstance = appArr[0];
                        appViewInstance.show();
                        var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                        currCon[0].getLayout().setActiveItem('content-panel');
                    }
                }]
            }]
        });

        me.callParent(arguments);
    }
});