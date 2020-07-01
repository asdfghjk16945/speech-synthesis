<?php
namespace SpeechSynthesis\Contracts;

interface AudioSynthesisStrategy
{
    public function __construct(array $config);

    public function textToAudio(string $fileName):array ;
}