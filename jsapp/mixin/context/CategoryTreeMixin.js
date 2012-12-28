Ext.define('MyApp.mixin.context.CategoryTreeMixin', {
    addBookmark: function(){
        var record = this.record || {};
        var appArr, appViewInstance;
        appArr = Ext.ComponentQuery.query('workspaceview #allcoursedataview');
        appViewInstance = appArr[0];
        Ext.Ajax.request({
            url : 'data/DataList.php',
            method : 'get',
            params : {
                "course_id" : record.data.id,
                "module" : "makeBookmark"
            },
            scope : this,
            failure : function(response) {
                this.createNotification("Selection", lang.failure.deleteCategory);
            },
            success : function(response) {
                var res = Ext.decode(response.responseText);
                this.createNotification("Status", res.message);
                appViewInstance.store.load();
            }
        });
    },
    removeBookmark : function(){
        var record = this.record || {};
        var appArr, appViewInstance;
        appArr = Ext.ComponentQuery.query('workspaceview #allcoursedataview');
        appViewInstance = appArr[0];
        Ext.Ajax.request({
            url : 'data/DataList.php',
            method : 'get',
            params : {
                "course_id" : record.data.id,
                "module" : "removeBookmark"
            },
            scope : this,
            failure : function(response) {
                this.createNotification("Selection", lang.failure.deleteCategory);
            },
            success : function(response) {
                var res = Ext.decode(response.responseText);
                this.createNotification("Status", res.message);
                appViewInstance.store.load();
            }
        });
    },
    editPermission: function() {
        var record = this.record || {};
        var appArr, appViewInstance;
        if(record.data.leaf) {
            appArr = Ext.ComponentQuery.query('workspaceview container[region="center"]');
            appViewInstance = appArr[0];
            appViewInstance.getLayout().setActiveItem('course-access-view');
            appArr = Ext.ComponentQuery.query('courseaccessview form');
            appViewInstance = appArr[0];
            appViewInstance.getForm().reset();
            appArr = Ext.ComponentQuery.query('courseaccessview hidden[name="course_id"]');
            appViewInstance = appArr[0];
            appViewInstance.setValue(record.data.instanceid);
            appArr = Ext.ComponentQuery.query('courseaccessview combo[itemId="search-course"]');
            appViewInstance = appArr[0];
            appViewInstance.store.proxy.extraParams.parent = record.data.instanceid;
            appViewInstance.store.load();
        } else {
            appArr = Ext.ComponentQuery.query('abviewport container[region="center"]');
            appViewInstance = appArr[0];
            appViewInstance.getLayout().setActiveItem('useraccess-view-card');
        }				
    },
    editInstance: function() {
        var record = this.record || {}, data = record.data;
        var currCon;
        var parentField;

        if(record.data.leaf) {
            currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
            if(!currCon[0].down('[itemId=ws-add-course]')) {
                currCon[0].add(Ext.create('MyApp.view.AddCourseView', {
                    itemId : 'ws-add-course'
                }));
            }
            currCon[0].down('[itemId=ws-add-course]').setTitle('Edit - Course');
            parentField = Ext.ComponentQuery.query('#ws-add-course textfield[name="category"]');
            parentField[0].setValue(data.parent_name);
            parentField = Ext.ComponentQuery.query('#ws-add-course textfield[name="name"]');
            parentField[0].setValue(data.name);
            parentField = Ext.ComponentQuery.query('#ws-add-course ckeditor[name="description"]');
            parentField[0].setValue(data.description);
            parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="category_id"]');
            parentField[0].setValue(data.parent_id);
            parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="actual_category_id"]');
            parentField[0].setValue(data.parent_id);
            parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="actual_category_name"]');
            parentField[0].setValue(data.parent_name);
            parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="category_name"]');
            parentField[0].setValue(data.parent_name);
            parentField = Ext.ComponentQuery.query('#ws-add-course hidden[name="id"]');
            parentField[0].setValue(data.instanceid);
            currCon[0].getLayout().setActiveItem('ws-add-course');
            this.createNotification("right click", "course " + record.get('name'), 'information', 't');
        } else {
            if(!data.leaf) {
                currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                if(!currCon[0].down('[itemId=ws-add-category]')) {
                    currCon[0].add(Ext.create('MyApp.view.AddCategoryView', {
                        itemId : 'ws-add-category'
                    }));
                }
                currCon[0].down('[itemId=ws-add-category]').setTitle('Edit - Category');
                parentField = Ext.ComponentQuery.query('#ws-add-category textfield[name="parent"]');
                parentField[0].setValue(data.parent_name);
                parentField = Ext.ComponentQuery.query('#ws-add-category textfield[name="name"]');
                parentField[0].setValue(data.name);
                parentField = Ext.ComponentQuery.query('#ws-add-category ckeditor[name="description"]');
                parentField[0].setValue(data.description);
                parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="parent_id"]');
                parentField[0].setValue(data.parent_id);
                parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="actual_parent_id"]');
                parentField[0].setValue(data.parent_id);
                parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="actual_parent_name"]');
                parentField[0].setValue(data.parent_name);
                parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="parent_name"]');
                parentField[0].setValue(data.parent_name);
                parentField = Ext.ComponentQuery.query('#ws-add-category hidden[name="id"]');
                parentField[0].setValue(data.instanceid);
                currCon[0].getLayout().setActiveItem('ws-add-category');
            }

            this.createNotification("right click", "category " + record.get('name'), 'information', 't');
        }

    },
    deleteInstance: function() {
        var record = this.record || {};
        if(record.data.leaf) {
            Ext.Ajax.request({
                url : 'data/Delete.php',
                method : 'post',
                params : {
                    "id" : record.data.id,
                    "module" : "course"
                },
                scope : this,
                failure : function(response) {
                    this.createNotification("Selection", lang.failure.deleteCategory);
                },
                success : function(response) {
                    window.msg = Ext.decode(response.responseText);
                    if(window.msg.success == true) {
                        var treeInstanceArr = Ext.ComponentQuery.query('categorytreeview');
                        var selModel = treeInstanceArr[0].getSelectionModel();
                        var node = selModel.getLastSelected();
                        node = treeInstanceArr[0].getStore().getRootNode();

                        console.log("tree store loaded here...");

                        treeInstanceArr[0].getStore().load({
                            node : node
                        });
                        var currCon = Ext.ComponentQuery.query('workspaceview container[region=center]');
                        var contentPanel = currCon[0].down('[itemId="content-panel"]');
                        var courseTabPanel = contentPanel.down('#' + record.data.instanceid + '-tab-panel');
                        if(courseTabPanel) {
                            courseTabPanel.close();
                        }
                        this.createNotification("Success", record.data.name + " successfully deleted", 'information', 't');
                    }
                }
            });

        } else {
            //record.data.id
            Ext.Ajax.request({
                url : 'data/Delete.php',
                method : 'post',
                params : {
                    "id" : record.data.id,
                    "module" : "category"
                },
                scope : this,
                failure : function(response) {
                    this.createNotification("Selection", lang.failure.deleteCategory);					
                },
                success : function(response) {
                    window.msg = Ext.decode(response.responseText);
                    if(window.msg.success == false) {
                        this.createNotification("Error", "Empty category alone can be deleted", 'error', 't');
                    } else {
                        var treeInstanceArr = Ext.ComponentQuery.query('categorytreeview');
                        //var selModel = treeInstanceArr[0].getSelectionModel();
                        //var node = selModel.getLastSelected();
                        var node = treeInstanceArr[0].getStore().getRootNode();
                        treeInstanceArr[0].getStore().load({
                            node : node
                        });
                        this.createNotification("Success", record.data.name + " successfully deleted", 'information', 't');
                    }
                }
            });
        }
    }
});