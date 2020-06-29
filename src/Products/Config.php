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
            'fileRoot'=>'',
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
            'draftContent' => '对于一个在北平住惯的人，像我，冬天要是不刮风，便觉得是奇迹;济南的冬天是没有风声的。对于一个刚由伦敦回来的人，
                像我，冬天要能看得见日光，便觉得是怪事;济南的冬天是响晴的。自然，在热带的地方，日光是永远那么毒，响亮的天气，反有点叫人害怕。
                可是，在北中国的冬天，而能有温晴的天气，济南真得算个宝地。假若单单是有阳光，那也算不了出奇。请闭上眼睛想：一个老城，有山有水，
                全在天底下晒着阳光，暖和安适地睡着，只等春风来把它们唤醒，这是不是个理想的境界?小山整把济南围了个圈儿，只有北边缺着点口儿。
                这一圈小山在冬天特别可爱，好像是把济南放在一个小摇篮里，它们安静不动地低声地说：“你们放心吧，这儿准保暖和。”真的，
                济南的人们在冬天是面上含笑的。他们一看那些小山，心中便觉得有了着落，有了依靠。他们由天上看到山上，便不知不觉地想起：
                “明天也许就是春天了吧?这样的温暖，今天夜里山草也许就绿起来了吧?”就是这点幻想不能一时实现，他们也并不着急，因为有这样慈善的冬天，
                干啥还希望别的呢!最妙的是下点小雪呀。看吧，山上的矮松越发的青黑，树尖上顶着一髻儿白花，好像日本看护妇。山尖全白了，给蓝天镶上一道银边。
                山坡上，有的地方雪厚点，有的地方草色还露着;这样，一道儿白，一道儿暗黄，给山们穿上一件带水纹的花衣;看着看着，这件花衣好像被风儿吹动，
                叫你希望看见一点更美的山的肌肤。等到快日落的时候，微黄的阳光斜射在山腰上，那点薄雪好像忽然害了羞，微微露出点粉色。就是下小雪吧，
                济南是受不住大雪的，那些小山太秀气!古老的济南，城里那么狭窄，城外又那么宽敞，山坡上卧着些小村庄，小村庄的房顶上卧着点雪，对，
                这是张小水墨画，也许是唐代的名手画的吧。那水呢，不但不结冰，倒反在绿萍上冒着点热气，水藻真绿，把终年贮蓄的绿色全拿出来了。
                天儿越晴，水藻越绿，就凭这些绿的精神，水也不忍得冻上，况且那些长枝的垂柳还要在水里照个影儿呢!看吧，由澄清的河水慢慢往上看吧，
                空中，半空中，天上，自上而下全是那么清亮，那么蓝汪汪的，整个的是块空灵的蓝水晶。这块水晶里，包着红屋顶，黄草山，像地毯上的小团花的灰色树影。
                这就是冬天的济南。',
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