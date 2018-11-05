<?php

namespace App\MessageParsers;

class JsonParser implements ParserInterface
{
    /**
     * @var \stdClass
     */
    private $parsedMessage;

    /**
     * @return \stdClass
     */
    public function getMessage(): \stdClass
    {
        return $this->parsedMessage;
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->parsedMessage->type;
    }

    /**
     * @param string $message
     * @return ParserInterface
     */
    public function parse(string $message): ParserInterface
    {
        $this->parsedMessage = json_decode($message);

        if ($this->parsedMessage === null) {
            throw new \UnexpectedValueException('Received message is not in json format');
        }

        if (empty($this->parsedMessage->type)) {
            throw new \UnexpectedValueException('Undefined required attribute "type"');
        }

        return $this;
    }
}