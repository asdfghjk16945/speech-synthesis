<?php


namespace SpeechSynthesis\Products;


class Config
{
    static private $instance;
    public function __construct()
    {

    }
    public static function getInstance()
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @var array
     */
    protected static $config = [
        'Iflytek'=>[
            'fileRoot'=>'/',
            'url'=>'wss://tts-api.xfyun.cn/v2/tts',
            'algorithm'=>'hmac-sha256',
            'time'=>'',

            'app_id' => '***',
            'api_secret'=>'***',
            'api_key'=>'***',
            'aue' => 'lame',
            'sfl' => 1,
            'auf' => 'audio/L16;rate=16000',
            'vcn' => 'xiaoyan',
            'speed' => 50,
            'volume' => 100,
            'pitch' => 50,
            'tte' => 'UTF8',
            'ent' => 'intp65',
            'rdn' => '0',
            'ram' => '0',
            'reg' => '2',
            'bgs' => 0,

            'status' => 2,
            'draftContent' => '测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。
            测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。
            测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。
            测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。
            测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。
            测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。测试一下哈哈哈，测试一下哈哈哈。',
        ]
    ];

    /**
     * @param $type
     * @return array
     */
    public function getConfig(string $type): array
    {
        return self::$config[$type];
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        self::$config = array_replace_recursive(self::$config, $config);
    }
}