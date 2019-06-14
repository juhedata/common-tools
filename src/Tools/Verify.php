<?php
/**
 * Created by PhpStorm.
 * User: owner
 * Date: 2019-06-13
 * Time: 17:42
 * Project Name: common-tools
 */
namespace ComTools;

class Verify
{
    /**
     * 校验微信内打开
     *
     * @param $userAgent
     * @return bool
     */
    public static function isWeChat($userAgent)
    {
        if (strpos($userAgent, 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 校验是否是移动设备
     *
     * @param $server
     * @return bool
     */
    public static function isMobileServer($server)
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($server['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        //此条摘自TPM智能切换模板引擎，适合TPM开发
        if (($client = (isset($server['HTTP_CLIENT']) ? $server['HTTP_CLIENT'] : '')) && 'PhoneClient' == $client) {
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (($wap = (isset($server['HTTP_VIA']) ? $server['HTTP_VIA'] : ''))) {
            //找不到为flase,否则为true
            if (stristr($wap, 'wap')) {
                return true;
            } else {
                return false;
            }
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if ($agent = (isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : '')) {
            $clientkeywords = [
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            ];
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($agent))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if ($accept = (isset($server['HTTP_ACCEPT']) ? $server['HTTP_ACCEPT'] : '')) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($accept, 'vnd.wap.wml') !== false) &&
                (strpos($accept, 'text/html') === false ||
                    (strpos($accept, 'vnd.wap.wml') < strpos($accept, 'text/html')))
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * 校验手机几号规则
     *
     * @param $mobile
     * @return bool
     */
    public static function isMobilePhone($mobile)
    {
        $mobilePreg = '/^1(3\d{1}|4[135789]{1}|5\d{1}|6[56]{1}|7[0135678]{1}|8\d{1}|9[189]{1})\d{8}$/';
        if (preg_match($mobilePreg, $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isMobilePhones($mobiles)
    {
        $mobilePreg ='/^1(3\d{1}|4[135789]{1}|5\d{1}|6[56]{1}|7[0135678]{1}|8\d{1}|9[189]{1})\d{8}(#+1(3\d{1}|4[135789]{1}|5\d{1}|6[56]{1}|7[0135678]{1}|8\d{1}|9[189]{1})\d{8}#{0,}){0,}$/';
        if (preg_match($mobilePreg, $mobiles)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 校验数字
     *
     * @param $val
     * @param $num
     * @return bool
     */
    public static function isInt($val, $num = 6)
    {
        if (preg_match("/^\d{{$num}}$/", $val)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 身份证号码简单校验
     *
     * @param $idCard
     * @return bool
     */
    public static function isIdCard($idCard)
    {
        $id = strtoupper($idCard);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";

        if (!preg_match($regx, $id)) {
            return false;
        }

        //身份证号码校验
        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else {
            //检查18位
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return false;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                $arr_ch = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b = (int)$id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];

                if ($val_num != substr($id, 17, 1)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    /**
     * 邮箱规则校验
     *
     * @param $email
     * @return bool
     */
    public static function isEmail($email)
    {
        $patrn = '/^[\da-z]+([-._]?[\da-z]+)*@[\da-z]+([-._]?[\da-z]+)*(\.[a-zA-Z]{2,3})+$/i';
        if (preg_match($patrn, $email) && strlen($email) < 64) {
            if (self::filterTempMail($email)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 临时邮箱校验
     *
     * @param $email
     * @return bool
     */
    protected static function filterTempMail($email)
    {
        $filters = [
            '@trbvm.com',
            '@chacuo.net',
            '@027168.com',
            '@mail.bccto.me',
            '@bccto.me',
            '@yopmail.com',
            '@guerrillamail',
            '@nowmymail',
            '@fakeinbox.com',
            '@mailnesia.com',
            '@mailinator',
            '@maildrop.cc',
            '@trashymail.com',
            '@mt2015.com',
            '@maildu.de',
            '@sharklasers.com',
            '@www.bccto.me',
            'bccto.me'
        ];
        foreach ($filters as $f) {
            if (stristr($email, $f)) {
                return false;
                break;
            }
        }
        return true;
    }

    /**
     * 金额
     *
     * @param $money
     * @param $limit
     * @return bool
     */
    public static function isMoney($money, $limit = 2)
    {
        if (preg_match('/^[0-9]{1,6}(.[0-9]{1,' . $limit . '})?$/', $money)) {
            return true;
        }
        return false;
    }

    /**
     * 校验中英文名称
     *
     * @param $name
     * @return bool
     */
    public static function isChinese($name)
    {
        if (preg_match('/^[0-9A-Za-z\x{4e00}-\x{9fa5}]{2,40}$/u', $name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 日期时间校验从2000年开始：格式2010-01-01
     *
     * @param $date
     * @return bool
     */
    public static function isDate($date)
    {
        $pattern = '#^(20|19)\d{2}-((0[1-9])|(1[0-2]))-(([012][0-9])|(3[01]))$#';
        if(preg_match($pattern,$date)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 日期时间校验从2000年开始：格式2010-01-01 11：11：11
     *
     * @param $date
     * @return bool
     */
    public static function isDateTime($date)
    {
        $pattern = '#^(20|19)\d{2}-((0[1-9])|(1[0-2]))-(([012][0-9])|(3[01]))\s((([01]\d)|(2[0-3])):[0-5]\d:[0-5]\d)$#';
        if(preg_match($pattern,$date)) {
            return true;
        } else {
            return false;
        }
    }
}