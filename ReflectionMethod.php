<?php

// 使用 ReflectionClass::getMethods() 来获取对象的所有方法并用ReflectionMethod来检查

/*
$prod_class = new ReflectionClass('CdProduct');
$methods = $prod_class->getMethods();
foreach ($methods as $method) {
    print $methodData($method);
    print "\n---\n";
}
function methodData (ReflectionMethod $method) {
    $details = "";
    $name = $method->getName();
    if ($method->isUserDefined()) {
        $details .= "$name is user defined\n";
    }
    if ($method->isAbstract()) {
        $details .= "$name is abstract\n";
    }
    if ($method->isPublic()) {
        $details .= "$name is public\n";
    }
    if ($method->isProtected()) {
        $details .= "$name is protected\n";
    }
    if ($method->isPrivate()) {
        $details .= "$name is private\n";
    }
    if ($method->isStatic()) {
        $details .= "$name is static\n";
    }
    if ($method->isFinal()) {
        $details .= "$name is final\n";
    }
    if ($metod->isConstructor()) {
        $details .= "$name is constructor\n";
    }
    if ($method->returnsReference()) {
        $details .= "$name returns a reference (as opposed to a value)\n";
    }
    return $details;
}

  */
/*
$con_config = [
    'user' => 'root',
    'pwd'  => '',
    'db'   => 'visit',
    'host' => 'localhost'
];
$conn = new PDO("mysql:host=" . $con_config['localhost'] . ';dbname=' . $con_config['db'], $con_config['user'], $con_config['pwd']);
$conn->query("set names 'utf8'");
$sql = "SELECT * FROM alldiseases WHERE id = ?";
$bindValue = 2;
$stmt = $conn->prepare($sql);
$stmt->bindValue(1, $bindValue);
$stmt->execute();
while ($row_result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row_result);
}

 */

header("Content-type:text/html; charset=utf-8");
$mysql = new mysqli("localhost", 'root', '', 'visit');
$mysql->query("SET NAMES UTF8");
$result = $mysql->query("SELECT * FROM alldiseases");
while ($row = $result->fetch_assoc()) {
    $district[$row['id']] = ['id' => $row['id'], 'pid' => $row['pid'], 'name' => $row['name']];
}
function arrayToTree (Array $items) {
    foreach ($items as $item) {
        $item[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
    }
    return isset($items[0]['son']) ? $items[0]['son'] : [];
}
