<?php

// 主从复制, 读写分离
class Db {
    private $res;
    public function __construct() {
        $querystr = strtolower(trim(substr($sql, 0, 6)));
        if ($querystr == 'select') {
            $res = $this->slave($sql);
            $this->res = $res;
        } else {
            $res = $this->master($sql);
            $this->res = $res;
        }
    }
    private function slave ($sql) {
        $slave_ip = $this->get_slave_ip();
        $dsn = "mysql:host=$slave_ip;dbname=test";
        $user = 'root';
        $pass = '';
        $dbh = new PDO($dsn, $user, $pass);
        return $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    private function master ($sql) {
        $master_ip = '192.168.80.3';
        $dsn = "mysql:host=$master_ip;dbname=test";
        $user = 'root';
        $pass = '';
        $dbh = new PDO($dsn, $user, $pass);
        return $this->exec($sql);
    }
    private function get_slave_ip () {
        $master_ips = ['192.168.0.1', '192.168.0.2'];
        $count = count($master_ips) - 1;
        $index = mt_rand(0, $count);
        return $slave_ips[$index];
    }
    public function get_res () {
        return $this->res;
    }
    public function func () {

    }
}