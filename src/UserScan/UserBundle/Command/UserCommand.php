<?php

namespace UserScan\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:mote')
            ->setDescription('Promote or Demote Users of UserScan')
            ->addOption('pro', null, InputOption::VALUE_NONE)
            ->addOption('de', null, InputOption::VALUE_NONE)
            ->addArgument('username', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $doctrine = $this->getContainer()->get('doctrine');

        $em = $doctrine->getEntityManager();

        $user = $doctrine->getRepository('UserBundle:User')
            ->findOneByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('Username %s not found', $username));
        }

        $promote = $input->getOption('pro');
        $demote  = $input->getOption('de');

        if ((!$promote && !$demote) || ($promote && $demote)) {
            //throw new \InvalidArgumentException('Please specify --pro or --de options');
        }

        if ($promote) {
            $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
            $em->flush();
            $output->writeln(sprintf('Username <info>%s</info> successfully promoted', $username));
        }

        if ($demote) {
            $user->setRoles(array('ROLE_USER'));
            $em->flush();
            $output->writeln(sprintf('Username <info>%s</info> successfully demoted', $username));
        }
    }
}