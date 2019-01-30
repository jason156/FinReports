<?php
require_once 'modules/FinReports/vendor/vendor/autoload.php';

class FinReports_ActionAjax_Action extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("getReport");
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    public function getReport(Vtiger_Request $request)
    {
        $module = $request->get("moduleSelected");
        $record_id = $request->get("record");
        $moduleModel = new FinReports_Module_Model();
        if ($module == 'Accounts') {
            $field_name = $moduleModel->getOrgField();
        } else {
            $field_name = $moduleModel->getVendorField();
        }
        $key = $moduleModel->getAPI();
        $method = 'getOrganization';
        $record_model = Vtiger_Record_Model::getInstanceById($record_id, $module);
        $field_value = $record_model->get($field_name);
        $result = $this->sendRequest($key, $method, $field_value);
        $response = new Vtiger_Response();
        if ($result['status'] == 'success' && $result['data'] !== false) {
            if ($this->recordNotExist($field_name, $field_value, $result['data']['org']['stat_year'])) {
                $res = $this->createRecord($result['data'], $record_model);
                if ($res) {
                    $response->setResult(array("value" => $res->getDetailViewUrl()));
                } else {
                    $response->setError("ERROR_CREATING_RECORD", 'Ошибка при регистрации отчётности');
                    $response->setResult("Ошибка при создании отчётности");
                }
            } else {
                $response->setError("RECORD_ALREADY_EXIST", 'Отчётность уже загружена');
                $response->setResult("Отчётность уже загружена");
            }
        } else {
            $response->setError($result['error_code'], $result['error_text']);
            $response->setResult($result['error_text']);
        }

        $response->emit();
    }
    /*
     * $fields = ['key' => '99c5e07b4d5de9d18c350cdf64c5aa3d', 'method' => 'getOrganization', 'inn' => '7736050003'];
     */
    public function sendRequest($key, $method, $inn)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://www.testfirm.ru/api/', [
            'form_params' =>  array(
                'method' => $method,
                'inn' => $inn,
                'key' => $key
            ),
        ]);
        $body = json_decode($response->getBody(), true);
        /*var_dump($body);die;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.testfirm.ru/api/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"method\"\r\n\r\n$method\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"inn\"\r\n\r\n$inn\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"key\"\r\n\r\n$key\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 731f3344-9ded-4174-b97a-ddeb9d7e74ca",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);*/
        return $body;

    }
    public function recordNotExist($field_name, $inn, $year)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_finreportscf INNER JOIN vtiger_crmentity ON vtiger_finreportscf.finreportsid = vtiger_crmentity.crmid WHERE `cf_1259` = ? AND vtiger_crmentity.deleted = 0", array($inn . $year));
        $numRows = $adb->num_rows($result);
        if (0 >= $numRows) {
            return true;
        } else {
            return false;
        }

    }
    public function createRecord($data, $parentModel)
    {
        $record = Vtiger_Record_Model::getCleanInstance('FinReports');
        $record->set('mode', 'create');
        $record->set('name', $data['org']['inn'] . ' - отчётность за ' . $data['org']['stat_year'] . ' год');
        $record->set('cf_1259', $data['org']['inn'] . $data['org']['stat_year']);
        $record->set('description', $data['org']['name']);
        if ($parentModel->getModuleName() == 'Accounts') {
            $record->set('cf_accounts_id', $parentModel->getId());
        } else {
            $record->set('cf_vendors_id', $parentModel->getId());
        }
        $record->set('cf_1261', $data['org']['okved']);
        $record->set('cf_1263', $data['org']['okved_text']);
        $record->set('cf_1265', $data['org']['revenue']);
        $record->set('cf_1267', $data['org']['assets']);
        $record->set('cf_1267', $data['org']['assets']);
        $record->set('cf_1269', $data['org']['org_group']);
        $record->set('cf_1271', $data['org']['org_group_text']);
        $record->set('cf_1273', $data['org']['region']);
        $record->set('cf_1275', $data['org']['region_text']);
        $record->set('cf_1277', $data['org']['stat_year']);
        $record->set('cf_1295', $data['warnings']['fns_no_report']);
        $record->set('cf_1297', $data['warnings']['fns_debt']);
        $record->set('cf_1279', $data['result']['industry']['score']);
        $record->set('cf_1281', $data['result']['industry']['score_text']);
        $record->set('cf_1283', $data['result']['industry']['score_longtext']);
        $record->set('cf_1285', $data['result']['all']['score']);
        $record->set('cf_1287', $data['result']['all']['score_text']);
        $record->set('cf_1289', $data['result']['all']['score_longtext']);
        $record->set('cf_1291', $data['change']['value']);
        $record->set('cf_1293', $data['change']['value_text']);
        $record->set('cf_1299', $data['details']['avtonom']['value']);
        $record->set('cf_1301', $data['details']['sosobesp']['value']);
        $record->set('cf_1303', $data['details']['pokrinvest']['value']);
        $record->set('cf_1309', $data['details']['CashRatio']['value']);
        $record->set('cf_1311', $data['details']['ROA']['value']);
        $record->set('cf_1313', $data['details']['ProfitMargin']['value']);
        $record->set('cf_1315', $data['details']['GrossMargin']['value']);
        $record->save();
        return $record;
    }
}

?>