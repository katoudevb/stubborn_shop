<?php
// test-mail.php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

// On force le DSN directement pour ne pas dépendre du .env
$dsn = 'smtp://localhost:1025';
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('noreply@tonsite.com')
    ->to('test@example.com')
    ->subject('Test MailHog Symfony Mailer')
    ->text('Ceci est un test avec Symfony Mailer.');

$mailer->send($email);

echo "Mail envoyé ! Vérifie MailHog à http://localhost:8025\n";
