<?php
htmlspecialchars(); // 将html中有特殊字符含义的字符转换为等价的html实体,例如" < ";
str_replace(); // string replace.
$string = ' 123 ';
trim($string); // 清除字符串两边的空格
$str = 'Thin is \tan example\nstring';
$tok = strtok($str, ' \n\t');
while ($tok !== false) {
    echo "Word=$tok<br/>";
    $tok = strtok(" \n\t");
}
substr($target, $start, $end);
$str2 = 'kexinfrdy@126.com';
print_r(strstr($str2, '@'));
echo "</br>";
print_r(strstr($str2, '@', true));
echo "</br>";
print_r(strpos($str2, '@'));
echo "</br>";
print_r(str_replace('@', '#', $str2));
echo "implode(substr_replace());";
$input = array('A: XXX', 'B: XXX', 'C: XXX');
print_r(implode(';', substr_replace($input, 'YYY', 3, 3)));
$arr = implode(';', substr_replace($input, 'YYY', 3, 3));
echo "<br/>";
print_r(explode(';', $arr));
echo "</br>";

/*
 *
 *      The sample above is only true on some platforms that only use a simple 'C' locale,
 *   where individual bytes are considered as complete characters that are converted to lowercase before being differentiated.
 *   Other locales (see LC_COLLATE and LC_ALL) use the difference of collation order of characters,
 *   where characters may be groups of bytes taken from the input strings,
 *   or simply return -1, 0, or 1 as the collation order is not simply defined by comparing individual characters but by more complex rules.
 *   Don't base your code on a specific non null value returned by strcmp() or strcasecmp(): it is not portable. Just consider the sign of the result and be sure to use the correct locale!
 *
 * */

// PCRE 正则表达式
"/^$/";
auto_prepend_file;
auot_append_file;