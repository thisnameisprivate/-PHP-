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
}