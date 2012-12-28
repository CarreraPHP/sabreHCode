/*
 * File: jsapp/controller/wsControl.js
 */

Ext.define('MyApp.controller.wsControl', {
    extend : 'Ext.app.Controller',
    models : ['courseModel', 'chartModel', 'categoryModel', 'contentModel', 'contentTypeModel', 'resourceModel', 'courseAccessModel', 'accessTCModel', 'forumModel', 'commentModel', 'singleQuizModel', 'resultChartModel', 'quizTypeModel', 'quizAnsModel', 'quizCorrAnsModel', 'quizResultModel', 'quizSelModel', 'quizCorModel', 'matchQuesModel'],
    stores : ['courseStore', 'chartStore', 'categoryTreeStore', 'contentStore', 'contentTypeStore', 'resourceStore', 'courseAccessStore', 'accessTCStore', 'forumStore', 'commentStore', 'singleQuizStore', 'resultChartStore', 'quizTypeStore', 'quizResultStore', 'matchQuesStore', 'matchAnsStore'],
    views : ['workspaceView', 'CategoryTreeView', 'CKEditor', 'ContentGridView', 'AddTopicView', 'chartView', 'ContentDisplayView', 'advancesearchView', 'HomeScreenView', 'CourseAccessView', 'ContentFullScreenView', 'ContentForumView', 'AddForumTopicView', 'ForumThreadView', 'ReplyForumView', 'ContentQuizView', 'SingleChoiceQuizView', 'SingleChoiceResultView', 'EditSingleChoiceQuizView'],
    mixins:{
        categoryTreeContext: 'MyApp.mixin.context.CategoryTreeMixin',
        moduleLayout: 'MyApp.mixin.ModuleLayout'
    },	
    init : function() {
        this.control({
            'categorytreeview' : {
                // load : this.onTreeLoad,
                select : this.onTreeSelect,
                itemcontextmenu : this.onTreeItemRightClick
            },
            'addcategoryview button[itemId="submit-button"]' : {
                click : this.updateCategory
            },
            'addcourseview button[itemId="submit-button"]' : {
                click : this.updateCourse
            },
            'categorytreeview button[itemId="category-button"]' : {
                click : this.addCategory
            },
            'categorytreeview button[itemId="course-button"]' : {
                click : this.addCourse
            },
			
            'workspaceview tabpanel dataview#allcoursedataview' : {
                select : this.onCourseSelect,
                itemcontextmenu : this.onBookmarkRightClick
            },
            'courseaccessview button[itemId="access-search"]' : {
                click : this.onCourseAccessSearch
            },
			
            'ckeditor' : {
                instanceReady : function(e) {
                    var o = Ext.ComponentQuery.query('ckeditor[editorId="' + e.editor.id + '"]'), comp = o[0];
                    e.editor.resize(comp.getWidth(), comp.getHeight());
                    comp.on('resize', function(c, adjWidth, adjHeight) {
                        c.editor.resize(adjWidth, adjHeight);
                    });
                }
            }
        });
    },

    onCourseAccessSearch : function(me, e, opts) {
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();
        var currCon = Ext.ComponentQuery.query('courseaccessview gridpanel[itemId="access-topic-content"]');
        currCon[0].store.proxy.extraParams.parent = values.course_id
        currCon[0].store.load();

    },

    onTreeLoad : function() {
    // console.log("loading");
    },
    updateCategory : function(me, e, opts) {
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();

        if(values.actual_parent_id != '' && values.actual_parent_id != values.parent_id) {
            Ext.Msg.confirm('Are you sure', 'Do you want to move ' + values.name + ' from ' + values.actual_parent_name + ' to ' + values.parent_name, function(btn) {
                if(btn == 'yes') {
                    this._onAjaxRequest(me, values, form);
                }
            }, this);
        } else {
            this._onAjaxRequest(me, values, form);
        }
    },
    updateCourse : function(me, e, opts) {
        var form = me.up('form').getForm();
        var values = me.up('form').getValues();

        if(values.actual_category_id != '' && values.actual_category_id != values.category_id) {
            Ext.Msg.confirm('Are you sure', 'Do you want to move ' + values.name + ' from ' + values.actual_category_name + ' to ' + values.category_name, function(btn) {
                if(btn == 'yes') {
                    this._onAjaxRequest(me, values, form);
                }
            }, this);
        } else {
            this._onAjaxRequest(me, values, form);
        }
    },
    _onAjaxRequest : function(me, values, form) {
        Ext.Ajax.request({
            url : 'data/SaveForm.php',
            method : 'post',
            params : values,
            scope : me,
            failure : function(response) {
                Ext.create('Ext.ux.window.Notification', {
                    hideDuration : 500,
                    autoHideDelay : 7000,
                    slideInAnimation : 'bounceOut',
                    slideBackAnimation : 'easeIn',
                    cls : 'ux-notification-light',
                    stickOnClick : true,
                    stickWhileHover : true,
                    title : 'Selection',
                    position : 't',
                    spacing : 20,
                    html : lang.failure.addCategory
                }).show();
            },
            success : function(response) {
                var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                currCon[0].getLayout().setActiveItem('content-panel');
                //currCon[0].down('[itemId=ws-add-category]').setTitle(lang.title.addCategory);
                //currCon[0].down('[itemId=ws-add-course]').setTitle(lang.title.addCourse);
                // console.log(currCon[0]);

                var treeInstanceArr = Ext.ComponentQuery.query('categorytreeview');
                var selModel = treeInstanceArr[0].getSelectionModel();
                var node = selModel.getLastSelected();
                node = treeInstanceArr[0].getStore().getRootNode();

                console.log("tree loaded here....");

                treeInstanceArr[0].getStore().load({
                    node : node
                });

                Ext.create('Ext.ux.window.Notification', {
                    hideDuration : 500,
                    autoHideDelay : 7000,
                    slideInAnimation : 'bounceOut',
                    slideBackAnimation : 'easeIn',
                    cls : 'ux-notification-light',
                    stickOnClick : true,
                    stickWhileHover : true,
                    title : 'Category',
                    position : 't',
                    spacing : 20,
                    iconCls : 'ux-notification-icon-information',
                    html : lang.success.addCategory
                }).show();
                me.up('form').getForm().reset();
            }
        });
    },
    addCategory : function(me, e, opts) {
        //console.log("something");
        var recordArr = me.up('treepanel').getSelectionModel().selected.items;
        // console.log(me.up('treepanel').getSelectionModel().selected.items);

        if(recordArr.length > 0) {
            Ext.Array.each(recordArr, function(record) {
                var data = record.data;
                if(record.raw.permission.create == true) {
                    if(!data.leaf) {
                        var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                        if(!currCon[0].down('[itemId=ws-add-category]')) {
                            currCon[0].add(Ext.create('MyApp.view.AddCategoryView', {
                                itemId : 'ws-add-category'
                            }));
                        }

                        var parentField = Ext.ComponentQuery.query('#ws-add-category textfield[name="parent"]');
                        parentField[0].setValue(data.name);
                        parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="parent_id"]');
                        parentField[0].setValue(data.id);
                        parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="id"]');
                        parentField[0].setValue('new');
                        currCon[0].getLayout().setActiveItem('ws-add-category');
                    }
                }

            });
        }

    },
    addCourse : function(me, e, opts) {

        var recordArr = me.up('treepanel').getSelectionModel().selected.items;
        // console.log(me.up('treepanel').getSelectionModel().selected.items);
        if(recordArr.length > 0) {
            Ext.Array.each(recordArr, function(record) {
                var data = record.data;
                if(record.raw.permission.create == true) {

                    if(!data.leaf) {
                        var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                        if(!currCon[0].down('[itemId=ws-add-course]')) {
                            currCon[0].add(Ext.create('MyApp.view.AddCourseView', {
                                itemId : 'ws-add-course'
                            }));
                        }

                        var parentField = Ext.ComponentQuery.query('#ws-add-course textfield[name="category"]');
                        parentField[0].setValue(data.name);
                        parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="category_id"]');
                        parentField[0].setValue(data.id);
                        parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="id"]');
                        parentField[0].setValue('new');
                        currCon[0].getLayout().setActiveItem('ws-add-course');
                    }
                }
            });
        }

    },
	
    onAddCategoryViewPanel : function(view, record, item, index, e, opts) {
        //console.log(record);
        var currObj = record.data;

        if(currObj.name.toLowerCase() != 'content') {
            var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');

            if(!currCon[0].down('[itemId=ws-add-' + currObj.name.toLowerCase() + ']')) {
                currCon[0].add(Ext.create('MyApp.view.Add' + currObj.name + 'View', {
                    itemId : 'ws-add-' + currObj.name.toLowerCase()
                }));
            }
            currCon[0].getLayout().setActiveItem('ws-add-' + currObj.name.toLowerCase());

            var catView = Ext.ComponentQuery.query('workspaceview tabpanel[region=west]');
            catView[0].setActiveTab(0);
        // 0 -> category, 1-> course
        } else {
            if(!contentWindow) {
                var contentWindow = Ext.create('MyApp.view.AddContentView');
            }
            contentWindow.show();
        }

    },
    onCourseSelect : function(me, record, index, opts) {
		
        var parentContainer = (!record.data.leaf) ? Ext.ComponentQuery.query('workspaceview tabpanel') : Ext.ComponentQuery.query('#content-panel');
        if(record.data.leaf) {
            var selObj = parentContainer[0].down(record.data.id + '-tab-panel');
            console.log(selObj);
            if(selObj == null || selObj == undefined) {
                console.log(record);
                if(record.raw.permission.read) {
                    var child = Ext.create('MyApp.view.ContentGridView', {
                        region : 'center',
                        split : false,
                        border : true,
                        itemId : record.data.id + '-grid-panel',
                        store : Ext.create('MyApp.store.contentStore', {
                            storeId : this.itemId + 'store'
                        }),
                        closable : false
                    });
                    var forum = Ext.create('MyApp.view.ContentForumView', {
                        region : 'center',
                        split : false,
                        border : true,
                        itemId : record.data.id + '-forum-panel',
                        store : Ext.create('MyApp.store.forumStore', {
                            storeId : this.itemId + 'store'
                        }),
                        closable : false
                    });

                    child.store.proxy.extraParams.parent = record.data.id;
                    child.store.load();
                    child.store.sort([{
                        property : 'topic_sort',
                        direction : 'ASC'
                    }, {
                        property : 'content_sort',
                        direction : 'ASC'
                    }]);
                    forum.store.proxy.extraParams.parent = record.data.id;
                    forum.store.load();
                    forum.store.sort([{
                        property : 'topic_sort',
                        direction : 'ASC'
                    }, {
                        property : 'content_sort',
                        direction : 'ASC'
                    }]);

                    child.coursePermission = record.raw.permission;
                    parentContainer[0].add(Ext.create('Ext.tab.Panel', {
                        items : [{
                            xtype : 'panel',
                            title : 'content list',
                            layout : {
                                align : 'stretch',
                                type : 'border',
                                padding : 5
                            },
                            flex : 1,
                            items : [child, {
                                xtype : 'panel',
                                title : 'Content Details',
                                region : 'south',
                                autoScroll : true,
                                height : 200,
                                border : true,
                                preventHeader : false,
                                animCollapse : true,
                                collapseMode : 'mini',
                                collapsible : true,
                                collapsed : true,
                                split : true,
                                tpl : ['<div>', '<div class="contDetailsOdd">' + lang.course + ': {course_name} </div>', '<div class="contDetailsEven">' + lang.topic + ': {topicName} </div>', '<div class="contDetailsOdd">' + lang.description + ': <br/> {description} </div>', '<div class="contDetailsEven">' + lang.preview + ': {text} </div>',
                                /*'<div> Preview: <br/> {text} </div>',*/
                                '</div>'],
                                html : 'description'
                            }]
                        }],
                        border : false,
                        itemId : record.data.id + '-tab-panel',
                        title : record.data.name,
                        closable : true
                    }));

                /*parentContainer[0].add(Ext.create('Ext.tab.Panel', {
						 items : [{
						 xtype : 'panel',
						 title : 'Forum',
						 layout : {
						 align : 'stretch',
						 type : 'border',
						 padding : 5
						 },
						 flex : 1,
						 items : [forum, {
						 xtype : 'panel',
						 title : 'Forum Details',
						 region : 'south',
						 autoScroll : true,
						 height : 200,
						 border : true,
						 preventHeader : false,
						 animCollapse : true,
						 collapseMode : 'mini',
						 collapsible : true,
						 collapsed : true,
						 split : true,
						 //tpl : ['<div>', '<div class="contDetailsOdd">' + lang.course + ': {course_name} </div>', '<div class="contDetailsEven">' + lang.topic + ': {topicName} </div>', '<div class="contDetailsOdd">' + lang.description + ': <br/> {description} </div>', '<div class="contDetailsEven">' + lang.preview + ': {text} </div>',
						 //'<div> Preview: <br/> {text} </div>',
						 //'</div>'],
						 html : 'description'
						 }]
						 }],
						 border : false,
						 itemId : record.data.id + 'forum-tab-panel',
						 title : lang.forum,
						 closable : true
						 }));*/
                    selObj = child;
                }
                if(!record.raw.permission.create) {
                    var topButton = Ext.ComponentQuery.query('contentgridview button[itemId=topic-button]');
                    topButton[0].disable(true);
                    var conButton = Ext.ComponentQuery.query('contentgridview button[itemId=content-button]');
                    conButton[0].disable(true);
                }
                if(!record.raw.permission.update) {

                //topEdit[0].disable(true);
                }
                
            }
            parentContainer[0].getLayout().setActiveItem(record.data.id + '-tab-panel');
            parentContainer[0].doLayout();
        }
    },
    onContentSelect : function(me, record, index, opts) {
        console.log(me);
        var parentTab = me.view.up('tabpanel');
        var childComponent;

        if(record.data.type == 'HTML') {
            childComponent = {
                xtype : 'container',
                html : Base64.decode(record.data.text),
                itemId : parentTab.itemId + '-content-' + record.data.id,
                title : record.data.name,
                autoScroll : true,
                padding : 5,
                closable : true
            };
        }

        if(childComponent) {
            parentTab.add(childComponent);
            parentTab.getLayout().setActiveItem(parentTab.itemId + '-content-' + record.data.id);
            parentTab.doLayout();
        }
    },
    onTreeSelect : function(me, record, index, opts) {

        var parentContainer = (!record.data.leaf) ? Ext.ComponentQuery.query('workspaceview tabpanel') : Ext.ComponentQuery.query('#content-panel');

        if(record.data.leaf) {

            var re = /cou([0-9]+)/ig, result = re.exec(record.data.instanceid);

            if(result) {
                var selObj = parentContainer[0].down('#' + record.data.instanceid + '-tab-panel');

                if(selObj == null) {
                    if(record.raw.permission.read) {
                        var child = Ext.create('MyApp.view.ContentGridView', {
                            region : 'center',
                            split : false,
                            border : true,
                            itemId : record.data.instanceid + '-grid-panel',
                            store : Ext.create('MyApp.store.contentStore', {
                                storeId : this.itemId + 'store'
                            }),
                            closable : false
                        });
                        var forum = Ext.create('MyApp.view.ContentForumView', {
                            region : 'center',
                            split : false,
                            border : true,
                            itemId : record.data.instanceid + '-forum-panel',
                            store : Ext.create('MyApp.store.forumStore', {
                                storeId : this.itemId + 'store'
                            }),
                            closable : false
                        });

                        child.store.proxy.extraParams.parent = record.data.instanceid;
                        child.store.load();
                        child.store.sort([{
                            property : 'topic_sort',
                            direction : 'ASC'
                        }, {
                            property : 'content_sort',
                            direction : 'ASC'
                        }]);
                        forum.store.proxy.extraParams.parent = record.data.instanceid;
                        forum.store.load();
                        forum.store.sort([{
                            property : 'topic_sort',
                            direction : 'ASC'
                        }, {
                            property : 'content_sort',
                            direction : 'ASC'
                        }]);

                        child.coursePermission = record.raw.permission;
                        parentContainer[0].add(Ext.create('Ext.tab.Panel', {
                            items : [{
                                xtype : 'panel',
                                title : 'content list',
                                layout : {
                                    align : 'stretch',
                                    type : 'border',
                                    padding : 5
                                },
                                flex : 1,
                                items : [child, {
                                    xtype : 'panel',
                                    title : 'Content Details',
                                    region : 'south',
                                    autoScroll : true,
                                    height : 200,
                                    border : true,
                                    preventHeader : false,
                                    animCollapse : true,
                                    collapseMode : 'mini',
                                    collapsible : true,
                                    collapsed : true,
                                    split : true,
                                    tpl : ['<div>', '<div class="contDetailsOdd">' + lang.course + ': {course_name} </div>', '<div class="contDetailsEven">' + lang.topic + ': {topicName} </div>', '<div class="contDetailsOdd">' + lang.description + ': <br/> {description} </div>', '<div class="contDetailsEven">' + lang.preview + ': {text} </div>',
                                    /*'<div> Preview: <br/> {text} </div>',*/
                                    '</div>'],
                                    html : 'description'
                                }]
                            }],
                            border : false,
                            itemId : record.data.instanceid + '-tab-panel',
                            title : record.data.name,
                            closable : true
                        }));

                    /*parentContainer[0].add(Ext.create('Ext.tab.Panel', {
						 items : [{
						 xtype : 'panel',
						 title : 'Forum',
						 layout : {
						 align : 'stretch',
						 type : 'border',
						 padding : 5
						 },
						 flex : 1,
						 items : [forum, {
						 xtype : 'panel',
						 title : 'Forum Details',
						 region : 'south',
						 autoScroll : true,
						 height : 200,
						 border : true,
						 preventHeader : false,
						 animCollapse : true,
						 collapseMode : 'mini',
						 collapsible : true,
						 collapsed : true,
						 split : true,
						 //tpl : ['<div>', '<div class="contDetailsOdd">' + lang.course + ': {course_name} </div>', '<div class="contDetailsEven">' + lang.topic + ': {topicName} </div>', '<div class="contDetailsOdd">' + lang.description + ': <br/> {description} </div>', '<div class="contDetailsEven">' + lang.preview + ': {text} </div>',
						 //'<div> Preview: <br/> {text} </div>',
						 //'</div>'],
						 html : 'description'
						 }]
						 }],
						 border : false,
						 itemId : record.data.instanceid + 'forum-tab-panel',
						 title : lang.forum,
						 closable : true
						 }));*/
                    }
                    if(!record.raw.permission.create) {
                        var topButton = Ext.ComponentQuery.query('contentgridview button[itemId=topic-button]');
                        topButton[0].disable(true);
                        var conButton = Ext.ComponentQuery.query('contentgridview button[itemId=content-button]');
                        conButton[0].disable(true);
                    }
                    if(!record.raw.permission.update) {

                    //topEdit[0].disable(true);
                    }
                    selObj = child;
                }
                parentContainer[0].getLayout().setActiveItem(record.data.instanceid + '-tab-panel');

                //pane.getLayout().setActiveItem(record.data.ref.split(',')[0]);

                //var selectedCard = parentContainer[0].down('#' + record.data.instanceid + '-tab-panel');
                //selectedCard.mixins.moduleLayout.makeInitialLayout(selectedCard, record.data.instanceid + '-tab-panel');

                var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                currCon[0].getLayout().setActiveItem('content-panel');

                parentContainer[0].doLayout();
                selObj.doLayout();
            } else {
                this.mixins.moduleLayout.createNotification(lang.selection, lang.failure.courseselection, 'error', 't');
            }
        } else {
            var data = record.data;
            var parentField;
            if(record.raw.permission.create) {
                var button = Ext.ComponentQuery.query('categorytreeview button[itemId=category-button]');
                button[0].enable(true);
                var couButton = Ext.ComponentQuery.query('categorytreeview button[itemId=course-button]');
                couButton[0].enable(true);
                if(Ext.ComponentQuery.query('#ws-add-category').length > 0) {
                    parentField = Ext.ComponentQuery.query('#ws-add-category textfield[name="parent"]');
                    parentField[0].setValue(data.name);
                    parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="parent_id"]');
                    parentField[0].setValue(data.id);
                    parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="parent_name"]');
                    parentField[0].setValue(data.name);
                }
                if(Ext.ComponentQuery.query('#ws-add-course').length > 0) {
                    parentField = Ext.ComponentQuery.query('#ws-add-course textfield[name="category"]');
                    parentField[0].setValue(data.name);
                    parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="category_id"]');
                    parentField[0].setValue(data.id);
                    parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="category_name"]');
                    parentField[0].setValue(data.name);
                }
            } else {
                var catButton = Ext.ComponentQuery.query('categorytreeview button[itemId=category-button]');
                catButton[0].disable(true);
                var couButton = Ext.ComponentQuery.query('categorytreeview button[itemId=course-button]');
                couButton[0].disable(true);
            //Ext.MessageBox.confirm('Confirm', 'You dont have permission to add category');
            }

        }
    },
	
    onTreeItemRightClick : function(me, record, item, index, e, opts) {
        var ctrl = this;
        var cScope = ctrl.mixins.moduleLayout || {};
        cScope.record = record;
        e.preventDefault();
        x = e.browserEvent.clientX;
        y = e.browserEvent.clientY;
        e.stopEvent();
        var menu = Ext.create('Ext.menu.Menu', {
            items : []
        });
        if(record.raw.permission.update) {
            menu.add({
                text : lang.edit,
                iconCls : 'edit-button',
                itemId : 'edit-but',
                scope : this,
                handler : Ext.bind(ctrl.mixins.categoryTreeContext.editInstance, cScope)				
            });
        }
        if(record.raw.permission['delete']) {
            menu.add({
                text : lang.deleteLabel,
                iconCls : 'delete-button',
                scope : this,
                handler : Ext.bind(ctrl.mixins.categoryTreeContext.deleteInstance, cScope)				
            });
        }
        menu.add({
            xtype:'menuseparator'
        });
        menu.add({
            text : lang.addToBookmark,
            iconCls : 'bookmark-button',
            itemId : 'edit-bookmark',
            scope : this,
            handler: Ext.bind(ctrl.mixins.categoryTreeContext.addBookmark, cScope)
        });
        if(record.raw.permission.permission) {
            menu.add({
                text : lang.permission,
                iconCls : 'edit-button',
                itemId : 'edit-permission',
                scope : this,
                handler : Ext.bind(ctrl.mixins.categoryTreeContext.editPermission, cScope)
            });
        }
        menu.showAt(e.xy);
    },
    onBookmarkRightClick : function(me, record, item, index, e, opts){
        var ctrl = this;
        var cScope = ctrl.mixins.moduleLayout || {};
        cScope.record = record;
        e.preventDefault();
        x = e.browserEvent.clientX;
        y = e.browserEvent.clientY;
        e.stopEvent();
        var menu = Ext.create('Ext.menu.Menu', {
            items : []
        });
        menu.add({
            text : 'Remove Bookmark',
            iconCls : 'delete-button',
            itemId : 'delete-bookmark',
            scope : this,
            handler: Ext.bind(ctrl.mixins.categoryTreeContext.removeBookmark, cScope)
        });
        menu.showAt(e.xy);
    },
    onTreePanelClick : function(view, record, item, index, e, opts) {
        //console.log("Course Clicked");

        e.preventDefault();
        var parentContainer = (!record.data.leaf) ? Ext.ComponentQuery.query('workspaceview tabpanel') : Ext.ComponentQuery.query('#content-panel');

        var child = Ext.create('MyApp.view.ContentGridView', {
            title : 'content list',
            itemId : record.data.instanceid + '-grid-panel',
            store : Ext.create('MyApp.store.contentStore', {
                storeId : this.itemId + 'store'
            }),
            border : false,
            closable : false
        });
        child.store.proxy.extraParams.parent = record.data.instanceid;
        child.store.load();

        if(parentContainer[0].add(Ext.create('Ext.tab.Panel', {
            frame : true,
            padding : 2,
            xtype : 'tabpanel',
            border : false,
            itemId : record.data.instanceid + '-tab-panel',
            items : [child],
            title : record.data.name,
            closable : true
        }))) {
            parentContainer[0].getLayout().setActiveItem(record.data.instanceid + '-tab-panel');
        }
        parentContainer[0].doLayout();
    }
});