<?php

namespace App\Command;

use App\Entity\User;
use App\Service\MultiAvatars;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:create',
    description: 'Local command to create admin user fastly',
	aliases: ['a:a:c', 'admin:create']
)]
class UserAdminCommand extends Command
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $em, private MultiAvatars $avatar)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'set the username for the admin user')
	        ->addArgument('password', InputArgument::REQUIRED, 'set the password for the admin user')
	        ->addArgument('email', InputArgument::OPTIONAL, 'set the email for the admin user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
	    $username = $input->getArgument('username');
	    $password = $input->getArgument('password');
		$email = $input->getArgument('email');

		$checkUser = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
		$checkEmail = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

		if ($checkUser)
		{
			$io->error('User already exists');
			return Command::FAILURE;
		}

		if($checkEmail)
		{
			$io->error('Email already exists');
			return Command::FAILURE;
		}

	    $output->writeln([
		    'Create Admin User',
		    '==================',
		    '',
	    ]);

		$user = new User();
		$user->setUsername($username);
		$user->setPassword($this->passwordHasher->hashPassword($user, $password));
		$user->setRoles(['ROLE_ADMIN']);
		$user->setIsVerified(true);
		$user->setIsBanned(false);
		$user->setAvatar($this->avatar->getRandomUserAvatar($user->getUsername()));

	    $output->writeln('Username: '. $username);
	    if($email) {
		    $output->writeln('Email: '. $email);
		    $user->setEmail($email);
	    } else {
		    $output->writeln('Email: support@k-grischko.fr');
		    $user->setEmail('support@k-grischko.fr');
	    }

		$this->em->persist($user);
		$this->em->flush();

		$io->success('Admin user created successfully!');

        return Command::SUCCESS;
    }
}
