<?php

namespace Olz\Command;

use Olz\Command\Common\OlzCommand;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'olz:update-measurements')]
class UpdateMeasurementsCommand extends OlzCommand {
    /** @return array<string> */
    protected function getAllowedAppEnvs(): array {
        return ['dev', 'test', 'staging', 'prod'];
    }

    protected function handle(InputInterface $input, OutputInterface $output): int {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $member_repo = $this->entityManager()->getRepository(Member::class);

        $num_users = count($user_repo->findBy(['on_off' => 1]));

        $active_members = $member_repo->findBy(['on_off' => 1]);
        $num_members = count($active_members);
        $num_member_users = 0;
        foreach ($active_members as $member) {
            $user = $member->getUser();
            if ($user !== null && $user->getOnOff() === 1) {
                $num_member_users++;
            }
        }

        $year = intval($this->dateUtils()->getCurrentDateInFormat('Y'));
        $month = intval($this->dateUtils()->getCurrentDateInFormat('m'));
        $measurement_repo = $this->entityManager()->getRepository(Measurement::class);
        $measurement_repo->set('num_users', $year, $month, $num_users);
        $measurement_repo->set('num_members', $year, $month, $num_members);
        $measurement_repo->set('num_member_users', $year, $month, $num_member_users);
        $this->logAndOutput("Users & Members measurements updated:");
        $this->logAndOutput("  num_users: {$num_users}");
        $this->logAndOutput("  num_members: {$num_members}");
        $this->logAndOutput("  num_member_users: {$num_member_users}");

        $this->logAndOutput("Measurements updated.");

        return Command::SUCCESS;
    }
}
