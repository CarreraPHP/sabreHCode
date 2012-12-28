/*
 * File: MyApp/controller/wsControl.js
 */
Ext.define('App.controller.wsControl', {
    extend : 'Ext.app.Controller',
    models : ['CatalogTreeModel'],
    stores : ['CatalogTreeStore'],
    views : ['workspaceView', 'DayOneView'],
    refs:[{
        ref : 'panel',
        selector : 'workspaceview #dataPanel'
    },{
			ref: 'iframePage',
			selector: 'workspaceview #dataDisplay'
		}],
    init : function() {

        this.control({
            'abviewport #tree-Panel' : {
                select : this.OnTreeSelect
            },
            'workspaceview #view-source' : {
                toggle : this.borderLayoutSource
            }
            
        });
    },
    OnTreeSelect : function(me, record, index, opts){
        var panel = this.getPanel();
        var iframeUrl = "";
				
				if(record.data.parent.indexOf(" ") != -1){
					iframeUrl += record.data.parent.replace(/ /g, "/") + "/";
				}
				
				if(record.data.name.indexOf(" ") != -1){
					iframeUrl += record.data.name.replace(/ /g, "_") + "/";
				}
				
//				console.log(record.data, iframeUrl);
				
				this.getIframePage().el.dom.src = iframeUrl;
				
        if(record.raw.id == 20){
            panel.getLayout().setActiveItem(0);
            panel.doLayout();
        }else if(record.raw.id == 25){
            panel.getLayout().setActiveItem(1);
            panel.doLayout();
        }
//            childComponent = Ext.create('App.view.DayOneView', {
//                itemId : '#border-layout',
//                title : 'Border Layout',
//                closable : true
//            });
//            parentTab.add(childComponent);
//            parentTab.getLayout().setActiveItem('#border-layout');
//            childComponent.doLayout();
//            parentTab.doLayout();
//        }
        
        
        
    },
    borderLayoutSource : function(me, pressed, opts){
        var panel = this.getPanel();
        if(pressed){
            panel.getLayout().setActiveItem(1);
        }else{
            panel.getLayout().setActiveItem(0);
        }
        
        
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
    }
});

