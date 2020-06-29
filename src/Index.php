<?php


namespace SpeechSynthesis;


use SpeechSynthesis\Test\Tests;

class Index
{
    public function test()
    {
        $test = new Tests();
        echo 'success';
        echo $test->index();
    }
}