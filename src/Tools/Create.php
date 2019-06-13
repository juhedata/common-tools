<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-06-13
 * Time: 17:42
 * Project Name: common-tools
 */
namespace ComTools;

class Create
{

    /**
     * 格式化生成字符串：订单号，或流水号
     *
     * @param string $prefix
     * @param string $format
     * @param int $randNum
     * @return string
     */
    public static function createOrderNoFormat($prefix = '', $format = 'YmdHis', $randNum = 6)
    {
        list($uSec, $sec) = explode(" ", microtime());
        $uSec = sprintf("%06d", $uSec * 1000000);
        $date = date($format, $sec) . $uSec;
        if ($randNum > 0) {
            $rand_start = 1;
            $rand_end = pow(10, ($randNum)) - 1;
            $rand = mt_rand($rand_start, $rand_end);
            $rand = sprintf("%0{$randNum}d", $rand);
        } else {
            $rand = "";
        }
        return $prefix . $date . $rand;
    }

    /**
     * 生成睡觉字符串
     *
     * @param int $length
     * @param bool $normal
     * @return bool|string
     */
    public static function randString($length = 128, $normal = true)
    {
        if ($normal) {
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+';
        }
        if (!is_int($length) || $length < 0) {
            return false;
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }

        return $string;
    }

    /**
     * 文档下载
     *
     * @param $file
     * @param $fileName
     * @param string $contentType
     * @return false|int
     */
    public static function fileToDownload($file, $fileName,$contentType = 'application/octet-stream')
    {
        // http headers
        $name = urlencode($fileName);
        header('Content-Type: '.$contentType);
        header("Accept-Ranges:bytes");
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-length: ' . filesize($file));
        header('Pragma: no-cache');

        // read file content and output
        return readfile($file);;
    }

    /**
     * 将出入字符串短小话，例如生成短链接等等
     *
     *
     * @param $input
     * @param $len
     * @return string
     */
    public static function shortStr($input,$len = 16)
    {
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $key = self::randString(16); //加盐
        $input = md5($key . $input);
        $i = mt_rand(0,16);
        $hashPiece = substr($input, $i , 16);
        //将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
        //此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
        $hex = hexdec($hashPiece) & 0x3fffffffffff;
        $encode = '';
        if(intval($len) && $len > 32) {
            $len = 32;
        }
        //生成{$len}位短网址
        for ($j = 0; $j < $len; $j++) {

            //将得到的值与0x0000003d,3d为61，即charset的坐标最大值
            $hex10 = $hex & 0x0000003d;
            if($hex10 > 61) {
                $index = $hex10 % 61;
            } elseif($hex10 == 0) {
                $index = mt_rand(0,61);
            } else{
                $index = $hex10;
            }
            $encode .= $charset[$index];

            //循环完以后将hex右移5位
            $hex = $hex >> 4;
        }
        return $encode ;
    }

    /**
     * 数组字符集转换
     *
     * @param $array
     * @param $to_encoding
     * @param string $from_encoding
     * @return array
     */
    public static function encodingArr($array, $to_encoding, $from_encoding = 'gb2312')
    {
        $encoded = [];

        foreach ($array as $key => $value) {
            $encoded[$key] = is_array($value) ? self::encodingArr($value, $to_encoding, $from_encoding) :
                mb_convert_encoding($value, $to_encoding, $from_encoding);
        }

        return $encoded;
    }

    /**
     * 替换字符中间部分为指定的符号
     *
     * @param string $str
     * @param string $symbol
     * @return mixed
     */
    public static function halfReplace($str = '', $symbol = '*')
    {
        $length = mb_strlen($str) / 2;

        return substr_replace($str, str_repeat($symbol, $length), floor($length / 2), $length);
    }

    /**
     * 将一个字符的中间1/3替换成星号
     * @param string $string
     * @return mixed
     */
    public static function replaceWithStar($string = '')
    {
        $newString = '';

        for ($i = 0; $i < mb_strlen($string); $i++) {
            $subString = mb_substr($string, $i, 1);

            if (($i >= mb_strlen($string)/3) && ($i < mb_strlen($string) - mb_strlen($string)/3)) {
                $newString .= '*';
            } else {
                $newString .= $subString;
            }
        }

        return $newString;
    }
}