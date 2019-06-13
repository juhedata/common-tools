## 常规校验和生成集合

#### 常规校验
```
Verify::isWeChat($userAgent);//校验是否是微信内打开

Verify::isMobileServer($server);//校验是否是移动设备访问

Verify::isMobilePhone($mobile);//校验手机号格式

Verify::isMobilePhones($mobiles);//校验手机号格式(多手机号)

Verify::isInt($val,$len);//校验整数

Verify::isIdCard($idCard);//校验身份证号

Verify::isEmail($email);//校验邮箱，排除临时邮箱

Verify::isMoney($money, $limit );//金额格式校验，保留小数位$limit

Verify::isChinese($val);//字母数字中文校验


```

#### 常用生成
```
    //格式化生成,format=YmdHis,randNum:补充随机数
   Create::createOrderNoFormat($prefix, $format, $randNum) ;
    
   Create::randString($length, $normal) ;//生成随机字符串

   Create::fileToDownload($file, $fileName,$contentType) ;//文件下载

   Create::shortStr($input,$len) ;//生成短链
   
   Create::encodingArr($array, $to_encoding, $from_encoding ) ;//数组字符集转换
   
   Create::halfReplace($string, $symbol ) ;//将字符串后半部分替换成指定字符
   
   Create::replaceWithStar($str);//将字符串中间一段替换成*

```