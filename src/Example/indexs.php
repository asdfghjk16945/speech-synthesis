<?php

use SpeechSynthesis\Example\Index;

require 'Index.php';
require '../Contracts/AudioSynthesisStrategy.php';
require '../SynthesisFactory.php';
require '../Products/Config.php';
require '../Products/Iflytek.php';

$res = new Index();
$res->test();