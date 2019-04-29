<?php

class SearchReplaceStr {
    private $search = null;
    private $matchStr = null;
    public function __construct ($search, $matchStr) {
        if ($search == null || $matchStr == null) {
            return false;
        }
        $this->match = $search;
        $this->matchStr = $matchStr;
    }
    private function modifyFile ($filename) {
        $fileHandler = fopen($filename, 'r+');
        $modify = '';
        while (!feof($fileHandler)) {
            $source = fgets($fileHandler);
            $modify .= str_replace($this->search, $this->matchStr, $source);
        }
        if (!@unlink($filename)) {
            echo "delete file" . $filename . " success<br>";
        }
        $newFileHandler = fopen($filename, 'w');
        if (!feof($newFileHandler)) {
            fwrite($newFileHandler, $modify);
        }
        if (file_exists($filename)) {
            echo "create file  " . $filename . " success<br>";
        }
    }
    public function soruceDir ($dir) {
        $files = array();
        if ($dir != ".idea" && $dir != "SearchReplaceStr") {
            if (@$handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if (($file != ".." && $file != "." && $file != "searchReplaceStr.php" && $file != 'index.html')) {
                        if (is_dir($dir . "/" . $file)) {
                            $files[$file] = $this->sourceDir($dir . "/" . $file);
                        } else {
                            $this->modifyFile($dir . "/" . $file);
                        }
                    }
                }
            }
        }
    }
}

if (!is_null($_POST)) {
    $searchReplaceStr = new searchReplaceStr($_POST['target'], $_POST['strReplace']);
    print_r($searchReplaceStr->sourceDir('../'));
} else {
    echo "<script>
alert('Please input value.');
location.href = './index.html';
</script>";
}