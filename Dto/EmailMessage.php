<?php

namespace MakG\UserBundle\Dto;


class EmailMessage
{
    public $recipientEmail;
    public $senderEmail;
    public $senderName;
    public $messageContent;

    public function __construct(string $recipientEmail, string $senderEmail, ?string $senderName, EmailMessageContent $messageContent)
    {
        $this->recipientEmail = $recipientEmail;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
        $this->messageContent = $messageContent;
    }
}
