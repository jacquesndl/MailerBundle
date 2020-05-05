<?php

namespace Jacquesndl\MailerBundle\Message;

use Symfony\Component\Mime\Email;

/**
 * @author Jacques de Lamballerie <jndl@protonmail.com>
 */
class TemplatedEmail extends Email
{
    protected $template;
    protected $context = [];

    public function template(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function context(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @internal
     */
    public function __serialize(): array
    {
        return [$this->template, $this->context, parent::__serialize()];
    }

    /**
     * @internal
     */
    public function __unserialize(array $data): void
    {
        [$this->template, $this->context, $parentData] = $data;

        parent::__unserialize($parentData);
    }
}
