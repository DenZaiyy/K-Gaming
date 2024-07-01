<?php

namespace App\Command;

use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: "admin:coupon:delete:expired", description: "Command to manually delete expired coupons", aliases: ["a:c:d:e"])]
class AdminCouponDeleteExpiredCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly PromotionRepository $promotionRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $expiredCoupons = $this->promotionRepository->findExpiredCoupon();
        $count = $this->promotionRepository->findExpiredCouponCount();

        if (empty($expiredCoupons) || $count === 0) {
            $io->warning("No expired coupons found.");
            return Command::FAILURE;
        }

        foreach ($expiredCoupons as $expiredCoupon) {
            $io->note("Coupon deleted: " . $expiredCoupon->getCoupon());
            $this->entityManager->remove($expiredCoupon);
        }

        $this->entityManager->flush();

        $io->success("Expired " . $count . " coupons have been deleted.");

        return Command::SUCCESS;
    }
}
