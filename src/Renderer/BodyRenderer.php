<?php

namespace Jacquesndl\MailerBundle\Renderer;

use Jacquesndl\MailerBundle\Message\TemplatedEmail;
use Jacquesndl\MailerBundle\Message\WrappedTemplatedEmail;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Message;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author Jacques de Lamballerie <jndl@protonmail.com>
 */
class BodyRenderer implements BodyRendererInterface
{
    private $twig;
    private $context;
    private $converter;

    public function __construct(Environment $twig, array $context = [])
    {
        $this->twig = $twig;
        $this->context = $context;
        if (class_exists(HtmlConverter::class)) {
            $this->converter = new HtmlConverter([
                'hard_break' => true,
                'strip_tags' => true,
                'remove_nodes' => 'head style',
            ]);
        }
    }

    /**
     * @param Message $message
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Throwable
     */
    public function render(Message $message): void
    {
        if (!$message instanceof TemplatedEmail) {
            return;
        }

        $messageContext = $message->getContext();

        $vars = array_merge($this->context, $messageContext, [
            'email' => new WrappedTemplatedEmail($this->twig, $message),
        ]);

        $template = $this->twig->load($message->getTemplate());
        $subject = $template->renderBlock('subject', $vars);
        $html = $template->renderBlock('html', $vars);
        $text = $template->hasBlock('text', []) ? $template->renderBlock('text', $vars) : null;

        $message->subject($subject);
        $message->text($text);
        $message->html($html);

        // if text body is empty, compute one from the HTML body
        if (!$message->getTextBody() && null !== $html = $message->getHtmlBody()) {
            $message->text($this->convertHtmlToText(is_resource($html) ? stream_get_contents($html) : $html));
        }
    }

    private function convertHtmlToText(string $html): string
    {
        if (null !== $this->converter) {
            return $this->converter->convert($html);
        }

        return strip_tags($html);
    }
}
