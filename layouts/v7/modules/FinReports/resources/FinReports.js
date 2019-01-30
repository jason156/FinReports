/* ********************************************************************************
 * The content of this file is subject to the Custom Header/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("FinReports_Js", {
    instance: false,
    runned: false,
    getInstance: function () {
        if (FinReports_Js.instance == false) {
            var instance = new FinReports_Js();
            FinReports_Js.instance = instance;
            return instance;
        }
        return FinReports_Js.instance;
    }
},{
    registerShowOnDetailView:function(){
        var self = this;
        jQuery('#loadFin').on('click', function (e) {
            self.runned = true;
            e.preventDefault();
            self.runRequest();
        });

    },

    runRequest: function() {
        var self = this;
        var params = {};
        params['module'] = 'FinReports';
        params['action'] = 'ActionAjax';
        params['mode'] = 'getReport';
        params['record'] = app.getRecordId();
        params['moduleSelected'] = app.getModuleName();
        params['parent'] = app.getParentModuleName();
        app.helper.showProgress();
        app.request.post({data:params}).then(
            function (err,data) {
                console.log(err, data);
                if(err == null) {
                    app.changeURL(data.value);
                    app.helper.hideProgress();
                    location.reload()
                } else {
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({title: 'Error', message: err.message});
                }
                self.runned = false;

            }
        );

    },

    registerEvents: function(){
        this.registerShowOnDetailView();

    }
});

jQuery(document).ready(function () {

    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    var parent = app.convertUrlToDataParams(window.location.href).relatedModule;
    if(viewName == 'Detail' && parent == 'FinReports'){
        var instance = new FinReports_Js();
        instance.registerEvents();
    }
});

jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    var parent = app.convertUrlToDataParams(window.location.href).relatedModule;
    if(viewName == 'Detail' && parent == 'FinReports'){
        var instance = new FinReports_Js();
        instance.registerEvents();
    }
});