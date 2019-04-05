<?php

namespace MakG\UserBundle\Entity;


trait ConfigurableTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default": true})
     */
    protected $configured = true;

    public function isConfigured(): ?bool
    {
        return $this->configured;
    }

    public function setConfigured(?bool $configured)
    {
        $this->configured = $configured;

        return $this;
    }
}
