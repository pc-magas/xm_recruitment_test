<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'test:mail',
    description: 'Send a test email to test transport',
)]
class TestMailCommand extends Command
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer=$mailer;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('stocks@example.com')
            ->to('test@example.com')
            ->subject("TEST")
            ->text("Oma wa mou shindeiru");

        $this->mailer->send($email);

        return Command::SUCCESS;
    }
}
