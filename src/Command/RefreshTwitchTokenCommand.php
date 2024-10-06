<?php

namespace App\Command;

use App\Service\TwitchTokenService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:refresh-twitch-token',
    description: 'Add a short description for your command',
    aliases: ['r:t:t']
)]
class RefreshTwitchTokenCommand extends Command
{
    public function __construct(private TwitchTokenService $twitchTokenService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Refresh the Twitch client_credentials access token if it has expired')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->twitchTokenService->getAccessToken();

        $io->success('Twitch client_credentials access token refreshed');
        return Command::SUCCESS;
    }
}
