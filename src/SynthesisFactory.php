<?php


namespace SpeechSynthesis;


use SpeechSynthesis\Contracts\AudioSynthesisStrategy;
use SpeechSynthesis\Products\Config;
use SpeechSynthesis\Products\Iflytek;

class SynthesisFactory
{
    private $synthesisRes = [];

    public function __construct($synthesisType,$filename, $config = [])
    {
        // 增加配置
        if($config){
            $configClass = Config::getInstance();
            $configClass->setConfig($config);
        }
        $synthesisType = 'SpeechSynthesis\Products\\' . $synthesisType;
        $synthesisClass = new $synthesisType();
        $synthesisRes = [];
        if($synthesisClass instanceof AudioSynthesisStrategy){
            $synthesisRes = $synthesisClass->textToAudio($filename);
        }

        $this->synthesisRes = $synthesisRes;
    }

    /**
     * 文件名
     * @return mixed
     */
    public function getSpeechFile():string
    {
        return $this->synthesisRes['data']['audio_name'];
    }

    /**
     * 返回错误信息
     * false代表没有错误
     * @return bool|mixed|string
     */
    public function getErrorMsg()
    {
        if(!$this->synthesisRes){
            return '出错了，没有任何数据';
        }
        if($this->synthesisRes['code'] === 0){
            return $this->synthesisRes['msg'];
        }
        return false;
    }
}