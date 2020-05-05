<?php

namespace Jacquesndl\MailerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;

/**
 * @author Jacques de Lamballerie <jndl@protonmail.com>
 */
final class MakeEmail extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:email';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new email class')
            ->addArgument('name', InputArgument::OPTIONAL, 'Choose a class name for your email (e.g. <fg=yellow>WelcomeEmail</>)')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeEmail.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $emailClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('name'),
            'Email\\',
            'Email'
        );

        $twigTemplatePath = sprintf('emails/%s.email.twig', Str::asSnakeCase($input->getArgument('name')));

        $generator->generateClass(
            $emailClassNameDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/email_class.tpl.php',
            [
                'template' => $twigTemplatePath,
            ]
        );

        $generator->generateTemplate(
            $twigTemplatePath,
            __DIR__.'/../Resources/skeleton/twig_template.tpl.php'
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new email class and start customizing it.',
            'Find the documentation at <fg=yellow>https://github.com/jacquesndl/MailerBundle</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }
}
