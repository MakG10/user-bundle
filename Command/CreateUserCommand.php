<?php

namespace MakG\UserBundle\Command;


use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Mailer\MailerInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommand extends Command
{
    private $userManager;
    private $userManipulator;
    private $mailer;
    private $validator;

    public function __construct(
        UserManagerInterface $userManager,
        UserManipulatorInterface $userManipulator,
        MailerInterface $mailer,
        ValidatorInterface $validator
    ) {
        parent::__construct();

        $this->userManager = $userManager;
        $this->userManipulator = $userManipulator;
        $this->mailer = $mailer;
        $this->validator = $validator;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $enabled = !$input->getOption('inactive');

        $user = $this->createUser($email, $enabled);

        $errors = $this->validator->validate($user, null, ['Default', 'CreateCommand']);

        if ($errors->count() > 0) {
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $output->writeln("<error>{$error->getMessage()}</error>");
            }

            return 1;
        }

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

    private function createUser(string $email, bool $enabled): UserInterface
    {
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setEnabled($enabled);

        // Generate random password which won't be revealed
        $this->userManipulator->generateRandomPassword($user);
        $this->userManipulator->generateRandomToken($user);

        return $user;
    }
}
