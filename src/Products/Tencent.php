<?php


namespace SpeechSynthesis\Products;


use SpeechSynthesis\Contracts\AudioSynthesisStrategy;

class Tencent implements AudioSynthesisStrategy
{
    /**
     * @var array
     * 腾讯云配置
     */
    private $config;

    /**
     * 获取配置
     * Tencent constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 获取签名鉴权
     * reqArr 请求原始数据
     * method 请求方式 POST
     * domain 请求域名
     * path 请求路径
     * secretKey 用户秘钥
     * output str 鉴权签名signature
     */
    function createSign($reqArr, $method, $domain, $path, $secretKey) {
        $signStr = "";
        $signStr .= $method;
        $signStr .= $domain;
        $signStr .= $path;
        $signStr .= "?";
        ksort($reqArr, SORT_STRING);

        foreach ($reqArr as $key => $val) {
            $signStr .= $key . "=" . $val . "&";
        }
        $signStr = substr($signStr, 0, -1);
        $signStr = base64_encode(hash_hmac('SHA1', $signStr, $secretKey, true));

        return $signStr;
    }

    /**
     * http post请求
     * url 请求链接地址
     * data 请求数据
     * rsp_str  回包数据
     * http_code 请求状态码
     * method 请求方式，默认POST
     * timeout 超时时间
     * cookies cookie
     * header http请求头
     * output int 请求结果
     */
    function http_curl_exec($url, $data, & $rsp_str, & $http_code, $method = 'POST', $timeout = 10, $cookies = array (), $headers = array ()) {
        $ch = curl_init();
        if (!curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)) {
            echo 'http_curl_ex set returntransfer failed';
            return -1;
        }

        if (count($headers) > 0) {
            //Log::debug('http_curl_ex set headers');
            if (!curl_setopt($ch, CURLOPT_HTTPHEADER, $headers)) {
                echo 'http_curl_ex set httpheader failed';
                return -1;
            }
        }

        // 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($method != 'GET') {
            $data = is_array($data) ? json_encode($data) : $data;
            //Log::debug('data (non GET method) : '.$data);
            if (!curl_setopt($ch, CURLOPT_POSTFIELDS, $data)) {
                echo 'http_curl_ex set postfields failed';
                return -1;
            }
        } else {
            $data = is_array($data) ? http_build_query($data) : $data;
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= $data;
        }
        echo "Req data :" . json_encode($data);
        if (!empty ($cookies)) {
            $cookie_str = '';
            foreach ($cookies as $key => $val) {
                $cookie_str .= "$key=$val; ";
            }

            if (!curl_setopt($ch, CURLOPT_COOKIE, trim($cookie_str))) {
                echo 'http_curl_ex set cookie failed';
                return -1;
            }
        }

        if (!curl_setopt($ch, CURLOPT_URL, $url)) {
            echo 'http_curl_ex set url failed';
            return -1;
        }

        if (!curl_setopt($ch, CURLOPT_TIMEOUT, $timeout)) {
            echo 'http_curl_ex set timeout failed';
            return -1;
        }

        if (!curl_setopt($ch, CURLOPT_HEADER, 0)) {
            echo 'http_curl_ex set header failed';
            return -1;
        }

        $rsp_str = curl_exec($ch);
        if ($rsp_str === false) {
//            var_dump(curl_error($ch));
            curl_close($ch);
            return -2;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return 0;
    }

    /**
     * 请求获取语音
     * output str 音频pcm格式
     */
    public function textToAudio(string $fileName) :array
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

        $ext = '.pcm';// Tencent的php只支持pcm格式
        // 完整文件地址
        $fullFileName = $this->config['fileRoot'] != '/' ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . $fileName.$ext : $fileName.$ext;
        // 文件冲突，重新命名一下，后缀加_new
        if(file_exists($fullFileName)){
            $fullFileName = $this->config['fileRoot'] != '/'
                ? $this->config['fileRoot'] . DIRECTORY_SEPARATOR . md5($fileName).'_new'.$ext
                : md5($fileName).'_new'.$ext;
        }
        $reqArr = $this->createMsg();
        $serverUrl = $this->config['url'];

        $autho = $this->createSign($reqArr, "POST", "tts.cloud.tencent.com", "/stream", $this->config['SecretKey']);

        $header = array (
            'Authorization: ' . $autho,
            'Content-Type: ' . 'application/json',
        );

        $rsp_str = "";
        $http_code = -1;
        $ret = $this->http_curl_exec($serverUrl, $reqArr, $rsp_str, $http_code, 'POST', 15, array (), $header);
        if ($ret != 0) {
            return ['code'=>0, 'msg' => 'http_curl_exec failed', 'data'=>['audio_name'=>'']];
        }

        $pcm_file = fopen($fullFileName, "w");
        fwrite($pcm_file, $rsp_str);
        fclose($pcm_file);
        return ['code'=>1, 'msg' => '合成成功', 'data'=>['audio_name'=>$fileName]];
    }

    /**
     * 创建参数
     * @return array
     */
    private function createMsg()
    {
        return [
            'Action'=>$this->config['Action'],
            'AppId'=>$this->config['AppId'],
            'Codec'=>$this->config['Codec'],
            'Expired'=>$this->config['Expired'] + time(), //表示为离线识别
            'ModelType'=>$this->config['ModelType'],
            'PrimaryLanguage'=>$this->config['PrimaryLanguage'],
            'ProjectId'=>$this->config['ProjectId'],
            'SampleRate'=>$this->config['SampleRate'],
            'SecretId'=>$this->config['SecretId'],
            'SessionId'=>$this->config['SessionId'],
            'Speed'=>$this->config['Speed'],
            'Text'=>$this->config['draftContent'],
            'Timestamp'=>time(),
            'VoiceType'=>$this->config['VoiceType'],
            'Volume'=>$this->config['Volume']
        ];
    }
    /**
     * 获取guid
     * output str uuid
     */
    function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid =
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }
}