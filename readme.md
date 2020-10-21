## 常规校验和生成集合
```
//composer安装

composer require juhedata/common-tools
```
#### 常规校验
```
Verify::isWeChat($userAgent);//校验是否是微信内打开

Verify::isMobileServer($server);//校验是否是移动设备访问

Verify::isMobilePhone($mobile,[$strict = false]);//校验手机号格式

Verify::isMobilePhones($mobiles,[$strict = false]);//校验手机号格式(多手机号)

Verify::isInt($val,$len);//校验整数

Verify::isIdCard($idCard);//校验身份证号

Verify::isEmail($email);//校验邮箱，排除临时邮箱

Verify::isMoney($money, $limit );//金额格式校验，保留小数位$limit

Verify::isChinese($val);//字母数字中文校验


```

#### 常用生成
```
    //格式化生成,format=YmdHis,randNum:补充随机数 $sec:是否使用毫秒默认true
   Create::createOrderNoFormat($prefix, $format, $randNum,$sec) ;
    
   Create::randString($length, $normal) ;//生成随机字符串

   Create::fileToDownload($file, $fileName,$contentType) ;//文件下载

   Create::shortStr($input,$len) ;//生成短链
   
   Create::encodingArr($array, $to_encoding, $from_encoding ) ;//数组字符集转换
   
   Create::halfReplace($string, $symbol ) ;//将字符串后半部分替换成指定字符
   
   Create::replaceWithStar($str, $symbol);//将一个字符的中间1/3替换成指定字符

   Create::moneyToString($money);//将数值金额转换为中文大写金额

   Create::shortTime($string);//当年时间短日期

```
