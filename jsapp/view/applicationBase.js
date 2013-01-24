Ext.define('MyApp.view.applicationBase', {
	extend : 'Ext.container.Viewport',
	alias : 'widget.abviewport',
	mixins : {
		moduleLayout : 'MyApp.mixin.ModuleLayout'
	},
	requires : ['MyApp.view.workspaceView', 'MyApp.view.loginView', 'MyApp.view.advancesearchView', 'MyApp.view.UserAccessView'],
	padding : 5,
	layout : {
		type : 'border'
	},

	initComponent : function() {
		var me = this;

		Ext.applyIf(me, {
			items : [{
				xtype : 'container',
				itemId : 'application-base',
				activeItem : 'login-view-card',
				layout : {
					type : 'card'
				},
				region : 'center',
				split : true,
				items : [{
					xtype : 'workspaceview',
					itemId : 'ws-view-card',
					width : 150
				}, {
					xtype : 'loginview',
					itemId : 'login-view-card'
				}, {
					xtype : 'advancesearchview',
					itemId : 'advancesearch-view-card'
				}, {
					xtype : 'useraccessview',
					itemId : 'useraccess-view-card'
				}]
			}, {
				xtype : 'toolbar',
				frame : true,
				height : 30,
				collapseMode : 'mini',
				region : 'north',
				border : true,
				split : true,
				items : [{
					xtype : 'container',
					html : '<h1>' + lang.application.title + '</h1>'
				}, '->', {
					width : 250,
					xtype : 'combo',
					emptyText : lang.searchcourse,
					itemId : 'search-combo',
					store : 'searchStore',
					displayField : 'title',
					multiSelect : false,
					typeAhead : false,
					hideLabel : true,
					hideTrigger : true,
					minChars : 2,
					anchor : '100%',
					listConfig : {
						loadingText : 'Searching...',
						emptyText : 'No matching posts found.',

						// Custom rendering template for each item
						getInnerTpl : function() {
							return '<h3>{name}</h3>' + '{description}';
						}
					},
					pageSize : 10
				}, {
					xtype : 'tbseparator'
				}, {
					width : 80,
					xtype : 'combo',
					id : 'language',
					emptyText : lang.language,
					store : new Ext.data.SimpleStore({
						fields : ['id', 'name'],
						data : [["eng", "english"], ["tam", "தமிழ்"], ["deu", "deu"]]
					}),
					displayField : 'name',
					valueField : 'id',
					listeners : {
						select : function(combo, record) {
							categorySelectedId = this.getSubmitValue();
                                                        console.log(categorySelectedId);
							window.location = "app.php?lang=" + categorySelectedId;
						}
					}
				}, {
					xtype : 'tbseparator'
				}, {
					xtype : 'buttongroup',
					columns : 3,
					itemId : 'Group-Button',
					frame : false,
					items : [{
						xtype : 'button',
						text : lang.advancedsearch,
						iconCls : 'advanced-search',
						itemId : 'advancedSearch',
						enableToggle : true,
						toggleGroup : 'Group-Button'
					}, {
						xtype : 'tbseparator'
					}, {
						xtype : 'button',
						text : lang.useraccess,
						itemId : 'userAccess',
						iconCls : 'user-access',
						enableToggle : true,
						toggleGroup : 'Group-Button'
					}]
				}, {
					xtype : 'tbseparator'
				}, {
					xtype : 'container',
					itemId : 'login-detail-label',
					data : {
						screenname : lang.guest
					},
					frame : true,
					tpl : ['<span>{screenname}</span>']
				}, {
					xtype : 'tbseparator'
				}, {
					xtype : 'button',
					text : lang.logout,
					itemId : 'logout',
					iconCls : 'logout-button'
				}]
			}]
		});

		me.callParent(arguments);
	}
});
/*{
 xtype: 'dataview',
 frame: true,
 maxHeight: 30,
 minHeight: 0,
 singleSelect: true,
 overItemCls: 'x-view-over',
 itemSelector: 'div.thumb-wrap',
 tpl: [
 '<tpl for=".">',
 '<div class="thumb-wrap">',
 '<div class="thumb">',
 '<span class="add-button">{name}</span>',
 '</div>',
 '</div>',
 '</tpl>'
 ],
 height: 30,
 collapseMode: 'mini',
 region: 'north',
 split: true,
 store: Ext.create('MyApp.store.menuStore')
 }*/
