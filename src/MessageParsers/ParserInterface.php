<?php

namespace App\MessageParsers;

interface ParserInterface
{
    /**
     * @return \stdClass
     */
    public function getMessage():? \stdClass;

    /**
     * @return string
     */
    public function getMessageType(): string;

    /**
     * @param string $message
     * @return ParserInterface
     */
    public function parse(string $message): ParserInterface;
}