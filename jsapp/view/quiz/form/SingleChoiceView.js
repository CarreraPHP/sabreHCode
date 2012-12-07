Ext.define('MyApp.view.quiz.form.SingleChoiceView', {
	extend : 'Ext.form.Panel',
	alias : 'widget.singlechoicequizformview',    
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
				fieldLabel : 'Question',
				labelWidth : 100,
				anchor : '100%',
				name : 'question',
				allowBlank : false
			},{
				xtype : 'fieldcontainer',
				layout : 'hbox',
				flex: 1,
				items : [{
					xtype : 'textfield',
					fieldLabel : 'Answer 1',
					labelWidth : 100,
					anchor : '100%',
					name : 'answer1',
					flex : 1,
					allowBlank : false  
				},{
					xtype: 'splitter'
				},{
					xtype : 'radiofield',
					inputValue : '1',
					itemId : 'ans1',
					flex : 0.02,
					name : 'CorrectAns'
				}]
                    
			},{
				xtype : 'fieldcontainer',
				layout : 'hbox',
				flex: 1,
				items : [{
					xtype : 'textfield',
					fieldLabel : 'Answer 2',
					labelWidth : 100,
					anchor : '100%',
					name : 'answer2',
					flex : 1,
					allowBlank : false  
				},{
					xtype: 'splitter'
				},{
					xtype : 'radiofield',
					inputValue : '2',
					itemId : 'ans2',
					flex : 0.02,
					name : 'CorrectAns'
				}]
			},{
				xtype : 'fieldcontainer',
				layout : 'hbox',
				flex: 1,
				items : [{
					xtype : 'textfield',
					fieldLabel : 'Answer 3',
					labelWidth : 100,
					anchor : '100%',
					name : 'answer3',
					flex : 1,
					allowBlank : false  
				},{
					xtype: 'splitter'
				},{
					xtype : 'radiofield',
					inputValue : '3',
					itemId : 'ans3',
					flex : 0.02,
					name : 'CorrectAns'
				}]
			},{
				xtype : 'fieldcontainer',
				layout : 'hbox',
				flex: 1,
				items : [{
					xtype : 'textfield',
					fieldLabel : 'Answer 4',
					labelWidth : 100,
					anchor : '100%',
					name : 'answer4',
					flex : 1,
					allowBlank : false  
				},{
					xtype: 'splitter'
				},{
					xtype : 'radiofield',
					inputValue : '4',
					itemId : 'ans4',
					flex : 0.02,
					name : 'CorrectAns'
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
					itemId : 'save-singleQuiz-but',
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