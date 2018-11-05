<?php

namespace App\Tests;

use Monolog\Logger;

trait Mocks
{
    /**
     * @return \Swift_Mailer
     */
    public function getNotExecutedMailer()
    {
        return $this->createMock('Swift_Mailer');
    }

    /**
     * @return \Swift_Mailer
     */
    public function getExecutedOnceMailer()
    {
        $mailer = $this->createMock('Swift_Mailer');
        $mailer->expects($this->once())->method('send')->willReturn(1);

        return $mailer;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->createMock(Logger::class);
    }

    /**
     * @return Logger
     */
    public function getLoggerWithWarningOnce()
    {
        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())->method('warning');

        return $logger;
    }
}