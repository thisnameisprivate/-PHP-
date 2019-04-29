<?php

$address = 'localhost:8333';
if (!isset($argv[1])) {
    exit("use php client.php \$file_path\n");
}
$file_to_transfer = trim($argv[1]);
if (!is_file($file_to_transfer)) {
    exit("$file_to_transfer not exist\n");
}
$client = stream_socket_client($address, $errno, $errmsg);
if (!$client) {
    exit("$errmsg\n");
}
stream_set_blocking($client, 1);
$file_name = basename($file_to_transfer);
$file_data = file_get_contents($file_to_transfer);
$file_data = base64_encode($file_data);
$package_data = [
    'file_name' => $file_name,
    'file_daat' => $file_data
];
$package = json_encode($package_data) . "\n";
fwrite($client, $package);
echo fread($client, 8129), "\n";