<?php

namespace MakG\UserBundle\Command;


use MakG\UserBundle\Mailer\MailerInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    private $userManager;
    private $userManipulator;
    private $mailer;

    public function __construct(
        UserManagerInterface $userManager,
        UserManipulatorInterface $userManipulator,
        MailerInterface $mailer
    ) {
        parent::__construct();

        $this->userManager = $userManager;
        $this->userManipulator = $userManipulator;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setName('makg:user:create')
            ->setDescription('Creates a new user with random password.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addOption('inactive', 'i', InputOption::VALUE_NONE, 'Set the user as inactive')
            ->addOption(
                'send-confirmation-email',
                'c',
                InputOption::VALUE_NONE,
                'Send an e-mail with confirmation link'
            )
            ->addOption(
                'send-resetting-email',
                'r',
                InputOption::VALUE_NONE,
                'Send an e-mail with password reset link'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $enabled = !$input->getOption('inactive');

        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setEnabled($enabled);

        // Generate random password which won't be revealed
        $this->userManipulator->generateRandomPassword($user);

        $this->userManipulator->generateRandomToken($user);
        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getEmail()));


        if ($input->getOption('send-confirmation-email')) {
            $this->mailer->sendConfirmationEmail($user);

            $output->writeln('Sent confirmation e-mail.');
        }

        if ($input->getOption('send-resetting-email')) {
            $this->mailer->sendResettingEmail($user);

            $output->writeln('Sent password resetting e-mail.');
        }

        return 0;
    }
}
