<?php

$height = 200;
$width = 200;
$image = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($image, 255, 255, 255);
$blue = imagecolorallocate($image, 0, 0, 255);
// draw on image
imagefill($image, 0, 0, $blue);
imageline($image, 0, 0, $width, $height, $white);
imagestring($image, 4, 50,150, 'Sales', $white);
header('Content-type:image/png');
imagepng($image);
imagedestroy($image);