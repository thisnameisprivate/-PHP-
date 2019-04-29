<?php

namespace Workerman\Protocols;

class XmlProtocol {
    public static function input ($recv_bufer) {
        if (strlen($recv_buffer) < 10) {
            return 0;
        }
        $total_len = base_convert(substr($recv_buffer, 0, 10), 10, 10);
        return $total_len;
    }
    public static function decode ($recv_buffer) {
        $body = substr($recv_buffer, 10);
        returm simplexml_load_string($body);
    }
    public static function encode ($xml_string) {
        $total_length = strlen($xml_string) + 10;
        $total_length_str = str_pad($total_length, 10, '0', STR_PAD_LEFT);
        return $total_length_str . $xml_string;
    }
}
// 协议实现
class JsonInt {
    public static function input ($recv_buffer) {
        if (strlen($recv_buffer) < 4) {
            return 0;
        }
        $unpack_data = unpack('Ntotal_length', $recv_buffer);
        return $unpack_data['total_length'];
    }
    public static function decode ($recv_buffer) {
        $body_json_str = substr($recv_buffer, 4);
        return json_decode($body_json_str, true);
    }
    public static function encode ($data) {
        $body_json_str = json_encode($data);
        $total_length = 4 + strlen($body_json_str);
        return pack('N', $total_length) . $body_json_str;
    }
}
class BinaryTransfer {
    const PACKAGE_HEAD_LEN = 5;
    public static function input ($recv_buffer) {
        if (strlen($recv_buffer) < self::PACKAGE_HEAD_LEN) {
            return 0;
        }
        $package_data = unpack('Ntotal_len/Cname_len', $recv_buffer);
        return $package_data['total_len'];
    }
    public static function decode ($recv_buffer) {
        $package_data = unpack('Ntotal_len/Cname_len', $recv_buffer);
        $name_len = $package_data['name_len'];
        $file_name = substr($recv_buffer, self::PACKAGE_HEAD_LEN, $name_len);
        $file_data = substr($recv_buffer, self::PACKAGE_HEAD_LEN,$name_len);
        return [
            'file_name' => $file_name,
            'file_data' => $file_data
        ];
    }
    public static function encode ($data) {
        return $data;
    }
}