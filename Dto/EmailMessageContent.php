<?php

namespace MakG\UserBundle\Dto;


class EmailMessageContent
{
    public $subject;
    public $bodyText;
    public $bodyHtml;

    public function __construct(string $subject, string $bodyText, string $bodyHtml)
    {
        $this->subject = $subject;
        $this->bodyText = $bodyText;
        $this->bodyHtml = $bodyHtml;
    }
}
