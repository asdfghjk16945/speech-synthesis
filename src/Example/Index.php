<?php


namespace SpeechSynthesis\Example;


use SpeechSynthesis\SynthesisFactory;

class Index
{
    public function test(){
        $config = [
            'Iflytek'=>[
                'fileRoot'=>'/'
            ]
        ];
        $syn = new SynthesisFactory('Iflytek','测试的名字',$config);
        $msg = $syn->getErrorMsg();
        $speechFile = $syn->getSpeechFile();
    }
}