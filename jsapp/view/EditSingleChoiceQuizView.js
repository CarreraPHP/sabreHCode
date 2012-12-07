/*
 * File: jsapp/view/ui/CategoryTreeView.js
 * something
 */
Ext.define('MyApp.view.EditSingleChoiceQuizView', {
    extend : 'Ext.container.Container',
    alias : 'widget.editsinglechoicequizview',
    requires: [
    'MyApp.view.quiz.form.SingleChoiceView',
    'MyApp.view.quiz.form.MultipleChoiceView'
    ],
    autoScroll : true,
    border : false,
    frame : false,
    SelectedData : {},
    data : {},
    layout : {
        type : 'hbox',
        align : 'stretch'
    },
    initComponent : function() {
        var me = this;

        Ext.applyIf(me, {
            items : [{
                xtype : 'grid',
                title : 'List of question',
                itemId : 'editQuizGrid',
                store : Ext.create('MyApp.store.singleQuizStore'),
                tbar:{
                    items: ['->', {
                        text: 'add',
                        iconCls: 'add-button',
                        action: 'add-new-question'
                    }]
                },								
                width : 300,
                columns : [{
                    xtype : 'templatecolumn',
                    tpl : '<span class="{cls}">{question}</span>',
                    header : 'Question',
                    dataIndex : 'question',
                    flex : 1
                }]
            },{
                xtype : 'container',
                layout : 'card',
                activeItem : 0,
                itemId : 'formPanel',
                border: false,
                margin: '0 0 0 3',
                flex : 1,                
                items:[{
                    xtype : 'singlechoicequizformview',
                    itemId : 'addSingleQuiz'
                },{
                    xtype : 'multiplechoicequizformview',
                    itemId : 'addMultipleQuiz'
                }]                
            }]
        });

        me.callParent(arguments);
    }
});
