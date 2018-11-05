<?php

namespace App\Tests\Rabbit;

use App\MessageParsers\JsonParser;
use App\Rabbit\EmailService;
use App\Tests\BaseTestCase;
use Monolog\Logger;
use PhpAmqpLib\Message\AMQPMessage;

class EmailServiceTest extends BaseTestCase
{
    public function testEmailService()
    {
        $amqpMessage = new AMQPMessage('{"type":"email","to":"to@test.org","from":"from@test.com","subject":"Some subject","message":"Hello world!"}');

        $emailService = $this->getEmailServiceWithSending();
        $emailService->execute($amqpMessage);
        $sent = $emailService->getSent();

        $this->assertEquals(1, $sent);
    }

    /**
     * @dataProvider notSentEmailsProvider
     *
     * @param $messageBody
     */
    public function testNotSentEmails($messageBody)
    {
        $amqpMessage = new AMQPMessage($messageBody);

        $emailService = $this->getEmailServiceWithOneWarning();
        $emailService->execute($amqpMessage);
        $sent = $emailService->getSent();

        $this->assertEquals(0, $sent);
    }

    public function notSentEmailsProvider()
    {
        return [
            'Wrong JSON' => ['{"type":"ema'],
            'Lost attribute Type' => ['{"to":"to@test.org","from":"from@test.com","subject":"Some subject","message":"Hello world!"}'],
            'Wrong attribute Type' => ['{"type":"email1","to":"to@test.org","from":"from@test.com","subject":"Some subject","message":"Hello world!"}'],
            'Lost attributes' => ['{"type":"email","to":"to@mail.ru"}'],
            'Invalid email' => ['{"type":"email","to":"toooo","from":"from@test.com","subject":"Some subject","message":"Hello world!"}'],
        ];
    }

    /**
     * @return EmailService
     */
    private function getEmailServiceWithSending(): EmailService
    {
        $mailer = $this->getExecutedOnceMailer();
        $logger = $this->getLogger();
        $parser = new JsonParser();

        return new EmailService($logger, $mailer, $parser);
    }

    /**
     * @return EmailService
     */
    private function getEmailServiceWithOneWarning(): EmailService
    {
        $mailer = $this->getNotExecutedMailer();
        $logger = $this->getLoggerWithWarningOnce();
        $parser = new JsonParser();

        return new EmailService($logger, $mailer, $parser);
    }
}