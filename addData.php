<?php
$conf = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'dbname' => 'element'
];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=element", $conf['user'], $conf['pass']);
    $pdo->query("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Insert Error: " . $e->getMessage());
}
function shuffRand () {
    $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
    str_shuffle($str);
    $name = substr(str_shuffle($str),26,10);
    return $name;
}
function shuffle_phone () {
    $str = '182819928191234';
    $left_str = substr($str, 0, 3);
    $right_str = substr(str_shuffle($str), 4, 8);
    $str_phone = $left_str . $right_str;
    return $str_phone;
}
function shuffle_disease () {
    $alldise = ['男科检查'];
    return $alldise[array_rand($alldise, 1)];
}
function shuffle_status () {
    $allstatus = ['全流失', '半流失', '已到', '未到', '已诊治', '未到', '等待', '预约未定'];
    return $allstatus[array_rand($allstatus,1)];
}
function shuffle_from () {
    $allfrom = ['新媒体', '营销QQ'];
    return $allfrom[array_rand($allfrom, 1)];
}
function shuffle_sex () {
    $allsex = ['男', '女'];
    return $allsex[array_rand($allsex, 1)];
}
function shuffle_Date () {
    return date("Y-m-d H:i:s", strtotime(- mt_rand(1, 20). "day"));
}
function shuffle_custservice () {
    $allcustservice = ['叶慧', '董鑫', '崔丹'];
    return $allcustservice[array_rand($allcustservice, 1)];
}
for ($i = 0; $i < 10; $i ++) {
    $shuffle = shuffRand();
    $shuffle_phone = shuffle_phone();
    $shuffle_disease = shuffle_disease();
    $shuffle_status = shuffle_status();
    $shuffle_from = shuffle_from();
    $shuffle_sex = shuffle_sex();
    $shuffle_Date = shuffle_Date();
    $shuffle_custservice = shuffle_custservice();
    $mt_rand1 = mt_rand(20, 99);
    $mt_rand2 = mt_rand(0, 99);
    $sql = "insert into nk (name, old, phone, qq, diseases, fromAddress, switch, sex, desc1, expert, oldDate, desc2, status, newDate, custservice) values ('{$shuffle}', '{$mt_rand1}', '{$shuffle_phone}', '{$shuffle_phone}', '{$shuffle_disease}', '{$shuffle_from}', '本地', '{$shuffle_sex}', '{$shuffle}', '{$mt_rand2}', '{$shuffle_Date}', '{$shuffle}', '{$shuffle_status}', '{$shuffle_Date}', '{$shuffle_custservice}')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "<span style='color:red'>添加了</span>" . $stmt->rowCount() + $i . "<span style='color:red'>条数据;</span>";
    echo "<br>";
}