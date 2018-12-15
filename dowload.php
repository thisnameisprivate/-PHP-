<?php

echo 'Getting file from service ... </br>';
$fp = fopen($localfile, 'wb');
if (! $success = ftp_fget($conn, $fp, $remotefile, FTP_BINARY)) {
    echo "Error: Could not download file";
    fclose($fp);
    ftp_quit($conn);
    exit;
}
fclose($fp);
echo "File download successfully";

//
$listing = ftp_nlist($conn, dirname($remotefile));
foreach ($listing as $filename) echo $filename;