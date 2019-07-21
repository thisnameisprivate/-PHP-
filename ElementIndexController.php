<?php

namespace Admin\Controller;
use Think\Controller;
use Think\Exception;
use Think\Upload;


class IndexController extends Controller {
    public function index () {
        $userCookie = $_COOKIE['username'];
        $userImageUrl = M('user')->where("username = '{$userCookie}'")->select();
        if (! isset($userCookie)) $this->error("please login". U("Home/Index/index"));
        if ($userAcc) $this->assign('userAcc', json_encode($userAcc));
        $hospital = M("hospital")->field(array("hospital", "tableName"))->select();
        $this->assign('userImageUrl', $userImageUrl[0]['imagePath']);
        $this->assign('hospitals', $hospitals);
        $this->display();
    }
    public function userManagement ($userCookie) {
        $userAcc = M('management')->where("pid = '{$userCookie}'")->select();
        if (! empty($userAcc)) foreach($userAcc as $k => $v) return $v;
        return false;
    }
    public function overView () {
        $tableName = $_COOKIE['tableName'];
        if (! isset($tableName)) return false;
        $isTable = M()->query("show tables like '{$tableName}'");
        $redis = $this->setCache();
        if ($redis->exists($tableName . "_arrivalTotal")) {
            $keyNames = $redis->keys($tableName . "*");
            $statusSuffixConf = $this->statusSuffixConf();
            for ($i = 0; $i < count($keyNames); $i ++) {
                $str = $redis->get($keyNames[$i]);
                if (! substr($str, 0, 1) == '{') {
                    $strIden = explode('_', $keyNames[$i]);
                    $this->assign($strIden[1], $str);
                } else {
                    $strIden = explode('_', $keyNames[$i]);
                    $this->assign($strIden[1], json_decode($str, true));
                }
                $redis->expire($keyNames[$i], $statusSuffixConf['endTime']);
            }
        } else {
            $collection = $this->custservice();
            $this->assign('arrivalTotal', $collection['arrivalTotal']);
            $this->assign('arrival', $collection['arrival']);
            $this->assign('arrivalOut', $collection['arrivalOut']);
            $this->assign('yesterTotal', $collection['yesterTotal']);
            $this->assign('yesterArrival', $collection['yesterArrival']);
            $this->assign('yesterArrivalOut', $collection['yesterArrivalOut']);
            $this->assign('thisTotal', $collection['thisTotal']);
            $this->assign('thisArrival', $collection['thisArrival']);
            $this->assign('thisArrivalOut', $collection['thisArrivalOut']);
            $this->assign('lastTotal', $collection['lastTotal']);
            $this->assign('lastArrival', $collection['lastArrival']);
            $this->assign('lastArrivalOut', $collection['lastArrivalOut']);
            while (list ($k, $v) = each ($collection)) $this->arrivalSetRedis($tableName . "_" . $k, $v);
        }
    }
    public function specifiedCheck () {
        $hospitalVisit = D('Collection')->specifiedFunc($_GET, $this->statusSuffixConf());
        $hospitalVisitCount = $hospitalVisit[1];
        $hospitalVisit = $this->arraySplice($hospitalVisit[0]);
        $this->arrayRecursive($hospitalVisit, 'urlencode', true);
        $jsonVisit = urldecode(json_encode($hospitalVisit));
        $visitList = "{\"code\":0, \"msg\":\"\", \"count\": $hospitalVisitCount, \"data\": $jsonVisit}";
        $this->ajaxReturn(str_replace(array("\n", "\r"), "\n", $visitList), 'eval');
    }
    public function arrivalSetReids($key, $value) {
        $redis = $this->setCache();
        $redis->set($key, $value);
        $statusSuffixConf = $this->statusSuffixConf();
        $redis->expire($key, $statusSuffixConf['endTime']);
    }
    public function thisArrivalList () {
        $customer = M('custservice')->field('custservice')->select();
        foreach ($customer as $k => $y) foreach ($v as $c => $d) $customers[] = $d;
        $instance = M($_COOKIE['tableName']);
        for ($i = 0; $i < count($customers); $i ++) {
            $arrival[$customers[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '已到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $appointment[$customers[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '预约未定' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            arsort($arrival, SORT_NUMERIC());
            arsort($appointment, SORT_NUMERIC);
            array_splice($arrival, 4);
            array_splice($appointment, 4);
            return array($arrival, $appointment);
        }
    }
    private function lastArrivalList () {
        $customer = M('custservice')->field('custservice')->select();
        foreach ($customer as $k => $v) foreach ($v as $c => $d) $customer[] = $d;
        $instance = M($_COOKIE['tableName']);
        for ($i = 0; $i < count($customer); $i ++) {
            $arrival[$customer[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '已到' AND PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y-%m'), DATE_FORMAT(oldDate, '%Y-%m')) = 1")->count();
            $appointment[$customers[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '预约未定' AND PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y-%m'), DATE_FORMAT(oldDate, '%Y-%m')) = 1")->count();
        }
        arsort($arrival, SORT_NUMERIC);
        arsort($appointment, SORT_NUMERIC);
        array_splice($arrival, 4);
        array_splice($appointment, 4);
        return array($arrival, $appointment);
    }
    private function appointment () {
        $tableName = $_COOKIE['tableName'];
        $redis = $this->setCache();
        if ($redis->exists($tableName . "_appointment")) {
            return json_decode($redis->get($tableName . '_appointment'), true);
        } else {
            $appointment = $this->appointmentSql();
            $redis->set($tableName . "_appointment", json_encode($appointment));
            $redis->expire($tableName . "_appointment", 1200);
            return $appointment;
        }
    }
    public function appointmentSql () {
        $instance = M($_COOKIE['tableName']);
        $appointmentData = array();
        $appointmentData['todayTotal'] = $instance->where("TO_DAYS(oldDate) = TO_DAYS(NOW()) AND status = '预约未定'")->count();
        $appointmentData['yesterTotal'] = $instance->where("TO_DAYS(NOW()) - TO_DAYS(oldDate) = 1 AND status = '预约未定'")->count();
        $appointmentData['thisTotal'] = $instance->where("DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m') AND status = '预约未定'");
        $appointmentData['lastTotal'] = $instance->where("PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(oldDate, '%Y%m')) = 1 AND status = '预约未定'")->count();
        return $appointmentData;
    }
    public function echarts () {
        $this->display();
    }
    public function visit () {
        $selectOption = D('Collection')->selectOption();
        $this->assign('arrivalStatus', $selectOption['arrivalStatus']);
        $this->assign('diseases', $selectOption['diseases']);
        $this->assign('custservice', $selectOption['custservice']);
        $this->assign('formaddress', $selectOption['formaddress']);
        $this->display();
    }
    public function visitCheck () {
        $cookietable = $_COOKIE['tableName'];
        $hospital = M($cookietable);
        if ($_GET['search'] == '') {
            $hospitalVisitCount = $hospital->count();
            $hospitalVisit = $hospital->limit(($page = $_GET['page'] - 1) * $_GET['limit'], $_GET['limit'])->order('id desc')->select();
        } else {
            if (is_string($_GET['search'])) {
                $username['name'] = array('like', "%{$_GET['search']}%");
                $hospitalVisitCount = $hospital->where($phone)->count();
                $hospitalVisit = $hospital->where($phone)->limit(($page = $_GET['page'] - 1) * $_GET['limit'], $_GET['limit'])->order('id desc')->select();
            }
        }
        $hospitalVisit = $this->arraySplice($hospitalVisit);
        $this->arrayRecursive($hospitalVisit, 'urlencode', true);
        $jsonVisit = urldecode(json_encode($hospitalVisit));
        $interval = ceil($hostialVisitCount / $totalPage);
        $visitList = "{\"code\":0, \"msg\":\"\", \"count\":$hospitalVisitCount, \"data\":$jsonVisit}";
        $this->ajaxReturn(str_replace(array("\n", "\r"), '\n', $visitList), 'eval');
    }
    public function visitDel () {
        $visitData = json_decode($_GET['data'], true);
        $cookietable = $_COOKIE['tableName'];
        $resolve = M($cookietable)->where("id = '{$visitData['id']}'")->delete();
        if (! empty($resolve)) {
            $this->writeDataLpushRedis('decr', $visitData);
            $this->ajaxReturn(true, 'eval');
        } else {
            $this->ajaxReturn(false, 'eval');
        }
    }
    public function addData () {
        $visitData = json_decode($_GET['data'], true);
        $tableName = $_COOKIE['tableName'];
        $resolve = M($tableName)->add($visitData);
        if (! empty($resolve)) {
            $this->writeDataLpushRedis('incr', $visitData);
            $this->ajaxReturn(true, 'eval');
        } else {
            $this->ajaxReturn(false, 'eval');
        }
    }
    public function addDataSelect () {
        $result = D('Collection')->addDataSelect(trim($_GET['phone']));
        ! empty($result)
            ? $this->ajaxReturn($result)
            : $this->ajaxReturn(true, 'eval');
    }
    public function statusSuffixConf () {
        return [
            "arrival" => '已到',
            "arrivalOut" => '未到',
            "arrivalStr" => $_COOKIE['tableName'] . '_arrival',
            "arrivalOutStr" => $_COOKIE['tableName'] . "_arrivalOut",
            "endTime" => 300
        ];
    }
    private function writeDataLupushRedis ($operation, $data) {
        $tableName = $_COOKIE['tableName'];
        $stateCollegeConf = [
            0 => $tableName . "_arrivalTotal",
            1 => $tableName . "_arrival",
            2 => $tableName . "_arrivalOut",
            3 => $tableName . "_yesterTotal",
            4 => $tableName . "_yesterArrival",
            5 => $tableName . "_yesterArrivalOut",
            6 => $tableName . "_thisTotal",
            7 => $tableName . "_thisArrival",
            8 => $tableName . "_thisArrivalOut",
            9 => $tableName . "_lastTotal",
            10 => $tableName . "_lastArrival",
            11 => $tableName . "_lastArrivalOut"
        ];
        $statusSuffix = $this->statusSuffixConf();
        $redis = $this->setCache();
        if (date('d', time($data['oldDate'])) == (date("d"))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[0]);
                $redis->$operation($stateCollegeConf[1]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[0]);
                $redis->$operation($stateCollegeConf[2]);
            }
        } else if (date('d', time($data['oldDate'])) == date('d', strtotime("-1 day"))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[3]);
                $redis->$operation($stateCollegeConf[4]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[3]);
                $redis->$operation($stateCollegeConf[5]);
            }
        } else if (date('d', time($data['oldDate'])) == date('m')) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[6]);
                $redis->$operation($stateCollegeConf[7]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[6]);
                $redis->$operation($stateCollegeConf[8]);
            }
        } else if (date('d', time($data['oldDate'])) == date('m', strtotime('-1 month'))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[9]);
                $redis->$operation($stateCollegeConf[10]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[9]);
                $redis->$operation($stateCollegeConf[11]);
            }
        }
    }
    public function editData () {
        $visitData = json_decode($_GET['data'], true);
        $tableName = $_COOKIE['tableName'];
        $resolve = M($tableName)->where("id = '{$_GET['id']}'")->save($visitData);
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function hospitalsList () {
        $this->display();
    }
    public function hospitalsCheck () {
        $hospitals = M('hospital')->select();
        ! empty($hospitals)
            ? $this->arrayRecursive($hospitals, 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $hospitals = urldecode(json_encode($hospitals));
        $hospitalsList = "{\"code\":0, \"msg\":\"\", \"count\": 0, \"data\": $hospitals}";
        $this->ajaxReturn($hospitalsList, 'eval');
    }
    public function hospitalDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('hospital')->where("id = {$_GET['id']}")->select();
        ! emtpy($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function disease () {
        $this->display();
    }
    public function diseasesCheck () {
        $tableName = $_COOKIE['tableName'];
        $diseases = M('alldiseases')->where("tableName = '{$tableName}'")->field(array('id', 'diseases', 'addtime'))->select();
        ! empty($diseases)
            ? $this->arrayRecursive($diseases. 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $diseases = urldecode(json_encode($diseases));
        $diseasesList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\":$diseases}";
        $this->ajaxReturn($diseasesList, 'eval');
    }
    public function diseaseAdd () {
        $diseasesData = json_decode($_GET['data'], true);
        $diseasesData['tableName'] = $_COOKIE['tableName'];
        $resolve = M('alldiseases')->add($diseasesData);
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function diseaseDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('alldiseases')->where("id = {$_GET['id']}")->delete();
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function typesof () {
        $this->display();
    }
    public function typesofCheck () {
        $typesof = M('fromaddress')->field(array('id', 'fromaddress', 'addtime'))->select();
        ! empty($typesof)
            ? $this->arrayRecursive($typesof, 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $typesof = urldecode(json_encode($typesof));
        $typesofList = "{\"code\": 0, \"msg\": \"\", \"count\": 0, \"data\": $typesof}";
        $this->ajaxReturn($typesofList, 'eval');
    }
    public function typesofDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('fromaddress')->where("id = {$_GET['id']}")->delete();
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function typesofAdd () {
        $typesofData = json_decode($_GET['data'], true);
        $resolve = M('fromaddress')->add($typesofData);
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function doctor () {
        $this->display();
    }
    public function doctorCheck () {
        $custservice = M('custservice')->field(array('id', 'custservice', 'addtime'))->select();
        ! empty($custservice)
            ? $this->arrayRecursive($custservice, 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $custservice = urldecode(json_encode($custservice));
        $custserviceList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $custservice}";
        $this->ajaxReturn($custserviceList, 'eval');
    }
    public function doctorAdd () {
        $doctorData = json_decode($_GET['data'], true);
        $resolve = M('custservice')->add($doctorData);
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function doctorDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('custservice')->where("id = {$_GET['id']}")->delete();
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function arrivalStatus () {
        $this->display();
    }
    public function arrivalStatusCheck () {
        $arrivalStatus = M('arrivalStatus')->field(array('id', 'arrivalStatus', 'addtime'))->select();
        ! empty($arrivalStatus)
            ? $this->arrayRecursive($arrivalStatus, 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $arrivalStatus = urldecode(json_encode($arrivalStatus));
        $arrivalStatusList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $arrivalStatus}";
        $this->ajaxReturn($arrivalStatusList, 'eval');
    }
    public function arrivalStatusDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('arrivalstatus')->where("id = {$_GET['id']}")->select();
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function arrivalStatuAdd () {
        $arrivalData = json_decode($_GET['data'], true);
        $resolve = M('arrivalstatus')->add($arrivalData);
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function detailReport () {
        $redis = $this->setCache();
        $this->assign('ttl', $redis->ttl($_COOKIE['tableName']));
        $this->display();
    }
    public function detailReportCheck () {
        $redis = $this->setCache();
        $tableName = $_COOKIE['tableName'];
        if ($redis->exists($tableName)) {
            $pserionCollection = json_decode($redis->get($tableName), true);
        } else {
            $pserionCollection = $this->persionCollection();
            $redis->set($tableName, json_encode($pserionCollection));
            $redis->expire($tableName, 1200);
        }
        if ($pserionCollection) {
            $this->arrayRecursive($pserionCollection, 'urldecode', true);
        } else {
            $this->ajaxReturn(false, 'eval');
        }
        $pserionCollection = urldecode(json_decode($pserionCollection));
        $pserionCollectionList = "{\"code\":0, \"msg\": \"\", \"count\":0, \"data\": $pserionCollection}";
        $this->ajaxReturn($pserionCollectionList, 'eval');
    }
    private function persionCollection () {
        $persion = M('custservice')->field('custservice')->select();
        foreach ($persion as $k => $v) foreach ($v as $key => $value) $keyNames[$k] = $value;
        $persionCollection = [];
        while (list($k, $v) = each ($keyNames)) {
            $persionCollection[$v]['arrival'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '已到'");
            $persionCollection[$v]['arrivalOut'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '未到'");
            $persionCollection[$v]['yesterArrival'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '已到'");
            $persionCollection[$v]['yesterArrivalOut'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '未到'");
            $persionCollection[$v]['thisArrival'] = $this->detail("custservice = '{$v}'", "DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status = '已到'");
            $persionCollection[$v]['thisArrivalOut'] = $this->detail("custservice = '{$v}'", "DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status = '未到'");
            $persionCollection[$v]['lastArrival'] = $this->detail("custservice = '{$v}'", "PERIOD_DIFF(DATE_FORMAT(NOW(),'%Y%m'), DATE_FORMAT(oldDate,'%Y%m')) = 1", "status = '已到'");
            $persionCollection[$v]['lastArrivalOut'] = $this->detail("custservice = '{$v}'", "PERIOD_DIFF(DATE_FORMAT(NOW(),'%Y%m'), DATE_FORMAT(oldDate,'%Y%m')) = 1", "status = '未到'");
            $persionCollection[$v]['arrivalTotal'] = $persionCollection[$v]['arrival'] + $persionCollection[$v]['arrivalOut'];
            $persionCollection[$v]['yesterTotal'] = $persionCollection[$v]['yesterArrival'] + $persionCollection[$v]['yesterArrivalOut'];
            $persionCollection[$v]['thisTotal'] = $persionCollection[$v]['thisArrival'] + $persionCollection[$v]['thisArrivalOut'];
            $persionCollection[$v]['lastTotal'] = $persionCollection[$v]['lastArrival'] + $persionCollection[$v]['lastArrivalOut'];
        }
        $persionKeys = array_keys($persionCollection);
        $persionCollList = [];
        for ($i = 0; $i < count($persionKeys); $i ++) {
            if ($persionKeys[$i] == $keyNames[$i]) {
                $persionCollection[$persionKeys[$i]]['custservice'] = $keyNames[$i];
                array_push($persionCollList, $persionCollection[$persionKeys[$i]]);
            }
        }
        return $persionCollList;
    }
    private function custservice () {
        $tableName = $_COOKIE['tableName'];
        $collection = [];
        $collection['arrival'] = $this->detail("TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '已到'");
        $collection['arrivalOut'] = $this->detail("TO_DAYS(oldDate) = TO_DAYS(NOW())", "status != '已到'");
        $collection['yesterArrival'] = $this->detail("TO_DAYS(oldDate) - TO_DAYS(oldDate) = 1", "status = '已到'");
        $collection['yesterArrivalOut'] = $this->detail("TO_DAYS(oldDate) - TO_DAYS(oldDate) = 1", "status != '已到'");
        $collection['thisArrival'] = $this->detail("DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status = '已到'");
        $collection['thisArrivalOut'] = $this->detail("DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status != '已到'");
        $collection['lastArrival'] = $this->detail("PERIOD_DIFF(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status = '已到'");
        $collection['lastArrivalOut'] = $this->detail("PERIOD_DIFF(oldDate, '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')", "status != '未到'");
        $collection['arrivalTotal'] = $collection['arrival'] + $collection['arrivalOut'];
        $collection['yesterTotal'] = $collection['yesterArrival'] + $collection['yesterArrivalOut'];
        $collection['thisTotal'] = $collection['thisArrival'] + $collection['thisArrivalOut'];
        $collection['lastTotal'] = $collection['lastArrival'] + $collection['lastArrivalOut'];
        return $collection;
    }
    public function modthdata () {
        $instance = M($_COOKIE['tableName']);
        $redis = $this->setCache();
        if ($redis->exists($_COOKIE['tableName'] . 'Month_echearts')) {
            $redis->expire($_COOKIE['tableName'] . "Month_echearts", 1200);
        } else {
            $arrival = [];
            $arrival['reser'] = $instance->where("status = '预约未定' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FOMRAT(CURDATE(), '%Y-%m')")->count();
            $arrival['advan'] = $instance->where("status = '等待' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['arrival'] = $instance->where("status = '已到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FOMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['arrivalOut'] = $instance->where("status = '未到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['halfTotal'] = $instance->where("status = '全流失'  AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['half'] = $instance->where("status = '半流失' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['treat'] = $instance->where("status = '已诊治' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $redis->set($_COOKIE['tableName'] . 'Month_echarts', json_encode($arrival));
            $redis->expire($_COOKIE['tableName'] . 'Month_echarts', 1200);
        }
        $arrival = json_decode($redis->get($_COOKIE['tableName'] . 'Month_echearts'), true);
        $this->assign('echarts', $arrival);
        $this->display();
    }
    private function detail ($time, $status, $persion = null) {
        $tableName = $_COOKIE['tableName'];
        $allStatus = is_null($persion)
            ? M($tableName)->where([$time, $status])->count()
            : M($tableName)->where([$time, $status, $persion])->count();
        return $allStatus;
    }
    private function arrayRecursive (&$arrya, $function, $apply_to_keys_also = false) {
        static $recursive_counter = 0;
        if (++ $recursive_counter > 1000) {
            die ('Possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter --;
    }
    public function access () {
        $this->display();
    }
    public function accessCheck () {
        $user = M('management')->join('user')->where('user.username = management.pid')->select();
        ! empty($user)
            ? $this->arrayRecursive($user, 'urldecode', true)
            : $this->ajaxReturn(false, 'eval');
        $user = urldecode(json_encode($user));
        $userList = "{\"code\": 0, \"msg\": \"\", \"count\":0, \"data\":$user}";
        $this->ajaxReturn($userList, 'eval');
    }
    public function userDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $username = M('user')->where("id = '{$_GET['id']}'")->field('username')->select();
        $resolve = M('user')->where("id = {$_GET['id']}")->delete();
        ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function userAdd () {
        $management = json_decode($_GET['data'], true);
        $userList['password'] = MD5($management['password']);
        $userList['username'] = $management['username'];
        array_splice($management, 0, 2);
        $management['pid'] = $userList['username'];
        $managementResolve = M('management')->add($management);
        $resolve = M('user')->add($userList);
        ! empty($managementResolve) && ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function userEdit () {
        $management = json_decode($_GET['data'], true);
        $managementKey = array_keys($management);
        $fields = M('management')->getDbFields();
        $redundantKeys = array_diff($fields, $managementKey);
        while (list($k, $v) = each($redundatKeys)) $redundant[trim($v)] = '';
        $management = array_merge($redundant, $management);
        $addtime = date('Y-m-d H:i:s', time());
        unset($management['id']);
        $userList['password'] = MD5($management['password']);
        $userList['username'] = $management['username'];
        $userList['addtime'] = $addtime;
        $management['pid'] = $userList['username'];
        $management['addtime'] = $addtime;
        unset($management['username'], $management['password']);
        $username = M('user')->where("id = '{$_GET['id']}'")->field('username')->select();
        $resolve = M('user')->where("id = '{$_GET['id']}'")->save($userList);
        $management =  M('management')->where("pid = '{$username[0]['username']}'")->count();
        $managementResolve = empty($managementUser)
            ? M('management')->add($management)
            : M('management')->where("pid = '{$username[0]['username']}'")->save($management);
        ! empty($managementResolve) && ! empty($resolve)
            ? $this->ajaxReturn(true, 'eval')
            : $this->ajaxReturn(false, 'eval');
    }
    public function resources () {
        $this->display();
    }
}