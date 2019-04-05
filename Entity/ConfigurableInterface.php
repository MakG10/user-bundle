<?php

namespace MakG\UserBundle\Entity;


interface ConfigurableInterface
{
    public function isConfigured(): ?bool;

    public function setConfigured(?bool $configured);
}
