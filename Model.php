<?php

$config = include "config.php";
$model = new \Think\Model();


class Model {
    protected $host;
    protected $user;
    protected $pwd;
    protected $dbname;
    protected $charset;
    protected $prefix;
    protected $link;
    protected $tableName;
    protected $sql;
    protected $options;

    public function __construct ($config) {
        $this->host = $config['DB_HOST'];
        $this->user = $config['DB_USER'];
        $this->pwd = $config['DB_PWD'];
        $this->dbname = $config['DB_NAME'];
        $this->charset = $config['DB_CHARSET'];
        $this->prefix = $config['DB_PREFIX'];
        $this->link = $this->connect();
        $this->tableName = $this->getTableName();
        $this->initOptions();
    }
    protected function connect () {
        $link = mysqli_connect($this->host, $this->user, $this->pwd);
        if (!$link) {
            die ("database connect faile");
        }
        mysqli_select_db($link, $this->dbname);
        mysqli_set_charset($link, $this->charset);
        return $link;
    }
    protected function getTableName () {
        if (!empty($this->tableName)) {
            return $this->prefix . $this->tableName;
        }
        $className = get_class($this);
        $table = strtolower(substr($className, 0, -5));
        return $this->prefix . $table;
    }
    protected function initOptions () {
        $arr = ['where', 'table', 'field', 'order', 'group', 'having', 'limit'];
        foreach ($arr as $value) {
            if ($value === 'table') {
                $this->options[$value] = $this->tableName;
            } elseif ($value == 'field') {
                $this->options[$value] = '*';
            }
        }
    }
    public function field ($field) {
        if (!empty($field)) {
            if (is_string($field)) {
                if (is_string($field)) {
                    $this->options['field'] = $field;
                } elseif (is_array($field)) {
                    $this->options['field'] = join(',', $field);
                }
            }
        }
        return $this;
    }
}