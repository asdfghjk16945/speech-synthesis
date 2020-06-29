<?php


namespace SpeechSynthesis\Products;


use SpeechSynthesis\Contracts\AudioSynthesisStrategy;
use WebSocket\Client;

class Iflytek implements AudioSynthesisStrategy
{
    /**
     * @var array
     * 科大讯飞配置
     */
    private $config;

    /**
     * Iflytek constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct()
    {
        $key = __CLASS__;
        $len = strripos($key,'\\');
        if( $len !== false){
            $key = substr('peechSynthesis\Products\Iflytek', $len);
        }
        $configClass = Config::getInstance();
        $this->config = $configClass->getConfig($key);
        $this->config['time'] = date('D, d M Y H:i:s', strtotime('-8 hours')) . ' GMT';
    }

    /**
     * 生成音频文件
     * @param $fileName
     * @return array
     * code , msg, data
     */
    public function textToAudio(string $fileName):array
    {
        if(empty($this->config['fileRoot'])){
            return ['code'=>0, 'msg' => '文件地址不能为空', 'data'=>['audio_name'=>'']];
        }
        if(!is_dir($this->config['fileRoot'])){
            @mkdir($this->config['fileRoot'], 0755, true);
        }

        // 设置文件名
        $ext = $this->config['sfl'] ? '.mp3' : '.pcm';
        // windows系统需要把中文编码方式改为GBK
        if(DIRECTORY_SEPARATOR === '\\'){
            $fileName = iconv('UTF-8','GBK',$fileName);
        }
        $fileName = $fileName . $ext;

        // 完整文件地址
        $fullFileName = $this->config['fileRoot'] == '\\' ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . $fileName : $fileName;

        // 文件冲突，重新命名一下，后缀加_new
        if(file_exists($fullFileName)){
            $fullFileName = $this->config['fileRoot'] == '\\'
                ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . md5($fileName).'_new'.$ext
                : md5($fileName.time()).'_new'.$ext;
        }

        /**
         * 开始合成
         */
        try {
            // 链接的url
            $url = $this->createUrl();
            // 要发送的消息
            $message = $this->createMsg();

            // 建立链接
            $client = new Client($url,['timeout'=>60]);
            // 发送请求
            $client->send(json_encode($message, true));
            //需要以追加的方式进行写文件
            $audioFile = fopen($fullFileName, 'ab');
            // 科大讯飞会分多次发送消息
            do {
                //持续接收消息
                $response = $client->receive();
                $response = json_decode($response, true);

                // 不成功会返回错误   code为0是成功
                if ($response['code']) {
                    return ['code' => 0, 'msg' => $response['code'].'_'.$response['message'], 'data'=>['audio_name'=>'']];
                }
                //返回的音频需要进行base64解码
                $audio = base64_decode($response['data']['audio']);
                // 追加
                fwrite($audioFile, $audio);

            } while ($response['data']['status'] !== 2);// status=2代表发送完成

            fclose($audioFile);

            return ['code' => 1, 'msg' => '合成成功', 'data' => ['audio_name' => $fileName]];

        } catch (\Exception $e) {

            return ['code' => 0, 'msg' => $e->getMessage(), 'data'=>['audio_name'=>'']];

        } finally {
            // 不管怎样，都需关掉连接
            $client->close();
        }
    }

    /**
     * 创建参数
     */
    private function createMsg():array
    {
        return [
            'common' => [
                'app_id' => $this->config['app_id'],
            ],
            'business' => [
                'aue' => $this->config['aue'],
                'sfl' => $this->config['sfl'],
                'auf' => $this->config['auf'],
                'vcn' => $this->config['vcn'],
                'speed' => $this->config['speed'],
                'volume' => $this->config['volume'],
                'pitch' => $this->config['pitch'],
                'tte' => $this->config['tte'],
                'ent' => $this->config['ent'],
                'rdn' => $this->config['rdn'],
                'ram' => $this->config['ram'],
                'reg' => $this->config['reg'],
                'bgs' => $this->config['bgs']
            ],
            'data' => [
                'status' => 2,
                'text' => base64_encode($this->config['draftContent']),
            ],
        ];
    }

    /**
     * 创建url
     * @return string
     */
    private function createUrl():string
    {
        // 生成签名
        $urlInfo = parse_url($this->config['url']);
        $signatureOrigin = 'host: ' . $urlInfo['host'] . "\n";
        $signatureOrigin .= 'date: ' . $this->config['time'] . "\n";
        $signatureOrigin .= 'GET ' . $urlInfo['path'] . ' HTTP/1.1';

        $signatureSha = hash_hmac('sha256', $signatureOrigin, $this->config['api_secret'], true);
        $signatureSha = base64_encode($signatureSha);

        $authorizationOrigin = 'api_key="' . $this->config['api_key'] . '", algorithm="' . $this->config['algorithm'] . '", ';
        $authorizationOrigin .= 'headers="host date request-line", signature="' . $signatureSha . '"';
        $authorization = base64_encode($authorizationOrigin);

        // 生成Url
        $signUrl = $this->config['url'] . '?' . 'authorization=' . $authorization . '&date='
            . urlencode($this->config['time']) . '&host=' . $urlInfo['host'];

        return $signUrl;
    }
}