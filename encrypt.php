<?php


//加密函数
function lock_url($txt, $key = 'www.jb51.net')
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $nh = rand(0, 64);
    $ch = $chars[$nh];
    $mdKey = md5($key . $ch);
    $start = $nh % 8;
    $mdKey = substr($mdKey, $start, $start + 7);
    $txt = base64_encode($txt);
    $tmp = '';
    $k = 0;
    $txtLen = strlen($txt);
    $mdKeyLen = strlen($mdKey);
    for ($i = 0; $i < $txtLen; $i++) {
        $k = $k == $mdKeyLen ? 0 : $k;
        $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
        $tmp .= $chars[$j];
    }
    return urlencode($ch . $tmp);
}
//解密函数
function unlock_url($txt, $key = 'www.jb51.net')
{
    $txt = urldecode($txt);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $ch = $txt[0];
    $nh = strpos($chars, $ch);
    $mdKey = md5($key . $ch);
    $start = $nh % 8;
    $mdKey = substr($mdKey, $start, $start + 7);
    $txt = substr($txt, 1);
    $tmp = '';
    $k = 0;
    $txtLen = strlen($txt);
    $mdKeyLen = strlen($mdKey);
    for ($i = 0; $i < $txtLen; $i++) {
        $k = $k == $mdKeyLen ? 0 : $k;
        $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
        while ($j < 0) $j += 64;
        $tmp .= $chars[$j];
    }
    return base64_decode($tmp);
}


$str = lock_url('测试');
echo $str;
echo '<br>';
echo unlock_url($str);

