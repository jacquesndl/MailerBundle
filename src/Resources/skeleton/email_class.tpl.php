<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use Jacquesndl\MailerBundle\Message\TemplatedEmail;

class <?= $class_name; ?> extends TemplatedEmail
{
    protected $template = '<?= $template; ?>';
}
