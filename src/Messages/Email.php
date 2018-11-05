<?php

namespace App\Messages;

use App\MessageParsers\ParserInterface;
use Symfony\Component\Validator\Validation;

class Email
{
    const TYPE = 'email';

    /**
     * @var array
     */
    private $requiredAttributes = [
        'to', 'from', 'subject', 'message',
    ];

    /**
     * @var \stdClass
     */
    private $message;

    /**
     * Email constructor.
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->message = $parser->getMessage();
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->message->to;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->message->from;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->message->subject;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message->message;
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function checkType(string $type)
    {
        return $type === self::TYPE;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $validator = Validation::createValidator();

        foreach ($this->requiredAttributes as $attribute) {
            if (!isset($this->message->{$attribute})) {
                return false;
            }
        }

        $constraints = [
            new \Symfony\Component\Validator\Constraints\Email(),
            new \Symfony\Component\Validator\Constraints\NotBlank()
        ];

        $emails = [
            $this->message->to,

            // not critical
            //$this->message->from,
        ];
        foreach ($emails as $email) {

            $error = $validator->validate($email, $constraints);

            if (count($error) > 0) {
                return false;
            }
        }

        return true;
    }
}