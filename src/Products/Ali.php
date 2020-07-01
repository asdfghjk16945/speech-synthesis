<?php
/**
 * 阿里云的语音合成
 */


namespace SpeechSynthesis\Products;


use SpeechSynthesis\Contracts\AudioSynthesisStrategy;

class Ali implements AudioSynthesisStrategy
{
    /**
     * @var array
     * 阿里云配置
     */
    private $config;

    /**
     * Ali constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 默认300字符内，超过会被截断
     * 可以配置longText=true
     * @param string $fileName
     * @return array
     */
    public function textToAudio(string $fileName): array
    {
        if(empty($this->config['fileRoot'])){
            return ['code'=>0, 'msg' => '文件地址不能为空', 'data'=>['audio_name'=>'']];
        }
        if(!is_dir($this->config['fileRoot'])){
            @mkdir($this->config['fileRoot'], 0755, true);
        }

        // windows系统需要把中文编码方式改为GBK
        if(DIRECTORY_SEPARATOR === '\\'){
            $fileName = iconv('UTF-8','GBK',$fileName);
        }

        // 完整文件地址
        $fileName = $fileName.'.'.$this->config['format'];
        $fullFileName = $this->config['fileRoot'] != '/' ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . $fileName : $fileName;

        // 文件冲突，重新命名一下，后缀加_new
        if(file_exists($fullFileName)){
            $fullFileName = $this->config['fileRoot'] != '/'
                ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . md5($fileName).'_new'.'.'.$this->config['format']
                : md5($fileName).'_new'.'.'.$this->config['format'];
        }
        if($this->config['longText'] === true){
            return $this->batch();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 只支持post
        curl_setopt($ch, CURLOPT_POST, 1);

        // 请求体
        $json = json_encode($this->createMsg(), JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 设置请求头
        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($json)
        ];
        if($this->config['X-NLS-Token']){
            $header['X-NLS-Token'] = $this->config['X-NLS-Token'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $output = curl_exec($ch);
        $curlError = curl_close($ch);

        // curl执行失败
        if($output === false) {
            return ['code'=>0,'msg'=>$curlError,'data'=>['audio_name'=>'']];
        }
        $res = json_decode($output, true);
        if($res){
            return ['code'=>0,'msg'=>$res['status'].'_'.$res['message'],'data'=>['audio_name'=>'']];
        }else{
            $writeRes = file_put_contents($fullFileName, $output);
            // 写入失败
            if($writeRes === false){
                return ['code'=>0,'msg'=>'合成失败','data'=>['audio_name'=>'']];
            }
            return ['code' => 1, 'msg' => '合成成功', 'data' => ['audio_name' => $fileName]];
        }
    }

    /**
     * 长文本
     * @return array
     */
    public function batch()
    {
        return ['code'=>0,'msg'=>'暂不支持','data'=>['audio_name'=>'']];
    }

    /**
     * 设置请求参数
     * @return array
     */
    public function createMsg()
    {
        return [
            'appkey'=>$this->config['appkey'],
            'text'=>$this->config['draftContent'],
            'token'=>$this->config['token'],
            'format'=>$this->config['format'],
            'sample_rate'=>$this->config['sample_rate'],
            'voice'=>$this->config['voice'],
            'volume'=>$this->config['volume'],
            'speech_rate'=>$this->config['speech_rate'],
            'pitch_rate'=>$this->config['pitch_rate']
        ];
    }
}