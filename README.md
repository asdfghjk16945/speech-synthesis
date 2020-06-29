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
        'fileRoot'=>'/',
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


