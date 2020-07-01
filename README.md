# speech-synthesis
支持阿里、百度、腾讯、科大讯飞语音合成接口

composer下载
```
composer require speech-synthesis/speech-synthesis
```

科大讯飞的使用示例：
```
$config = [
    'Iflytek'=>[ 
        'fileRoot'=>'/uploads',// 文件保存的路径
        'app_id' => '5e840bb8',
        'api_secret'=>'0b0f729b9e5302ba69f5b91aba91948f',
        'api_key'=>'09536ff9b5aeb8a4ccb51121b7844092',
    ]
];
$syn = new SynthesisFactory('Iflytek','此处是文件名',$config);
$msg = $syn->getErrorMsg();
if($msg){
    echo $msg;// 错误信息
    return false;
}
    $speechFile = $syn->getSpeechFile();
```
阿里云使用示例：

1、获取access_token
```
composer require alibabacloud/sdk
```
阿里官方demo
```
<?php
require __DIR__ . '/vendor/autoload.php';
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
/**
 * 第一步：设置一个全局客户端
 * 使用阿里云RAM账号的AccessKey ID和AccessKey Secret进行鉴权
 */
AlibabaCloud::accessKeyClient(
            "<your-access-key-id>",
            "<your-access-key-secret>")
            ->regionId("cn-shanghai")
            ->asDefaultClient();
try {
    $response = AlibabaCloud::nlsCloudMeta()
                            ->v20180518()
                            ->createToken()
                            ->request();
    print $response . "\n";
    $token = $response["Token"];
    if ($token != NULL) {
        print "Token 获取成功：\n";
        print_r($token);
    }
    else {
        print "token 获取失败\n";
    }
} catch (ClientException $exception) {
    // 获取错误消息
    print_r($exception->getErrorMessage());
} catch (ServerException $exception) {
    // 获取错误消息
    print_r($exception->getErrorMessage());
}
```
合成
```$xslt
$config = [
    'Iflytek'=>[
        'fileRoot'=>'/',
        "app_id"=>"5e840bb8",
        "api_secret"=>"0b0f729b9e5302ba69f5b91aba91948f",
        "api_key"=>"09536ff9b5aeb8a4ccb51121b7844092",
    ],
    'Ali'=>[
        'fileRoot'=>'/',
        'appkey'=>'****',
        'token'=>'****',
        'format'=>'mp3',
        'draftContent'=>'对于一个在北平住惯的人，像我，冬天要是不刮风，。'
//        ....
    ]
];
$syn = new SynthesisFactory('Ali','测试的名字',$config);
$msg = $syn->getErrorMsg();// 错误信息
if($msg){
    $speechFile = $syn->getSpeechFile();// 文件
}
```


