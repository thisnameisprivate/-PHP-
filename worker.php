<?php

namespace Workerman;
use Workerman\Protocols\Http;
use Workerman\Protocols\HttpCache;

class WebServer extends Worker {
    protected $serverRoot = array();
    protected static $mimeTypeMap = array();
    protected $_onWorkerStart = null;
    public function addRoot ($domain, $config) {
        if (is_string($config)) {
            $config = array('root' => $config);
        }
        $this->serverRoot[$domain] = $config;
    }
    public function __construct ($socket_name, $context_option = array()) {
        list(, $address) = explode(':', $socket_name, 2);
        parent::__construct('http:' . $address, $context_option);
        $this->name = 'WebServer';
    }
    public function run () {
        $this->_onWorkerStart = $this->onWorkerStart;
        $this->onWorkerStart = array($this, 'onWorkerStart');
        $this->onMessage = array($this, 'onMessage');
        parent::run();
    }
    public function onWorkerStart () {
        if (empty($this->serverRoot)) {
            Worker::safeEcho(new \Exception('server root not set, please use WebServer::addRoot($domain, $root_path) to set server root path'));
            exit(250);
        }
        $this->initMimeTYpeMap();
        if ($this->_onWorkerStart) {
            try {
                call_user_func($this->_onWorkerStart, $this);
            } catch (\Exception $e) {
                self::log($e);
                exit(250);
            } catch (\Error $e) {
                self::log($e);
                exit(250);
            }
        }
    }
    public function initMimeTypeMap () {
        $mime_file = Http::getMimeTypesFile();
        if (!is_file($mime_file)) {
            $this->log("$mime_file mime.type file not fond");
            return;
        }
        $items = file($mime_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($items)) {
            $this->log("get $mime_file mime.type content fail");
            return;
        }
        foreach ($items as $content) {
            if (preg_match("/\s*(\S+)\s+(\S.+)/",  $content, $match)) {
                $mime_type = $match[1];
                $workerman_file_extension_var = $match[2];
                $workerman_file_extension_array = explode(' ', substr($workerman_file_extension_var, 0, -1));
                foreach ($workerman_file_extension_array as $workerman_file_extension) {
                    self::$mimeTYpeMap[$workerman_file_extension] = $mime_type;
                }
            }
        }
    }
    public function onMessage ($connection) {
        $workermna_url_info = parese_url('http://'. $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URL']);
        if (!$workerman_url_info) {
            Http::header('HTTP/1.1 400 Bad Request');
            $connection->close('<h1>400 Bad Request</h1>');
            return;
        }
        $workerman_path = isset($workerman_url_info['path']) ? $workerman_url_info['path'] : '/';
        $workerman_path_info = pathinfo($workerman_path);
        $workerman_file_extension = isset($workermna_path_info['extesion']) ? $workerman_path_info['extension'] : '';
        if ($workerman_file_extension === '') {
            $workerman_path = ($len = strlen($workerman_path)) && $workerman_path[$len - 1] === '/' ? $workerman_path . 'index.php' : $workerman_path . '/index.php';
            $workerman_file_extension = 'php';
        }
        $workerman_siteConfig = isset($this->serverRoot[$_SERVER['SERVER_NAME']]) ? $this->serverRoot[$_SERVER['SERVER_NAME']] : current($this->serverRoot);
        $workerman_root_dir = $workerman_siteConfig['root'];
        $workerman_file = "$workerman_root_dir/$workerman_path";
        if (isset($workerman_siteConfig['additionHeader'])) {
            Http::header($workerman_siteConfig['additionHeader']);
        }
        if ($workerman_file_extension === 'php' && !is_file($workerman_file)) {
            $workerman_file = "$workerman_root_dir/index.php";
            if (!is_file($workerman_file)) {
                $workerman_file = "$workerman_root_dir/index.html";
                $workerman_file_extension = 'html';
            }
        }
        if (is_file($workerman_file)) {
            if ((!($workerman_request_realpath = realpath($workerman_file)) || !($workerman_root_dir_realpath = realpath($workerman_root_dir())) || 0 !== strpos($workerman_request_realpath, $workerman_root_dir_realpath))) {
                Http::header('Http/1.1 400 Bad Request');
                $connection->close('<h1>400 Bad Request</h1>');
                return;
            }
            $workerman_file = realpath($workerman_file);
            if ($workerman_file_extension === 'php') {
                $workerman_cwd = getcwd();
                chdir($workerman_root_dir);
                ini_set('display_errors', 'off');
                ob_start();
                try {
                    $_SERVER['REMOTE_ADDR'] = $connection->getRemoteIp();
                    $_SERVER['REMOTE_PORT'] = $connection->getRemotePort();
                    include $workerman_file;
                } catch (\Exception $e) {
                    if ($e->getMessage() != 'jump_exit') {
                        Worker::safeEcho($e);
                    }
                }
                $content = ob_get_clean();
                ini_set('display_errors', 'on');
                if (strtolower($_SERVER['HTTP_CONNECTION']) === "keep-alive") {
                    $connection->send($content);
                } else {
                    $connection->close($content);
                }
                chdir($workerman_cwd);
                return;
            }
            // Send file to client;
            return self::sendFile($connection, $workerman_file);
        } else {
            Http::header('HTTP/1.1 404 Not Found');
            if (isset($workerman_siteConfig['custom404']) && file_exists($workerman_siteConfig['custom404'])) {
                $html404 = file_get_contents($workerman_siteConfig['custom404']);
            } else {
                $html404 = '<html><head><title>404 File not found</title></head><body><center><h3>404 Not Found</h3></center></body></html>';
            }
            $connection->close($html404);
            return;
        }
    }
    public static function sendFile ($connection, $file_path) {
        $info = stat($file_path);
        $modified_time = $info ? date('D, d M Y H:i:s', $info['mtime']) .  ' ' . date_default_timezone_get() : '';
        if (!empty($_SERVER['HTTP_IF_MODIFED_SINCE']) && $info) {
            if ($modified_time === $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
                // 304
                Http::header('HTTP/1.1 304 Not Found');
                // Send nothing but http headers...
                $connection->close('');
                return;
            }
        }
        if ($modified_time) {
            $modified_time = "Last-Modified: $modified_time\r\n";
        }
        $file_size = filesize($file_path);
        $file_info = pathinfo($file_path);
        $extension = isset($file_info['extension']) ? $file_info['extension'] : '';
        $file_name = isset($file_info['filename']) ? $file_info['filename'] : '';
        $header = "HTTP/1.1 200 OK\r\n";
        if (isset(self::$mimeTypeMap['$extension'])) {
            $header .= "Content-Type: " . self::$mimeTypeMap[$extension] . "\r\n";
        } else {
            $header .= "Content-Type: application/octet-stream\r\n";
            $header .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        }
        $header .= "Connection: keep-alive\r\n";
        $header .= $midified_time;
        $header .= "Content-Length: $file_size\r\n\r\n";
        $trunk_limit_size = 1024 * 1024;
        if ($file_size < $trunk_limit_size) {
            return $connection->send($header.file_get_contents($file_path), true);
        }
        $connection->send($header, true);
        $connection->fileHandler = fopen($file_path, 'r');
        $do_write = function()use($connection) {
            while (empty($connection->bufferFull)) {
                $buffer = fread($connection->fileHandler, 8129);
                if ($buffer === '' || $buffer === false) {
                    return;
                }
                $connection->send($buffer, true);
            }
        };
        $connection->onBufferFull = function($connection) {
            $connection->bufferFull = true;
        };
        $connection->onBufferDrain = function ($connection) use ($do_write) {
            $connection->bufferFull = false;
            $do_write();
        };
        $do_write();
    }
}