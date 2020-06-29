<?php
namespace SpeechSynthesis\Contracts;

interface AudioSynthesisStrategy
{
    public function textToAudio(string $fileName):array ;
}