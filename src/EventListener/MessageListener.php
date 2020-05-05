<?php

namespace Jacquesndl\MailerBundle\EventListener;

use Jacquesndl\MailerBundle\Message\TemplatedEmail;
use Jacquesndl\MailerBundle\Renderer\BodyRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author Jacques de Lamballerie <jndl@protonmail.com>
 */
class MessageListener implements EventSubscriberInterface
{
    private $renderer;
    private $defaultSender;

    public function __construct(BodyRenderer $renderer, array $defaultSender)
    {
        $this->renderer = $renderer;
        $this->defaultSender = $defaultSender;
    }

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!$email instanceof TemplatedEmail) {
            return;
        }

        $this->render($email);
        $this->defaultSender($email);
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function render(TemplatedEmail $email): void
    {
        $this->renderer->render($email);
    }

    private function defaultSender(TemplatedEmail $email): void
    {
        $from = $email->getFrom();
        if (empty($from)) {
            $email->from(new Address($this->defaultSender['address'], $this->defaultSender['name']));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}
