# MailerBundle

MailerBundle add a new **TemplatedEmail** class for the [Symfony Mailer Component](https://symfony.com/doc/current/components/mailer.html).

## Install
 
 ```shell
composer require jacquesndl/mailer-bundle
```

## Setup
The bundle provides an official recipe to help you configure the bundle.

```yaml
# config/packages/jacquesndl_mailer.yaml

jacquesndl_mailer:
    sender:
        name: '%env(JACQUESNDL_MAILER_SENDER_NAME)%'
        address: '%env(JACQUESNDL_MAILER_SENDER_ADDRESS)%'
```

```
# .env

JACQUESNDL_MAILER_SENDER_NAME="Example"
JACQUESNDL_MAILER_SENDER_ADDRESS="example@domain.tld"
```

The env variables **JACQUESNDL_MAILER_SENDER_NAME** and **JACQUESNDL_MAILER_SENDER_ADDRESS** define the default value for the sender.
You can overwrite it using the **to()** method of the **TemplatedEmail** class. You can see an example below.


## Usage
 
### Basic
```php
// src/Controller/WelcomeController.php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Jacquesndl\MailerBundle\Message\TemplatedEmail;

class WelcomeController extends AbstractController
{
    public function index(MailerInterface $mailer): Response
    {
        // ...

        $email = (new TemplatedEmail())
            ->from('didier.deschamps@france.fr') // overwrite the default sender value
            ->to('zinedine.zidane@france.fr')
            ->replyTo('fabien.barthez@france.fr')
            ->template('emails/welcome.email.twig')
            ->attachFromPath('/path/to/documents/coupe-du-monde-1998.pdf')
            ->context([
                'firstName' => 'Zinédine',
            ])
        ;
        
        $mailer->send($email);
        
        // ...
    }
}
```

 ```twig
{# templates/emails/welcome.email.twig #}

{% block subject %}
    Welcome
{% endblock %}

{% block html %}
    <html>
    <head></head>
    <body>
    <h1>Welcome {{ firstName }}</h1>
    </body>
    </html>
{% endblock %}

{% block text %}
    Your text content
{% endblock %}
```

If the block **text** is missing or empty, mailer will generate it automatically by converting the HTML contents into text. 
If you have [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown) installed in your application, it uses that to turn HTML into Markdown (so the text email has some visual appeal). 
Otherwise, it applies the [strip_tags](https://secure.php.net/manual/en/function.strip-tags.php) PHP function to the original HTML contents.

### Advanced
 
The bundle provides a **maker** command to create an **Email** class that extends **TemplatedEmail**

 ```shell
php bin/console make:email WelcomeEmail
```

 ```php
// src/Email/WelcomeEmail.php

namespace App\Email;

use Jacquesndl\MailerBundle\Message\TemplatedEmail;

class WelcomeEmail extends TemplatedEmail
{
    protected $template = 'emails/welcome.email.twig';
}
```

```php
// src/Controller/WelcomeController.php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Email\WelcomeEmail;

class WelcomeController extends AbstractController
{
    public function index(MailerInterface $mailer): Response
    {
        // ...

        $email = (new WelcomeEmail())
            ->to('zinedine.zidane@france.fr');
        
        $mailer->send($email);
        
        // ...
    }
}
```
