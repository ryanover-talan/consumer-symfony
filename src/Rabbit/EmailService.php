<?php

namespace App\Rabbit;

use App\MessageParsers\ParserInterface;
use App\Messages\Email;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailService implements ConsumerInterface
{
    /**
     * @var int
     */
    private $sent = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * EmailService constructor.
     * @param LoggerInterface $logger
     * @param \Swift_Mailer $mailer
     * @param ParserInterface $parser
     */
    public function __construct(LoggerInterface $logger, \Swift_Mailer $mailer, ParserInterface $parser)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->parser = $parser;
    }

    /**
     * @param AMQPMessage $amqpMessage
     * @return int
     */
    public function execute(AMQPMessage $amqpMessage)
    {
        $emailMessage = $this->getEmailMessage($amqpMessage);

        if ($emailMessage) {
            $this->sent = $this->sendEmail($emailMessage);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getSent(): int
    {
        return $this->sent;
    }

    /**
     * @param AMQPMessage $amqpMessage
     * @return Email|null
     */
    private function getEmailMessage(AMQPMessage $amqpMessage)
    {
        $emailMessage = null;

        try {
            $this->parser->parse($amqpMessage->body);
        } catch (\UnexpectedValueException $e) {
            $this->logger->warning($e->getMessage(), [
                'body' => $amqpMessage->body
            ]);

            return null;
        }

        $messageTransformer = new MessageTransformer($this->parser);
        if ($messageTransformer->isEmail()) {
            $emailMessage = $messageTransformer->getMessage();

            if (!$emailMessage->isValid()) {
                $this->logger->warning('Invalid email message', [
                    'body' => $amqpMessage->body
                ]);

                return null;
            }

        } else {
            $this->logger->warning('Unexpected type of message', [
                'body' => $amqpMessage->body
            ]);
        }

        return $emailMessage;
    }

    /**
     * @param Email $email
     * @return int
     */
    private function sendEmail(Email $email)
    {
        $message = (new \Swift_Message($email->getSubject()))
            ->setFrom($email->getFrom())
            ->setTo($email->getTo())
            ->setBody($email->getMessage());

        return $this->mailer->send($message);

    }
}