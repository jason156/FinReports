<?php

class Settings_FinReports_Settings_View extends Settings_Vtiger_Index_View
{
    public function __construct()
    {
        parent::__construct();
    }
    public function preProcess(Vtiger_Request $request)
    {
        parent::preProcess($request);
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
    }
    public function process(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
                $mode = $request->getMode();
                if ($mode) {
                    $this->{$mode}($request);
                } else {
                    $this->renderSettingsUI($request);
                }
    }

    public function renderSettingsUI(Vtiger_Request $request)
    {
        $qualifiedModuleName = $request->getModule(false);
        $ajax = $request->get("ajax");
        $viewer = $this->getViewer($request);
        $listModules = Vtiger_Module_Model::getEntityModules();
        $settingModel = new Settings_FinReports_Settings_Model();
        $entities = $settingModel->getData();
        $fields = Vtiger_Module_Model::getInstance('Accounts')->getFields();
        $vendorsFields = Vtiger_Module_Model::getInstance('Vendors')->getFields();
        $viewer->assign("QUALIFIED_MODULE", $qualifiedModuleName);
        $viewer->assign("LIST_MODULES", $listModules);
        $viewer->assign("ENTITY", $entities);
        $viewer->assign("FIELDS", $fields);
        $viewer->assign("VFIELDS", $vendorsFields);
        $viewer->assign("COUNT_ENTITY", count($entities));
        if ($ajax) {
            $viewer->view("SettingsAjax.tpl", $qualifiedModuleName);
        } else {
            $viewer->view("Settings.tpl", $qualifiedModuleName);
        }
    }
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        return $headerScriptInstances;
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        return $headerCssInstances;
    }
}

?>