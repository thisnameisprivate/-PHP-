<?php

namespace Admin\Controller;
use Think\COntroller;
use Think\Exception;
use Think\Upload;

class IndexController extends Controller {
    public function index () {
        $userCookie = $_COOKIE['username'];
        $userImageUrl = M('user')->where("username = '{$userCookie}'")->select();
        if (! isset($userCookie)) $this->error("please login", U("Home/Index/index"));
        $userAcc = $this->userManagement($userCookie);
        if ($userAcc) $this->assign('userAcc', json_encode($userAcc));
        $hospitals = M('hospital')->field(array('hospital', 'tableName'))->select();
        $this->assign('userImageUrl', $userImageUrl[0]['imagePath']);
        $this->assign('hospitals', $hospitals);
        $this->display();
    }
    private function userManagement ($userCookie) {
        $userAcc = M('management')->where("pid = '{$userCookie}'")->select();
        if (! empty($userAcc)) foreach ($userAcc as $k => $v) return $v;
        return false;
    }
    public function overView () {
        $tableName = $_COOKIE['tableName'];
        if (! isset($tableName)) return false;
        $isTable = M()->query("show tables like '{$tableName}'");
        if (! $isTable) { if (! $this->createTable($tableName)) return false;}
        $redis = $this->setCache();
        if ($redis->exists($tableName . "arrivalTotal")) {
            $keyNames  = $redis->keys($tableName . "*");
            $statusSuffixConf = $this->statusSuffixConf();
            for ($i = 0; $i < count($keyNames); $i ++) {
                $str = $redis->get($keyNames[$i]);
                if (! substr($str, 0, 1) == '{') {
                    $strIden = explode('_', $keyNames[$i]);
                    $this->assign($strIden[1], $str);
                } else {
                    $strIden = explode('_', $keyNames[$i]);
                    $this->assign($keyNames[$i], $statusSuffixConf('endTime'));
                }
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
            while (list ($k, $v) = each($collection)) $this->arrivalSetRedis($tableName . "_" . $k, $v);
        }
        $thisArrivalList = $this->thisArrivalList();
        $lastArrivalList = $this->lastArrivalList();
        $this->assign('appointment', $this->appointment());
        $this->assign('thisArrivalSort', $thisArrivalList[0]);
        $this->assign('thisAppointmentSort', $thisArrivalList[1]);
        $this->assign('lastArrivalSort', $lastarrivalList[0]);
        $this->assign('lastAppointmentSort', $lastAppointment[1]);
        $this->display();
    }
    public function specifed () {
        $this->specifed('iden', $_GET['iden']);
        $selectOption = D('Collection')->selectOption();
        $this->assign('arrivalStatus', $selectOption['arrivalStatus']);
        $this->assign('disease', $selectOption['diseases']);
        $this->assign('custservice', $selectOption['custservice']);
        $this->assign('formaddress', $selectOption['fromaddress']);
        $this->display();
    }
    public function specifiedCheck () {
        $hospitalVisit = D('Collection')->specifiedFunc($_GET, $this->statusSuffixConf());
        $hospitalVisitCount = $hospitalVisit[1];
        $hospitalVisit = $this->arraySplice($hospitalVisit[0]);
        $this->arrayRecursive($hospitalVisit, 'urldecode', true);
        $jsonVisit = urldecode(json_encode($hospitalVisit));
        $visitList = "{\"code\":0, \"msg\":\"\", \"count\":$hospitalVisitCount, \"data\": $jsonVisit}";
        $this->ajaxReturn(str_replace(array("\n", "\r"), '\n', $visitList), 'eval');
    }
    private function arrivalSetRedis ($key, $value) {
        $redis = $this->setCache();
        $redis->set($key, $value);
        $statusSuffixConf = $this->statusSuffixConf();
        $redis->expire($key, $statusSuffixConf['endTime']);
    }
    private function thisArrivalList () {
        $customer = M('custservice')->field('custservice')->select();
        foreach ($customer as $k => $v) foreach ($v as $c => $d) $customers[] = $d;
        $instance = M($_COOKIE['tableName']);
        for ($i = 0; $i < count($customer); $i ++) {
            $arrival[$customers[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '已到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURRENT(), '%Y-%m')")->count();
            $pponintment[$customersp[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '预约未定' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURRENT(), '%Y-%m')")->count();
        }
        arsort($arrival, SORT_NUMERIC);
        arsort($appointment, SORT_NUMERIC);
        array_splice($arrival, 4);
        array_splice($appointment, 4);
        return array($arrival, $appointment);
    }
    private function lastArrivalList () {
        $customer = M('custservice')->field('custservice')->select();
        foreach ($customer as $k => $v) foreach ($v as $c => $d) $customers[] = $d;
        $instance = M($_COOKIE['tableName']);
        for ($i = 0; $i < count($customer); $i ++) {
            $arrival[$customers[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '已到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURRENT(), '%Y-%m')")->count();
            $pponintment[$customersp[$i]] = $instance->where("custService = '{$customers[$i]}' AND status = '预约未定' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURRENT(), '%Y-%m')")->count();
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
        if ($redis->exists($tableName . '_appointment')) {
            return json_decode($redis->get($tableName . '_appointment'), true);
        } else {
            $appointment = $this->appointment();
            $redis->set($tableName . '_appointment', json_encode($appointment));
            $redis->expire($tableName . '_appointment', 1200);
            return $appointment;
        }
    }
    private function appointmentSql () {
        $instance = M($_COOKIE['tableName']);
        $appointmentData = array();
        $appointmentData['todayTotal'] = $instance->where("TO_DAYS(oldDate) = TO_DAYS(NOW()) AND status = '预约未定'")->count();
        $appointmentData['yesterTotal'] = $instance->where("TO_DAYS(NOW()) - TO_DAYS(oldDate) = 1 AND status = '预约未定'")->count();
        $appointmentData['thisTotal'] = $instance->where("DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURRENT(), '%Y%m') AND status = '预约未定'")->count();
        $appointmentData['lastTotal'] = $instance->where("PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(oldDate, '%Y%m')) = 1 AND status = '预约未定'")->count();
        return $appointmentData;
    }
    public function ehcarts () {
        $this->display();
    }
    public function visit () {
        $selectOption = D('Collection')->selectOption();
        $this->assign('arrivalStatus', $selectOption['arrivalStatus']);
        $this->assign('diseases', $selectOption['diseases']);
        $this->assign('custservice', $selectOption['custservice']);
        $this->assign('fromaddress', $selectOption['fromaddress']);
        $this->display();
    }
    private function visitCheck () {
        sleep(5);
        $cookietable = $_COOKIE['tableName'];
        $hospital = M($cookietable);
        if ($_GET['search'] == '') {
            $hospitalVisitCount = $hospital->count();
            $hospitalVisit = $hospital->limit(($page - $_GET['page'] - 1) * $_GET['limit'], $_GET['limit'])->order('id desc')->select();
        } else {
            if (is_string($_GET['search'])) {
                $username['name'] = array('like', "%{$_GET['search']}%");
                $hospitalVisitCount = $hospital->where($username)->count();
                $hospitalVisit = $hospital->where($username)->limit(($page = $_GET['page'] - 1) * $_GET['limit'], $_GET['limit'])->order('id desc')->select();
            }
            if (is_numeric($_GET['search'])) {
                $phone['phone'] = array('like', "%{$_GET['search']}%");
                $hospitalVisitCount = $hospital->where($phone)->count();
                $hospitalVisit = $hospital->where($phone)->limit(($page = $_GET['page'] - 1) * $_GET['limit'], $_GET['limit'])->order('id desc')->select();
            }
        }
        $hospitalVisit = $this->arraySPlice($hospitalVisit);
        $this->arrayRecursive($hospitalVisit, 'urldecode', true);
        $jsonVisit = urldecode(json_encode($hospitalVisit));
        $interval = ceil($hospitalVisitCount / $totalPage);
        $visitList = "{\"code\":0, \"msg\":\"\", \"count\":$hospitalVisitCount, \"data\": $jsonVisit}";
        $this->ajaxReturn(str_replace(array("\n", "\r"), '\n', $visitList), 'eval');
    }
    public function visitDel () {
        $visitData = json_encode($_GET['data'], true);
        $cookieTable = $_COOKIE['tableName'];
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
        $result = D('Collection')->addDataSelect($_GET['phone']);
        ! empty($result)
        ? $this->ajaxReturn($result)
        : $this->ajaxReturn(true, 'eval');
    }
    public function statusSuffixConf () {
        return array(
            'arrival' => '已到',
            'arrivalOut' => '未到',
            'arrivalStr' => $_COOKIE['tableName'] . '_arrival',
            'arrivalOutStr' => $_COOKIE['tableName'] . '_arrivalOut',
            'endTime' => 300
        );
    }
    private function writeDataLpushRedis ($operation, $data) {
        $tableName = $_COOKIE['tableName'];
        $stateCollegeConf = array(
            0 => $tableName . '_arrivalTotal',
            1 => $tableName . '_arrival',
            2 => $tableName . '_arrivalOut',
            3 => $tableName . '_yesterTotal',
            4 => $tableName . '_yesterArrival',
            5 => $tableName . '_yesterArrivalOut',
            6 => $tableName . '_thisTotal',
            7 => $tableName . '_thisArrival',
            8 => $tableName . '_thisArrivalOut',
            9 => $tableName . '_lastTotal',
            10 => $tableName . '_lastArrival',
            11 => $tableName . '_lastArrivalOut'
        );
        $statusSuffix = $this->statusSuffixConf();
        $redis = $this->setCache();
        if (date('d', time($data['oldDate'])) == (date('d'))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[0]);
                $redis->$operation($stateCollegeConf[1]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[0]);
                $redis->$operation($stateCollegeConf[2]);
            }
        } else if (date('d', time($data['oldDate'])) == date('d', strtotime('-1 day'))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[3]);
                $redis->$operation($stateCollegeConf[4]);
            } else {
                $redis->$operation($stateCollegeConf[3]);
                $redis->$operation($stateCollegeConf[5]);
            }
        }
        if (date('m', time($data['oldDate'])) == date('m')) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[6]);
                $redis->$operation($stateCollegeConf[7]);
            }
            if ($data['status'] == $statusSuffix['arrivalOut']) {
                $redis->$operation($stateCollegeConf[6]);
                $redis->$operation($stateCollegeConf[8]);
            }
        } else if (date('m', time($data['oldDate'])) == date('m', strtotime('-1 month'))) {
            if ($data['status'] == $statusSuffix['arrival']) {
                $redis->$operation($stateCollegeConf[9]);
                $redis->$operation($stateCollegeConf[10]);
            } else {
                $redis->$operation($stateCollegeConf[9]);
                $redis->$operation($stateCollegeConf[11]);
            }
        }
    }
    public function editData () {
        $visitData = json_decode($_GET['data'], true);
        $tableName = $_COOKIE['tableName'];
        $resolve = M($tableName)->where("id = {$_GET['id']}")->save($visitData);
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
        : $this->arrayRecursive(false, 'eval');
        $hospitals = urldecode(json_encode($hospitals));
        $hospitalsList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $hospitals}";
    }
    public function hospitalsAdd () {
        $hospitalsData = json_decode($_GET['data'], true);
        $resolve = M('hospital')->add($hospitalsData);
        ! empty($resolve)
        ? $this->ajaxReturn(true, 'eval')
        : $this->ajaxReturn(false, 'eval');
    }
    public function disease () {
        $this->display();
    }
    public function diseaseCheck () {
        $tableName = $_COOKIE['tableName'];
        $diseases = M('alldiseases')->where("tableName = '{$tableName}'")->field(array('id', 'diseases', 'addtime'))->select();
        ! empty($diseases)
        ? $this->arrayRecursive($diseases, 'urldecode', true)
        : $this->ajaxReturn(false, 'eval');
        $diseases = urldecode(json_encode($diseases));
        $diseasesList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $diseases}";
        $this->ajaxReturn($diseasesList, 'eval');
    }
    public function diseaseAdd () {
        $diseasesData = json_encode($_GET['data'], true);
        $diseasesData['tableName'] = $_COOKIE['tableName'];
        $resolve = M('alldiseases')->add($diseasesData);
        ! empty($resolve)
        ? $this->ajaxReturn(true, 'eval')
        : $this->ajaxReturn(true, 'eval');
    }
    public function diseaseDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('alldiseases')->where("id = '{$_GET['id']}'")->delete();
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
        $typesofList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $typesof}";
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
        ? $this->ajaxReturn(treu, 'eval')
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
    public function doctoradd () {
        $doctorData = json_decode($_GET['data'], true);
        $resolve = M('custservice')->add($doctorData);
        ! empty($resolve)
        ? $this->ajaxReturn(true, 'eval')
        : $this->ajaxReturn(false, 'eval');
    }
    public function doctorDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('custservice')->where("id = {$_GET['id']}")->delete();
        ! emppty($resolve)
        ? $this->ajaxReturn(true, 'eval')
        : $this->ajaxReturn(false, 'eval');
    }
    public function arrivalStatus () {
        $this->display();
    }
    public function arrivalStatusCheck () {
        $arrivalStatus = M('arrivalstatus')->feild(array('id', 'arrivalStatus', 'addtime'))->select();
        ! empty($arrivalStatus)
        ? $this->arrayRecursvie($arrivalStatus, 'urldecode', true)
        : $this->ajaxReturn(false, 'eval');
        $arrivalStatus = urldecode(json_encode($arrivalStatus));
        $arrivalStatusList = "{\"code\":0,  \"msg\":\"\", \"count\":0, \"data\": $arrivalStatus}";
        $this->ajaxReturn($arrivalStatusList, 'eval');
    }
    public function arrivalStatusDel () {
        if (! is_numeric($_GET['id'])) $this->ajaxReturn(false, 'eval');
        $resolve = M('arrivalstatus')->where("id = {$_GET['id']}")->delete();
        ! empty($resolve)
        ? $this->ajaxReturn(true, 'eval')
        : $this->ajaxReturn(false, 'eval');
    }
    public function arrivalStatusAdd () {
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
        $pserionCollection = urldecode(json_encode($pserionCollection));
        $pserionCOllectionList = "{\"code\":0, \"msg\":\"\", \"count\":0, \"data\": $pserionCollection}";
        $this->ajaxReturn($pserionCollectionList, 'eval');
    }
    private function persionCollection () {
        $persion = M('custservice')->field('custservice')->select();
        foreach ($persion as $k => $v) foreach ($v as $key => $value) $keyNames[$k] = $value;
        $persionCollection = array();
        while (list($k, $v) = each($keyNames)) {
            $persionCollection[$v]['arrival'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '已到'");
            $persionCollection[$v]['arrivalOut'] = $this->detail("custservice = '{$v}'", "TO_DAYS(oldDate) = TO_DAYS(NOW())", "status = '未到'");
            $persionCollection[$v]['yesterArrival'] = $this->detail("custservice = '{$v}'", "TO_DAYS((NOW)) - TO_DAYS(oldDate) = 1", "status = '已到'");
            $persionCollection[$v]['yesterArrivalOut'] = $this->detail("custservice = '{$v}'", "TO_DAYS(NOW()) - TO_DAYS(oldDate) = 1", "status = '未到'");
            $persionCollection[$v]['thisArrival'] = $this->detail("custservice = '{$v}'", "DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURRENT(), '%Y%m')", "status = '已到'");
            $persionCollection[$v]['thisArrivalOut'] = $this->detail("custservice = '{$v}'", "DATE_FORMAT(oldDate, '%Y%m') = DATE_FORMAT(CURRENT(), '%Y%m')", "status = '未到'");
            $persionCOllection[$v]['lastArrival'] = $this->detail("custservice = '{$v}'", "PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(oldDate, '%Y%m')) = 1", "status = '已到'");
            $persionCollection[$v]['lastArrivalOut'] = $this->detail("cutservice = '{$v}'", "PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m), DATE_FORMAT(oldDate, '%Y%m)) = 1", "status = '未到'");
            $persionCollection[$v]['arrivalTotal'] = $persionCollection[$v]['arrival'] + $persionCollection[$v]['arrivalOut'];
            $persionCollection[$v]['yseterTotal'] = $persionCollection[$v]['yesterArrival'] + $persionCollection[$v]['yesterArrivalOut'];
            $persionCollection[$v]['thisTotal'] = $persionCollection[$v]['thisArrival'] + $persionCollection[$v]['thisArrivalOut'];
            $persionCollection[$v]['lastTotal'] = $persionCollection[$v]['lasatArrival'] + $persionCollection[$v]['lastArrivalOut'];
        }
        $persionKeys = array_keys($persionCollection);
        $persionCollList = array();
        for ($i = 0; $i < count($persionKeys); $i ++) {
            if ($persionKeys[$i] == $keyNames[$i]) {
                $persionCollection[$persionKeys[$i]]['custservice'] = $keyNames[$i];
                array_push($persionCollList, $persionCollection[$persionKeys[$i]]);
            }
        }
        return $persionCollList;
    }
    public function mohtdata () {
        $instance = M($_COOKIE['tableName']);
        $redis = $this->setCache();
        if ($redis->exists($_COOKIE['tableName'] . 'Month_echarst')) {
            $redis->expire($_COOKIE['tableName'] . 'Month_echarst', 1200);
        } else {
            $arrival = array();
            $arrival['reser'] = $instance->where("status = '预约未定' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['advan'] = $instance->where("status = '等待' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['arrival'] = $instance->where("status = '已到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['arrivalOut'] = $instance->where("status = '未到' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['halfTotal'] = $instance->where("status = '全流失' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m)")->count();
            $arrival['half'] = $instance->where("status = '半流失' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $arrival['treat'] = $instance->where("status = '已诊治' AND DATE_FORMAT(oldDate, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->count();
            $redis->set($_COOKIE['tableName'] . 'Month_echarst', json_encode($arrival));
            $redis->expire($_COOKIE['tableName'] . 'Month_echarst', 1200);
        }
        $arrival = json_decode($redis->get($_COOKIE['tableName'] . 'Month_echarst'), true);
        $this->assign('echarts', $arrival);
        $this->display();
    }
    public function detail ($time, $status, $persion = null) {
        $tableName = $_COOKIE['tableName'];
        $allStatus = is_null($persion)
            ? M($tableName)->where(array($time, $status))->count()
            : M($tableName)->where(array($time, $status, $persion))->count();
        return $allStatus;
    }
    private function arrayRecursive (&$array, $function, $apply_to_keys_also = false) {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die ('possible deep recursion attack');
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
}