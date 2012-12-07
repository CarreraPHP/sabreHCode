Ext.define('MyApp.view.quiz.form.NewQuestionForm', {
    extend : 'Ext.window.Window',
    alias : 'widget.newquestionform',
    title : 'New question',
    flex : 1,
    bodyPadding : 5,
    layout: 'fit',
    autoShow: true,
    initComponent : function() {
        var me = this;

        Ext.applyIf(me, {
            items:[{
                xtype: 'form',
                items: [{
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
                        
                }/*,{
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
                }*/]
            }],
            dockedItems : [{
                xtype : 'toolbar',
                dock : 'bottom',
                layout : {
                    pack : 'end'
                },
                items : [{
                    xtype : 'button',
                    itemId : 'Add-Quiz-Question',
                    text : lang.submit

                }, {
                    xtype : 'button',
                    text : lang.cancel,
                    type : 'reset',
                    handler : function(me, e, opts){
                        me.up('window').down('form').getForm().reset();
                        me.up('window').hide();
                    }
                }]
            }]
        });

        me.callParent(arguments);
    }
});