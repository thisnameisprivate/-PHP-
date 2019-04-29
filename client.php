<?php

$address = 'localhost:8333';
if (! isset($argv[1])) {
    exit("use php client.php \ $file_path \n");
}
$file_to_transfer = trim($argv[1]);
if (! is_file($file_to_transfer)) {
    exit("$file_to_transfer not exist\n");
}
$client = stream_socket_client($address, $errnom, $errmsg);
if (!$client) {
    exit("$errmsg\n");
}
stream_set_blocking($client, 1);
$file_name = basename($file_to_transfer);
$name_len = strlen($file_name);
$file_data = file_get_contents($file_to_transfer);
$PACKAGE_HEAD_LEN = 5;
$package = pack('NC', $PACKAGE_HEAD_LEN + strlen($file_name) + strlen($file_data), $name_len) . $file_name . $file_data;
fwrite($client, $package);
echo fread($client, 8192), "\n";


class TextTransfer {
    public static function input ($recv_buffer) {
        $recv_len = strlen($recv_buffer);
        if ($recv_buffer[$recv_len - 1] == "\n") {
            return 0;
        }
        return strlen($recv_buffer);
    }
    public static function decode ($recv_buffer) {
        $package_data = json_decode(trim($recv_buffer), true);
        $file_name = $package_data['file_name'];
        $file_data = $package_data['file_data'];
        $file_data = base64_decode($file_data);
        return [
            'file_name' => $file_name,
            'file_data' => $file_data
        ];
    }
    public static function encode ($data) {
        return $data;
    }
}