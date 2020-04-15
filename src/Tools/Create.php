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
     * @param string $prefix 前缀
     * @param string $format 格式
     * @param bool $sec 单号是否添加毫秒
     * @param int $randNum 随机数长度
     * @return string
     */
    public static function createOrderNoFormat($prefix = '', $format = 'YmdHis', $randNum = 6, $sec= true)
    {
        if($sec) {
            list($uSec, $sec) = explode(" ", microtime());
            $uSec = sprintf("%06d", $uSec * 1000000);
            $date = date($format, $sec) . $uSec;
        } else {
            $date = date($format) ;
        }

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
     * 生成随机字符串
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
     * 将输入字符串短小化，例如生成短链接等等
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
     *
     * @param string $string
     * @param string $symbol
     * @return mixed
     */
    public static function replaceWithStar($string = '', $symbol = '*')
    {
        $newString = '';

        for ($i = 0; $i < mb_strlen($string); $i++) {
            $subString = mb_substr($string, $i, 1);

            if (($i >= mb_strlen($string)/3) && ($i < mb_strlen($string) - mb_strlen($string)/3)) {
                $newString .= $symbol;
            } else {
                $newString .= $subString;
            }
        }

        return $newString;
    }

    /**
     * 将数值金额转换为中文大写金额
     *
     * @param int $money 金额(支持到厘)
     *
     * @return mixed 中文大写金额
     */
    public static function moneyToString($money)
    {
        $digits = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        $radices =['', '拾', '佰', '仟', '万', '亿'];
        $bigRadices = ['', '万', '亿','万'];
        $decimals = ['角', '分','厘'];
        $cn_dollar = '元';
        $cn_integer = '整';
        $num_arr = explode('.', $money);
        $int_str = isset($num_arr[0]) ? $num_arr[0] : '';
        $float_str = isset($num_arr[1]) ? $num_arr[1] : '';
        $outputCharacters = '';
        if ($int_str) {
            $int_len = strlen($int_str);
            $zeroCount = 0;

            for ($i = 0; $i < $int_len; $i++) {
                $p = $int_len - $i - 1;
                $d = substr($int_str, $i, 1);
                $quotient = $p / 4;
                $modulus = $p % 4;

                if ($d == "0") {
                    $zeroCount++;
                } else {
                    if ($zeroCount > 0)
                    {
                        $outputCharacters .= $digits[0];
                    }
                    $zeroCount = 0;
                    $outputCharacters .= $digits[$d] . $radices[$modulus];
                }
                if ($modulus == 0 && $zeroCount < 4) {
                    $outputCharacters .= $bigRadices[$quotient];
                    $zeroCount = 0;
                }
            }
            $outputCharacters .= $cn_dollar;
        }
        if ($float_str) {
            $float_len = strlen($float_str);
            for ($i = 0; $i < $float_len; $i++) {
                $d = substr($float_str, $i, 1);
                if ($d != "0") {
                    $outputCharacters .= $digits[$d] . $decimals[$i];
                }
            }
        }
        if ($outputCharacters == "") {
            $outputCharacters = $digits[0] . $cn_dollar;
        }
        if ($float_str) {
            $outputCharacters .= $cn_integer;
        }
        return $outputCharacters;
    }

    /**
     * 当年时间短日期
     *
     * @param $string
     * @return false|string
     */
    public static function shortTime($string)
    {
        $year = date('Y', strtotime($string));
        if (date('Y') == $year) {
            // 如果是当年的日期，省略年份
            return date('m-d H:i:s', strtotime($string));
        } else {
            return $string;
        }
    }

    /**
     * @param  $start
     * @param $end
     * @return float|int
     */
    public static function getDiffDays($start,$end)
    {
        $second1 = strtotime($start);
        $second2 = strtotime($end);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }
}
