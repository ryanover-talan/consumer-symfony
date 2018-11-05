<?php

namespace App\Rabbit;

use App\MessageParsers\ParserInterface;
use App\Messages\Email;

class MessageTransformer
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * MessageTransformer constructor.
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return Email|null
     */
    public function getMessage()
    {
        switch (true) {
            case $this->isEmail():
                return new Email($this->parser);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEmail()
    {
        return Email::checkType($this->parser->getMessageType());
    }
}