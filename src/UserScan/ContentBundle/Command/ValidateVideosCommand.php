<?php

namespace UserScan\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ValidateVideosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('videos:validate')
            ->setDescription('Validate UserScan video files and copy them to target folder')
            ->addArgument('path', InputArgument::OPTIONAL, 'Optional path to videos')
            ->addArgument('target', InputArgument::OPTIONAL, 'Optional target folder to be copied')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $path = $input->getArgument('path');
        $target = $input->getArgument('target');

        $path = $path ? $path : $this->getContainer()->getParameter('video_path');
        $target = $target ? $target : $this->getContainer()->getParameter('video_target');

        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Specified path "%s" is not a directory', $path));
        }

        if (!is_dir($target)) {
            throw new \InvalidArgumentException(sprintf('Specified target "%s" is not a directory', $target));
        }

        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('Unable to read from the "%s" directory', $path));
        }

        if (!is_writable($target)) {
            throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $target));
        }

        //@todo: we might add filter->fileNames to validate with generated fileName ids...
        $finder = new Finder();
        $finder->files()->size('< 75M')->name('*.mp4')->depth('== 0');

        $filesystem = new Filesystem();

        $count = 0;

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        foreach ($finder->in($path) as $file) {

            $basenameArray = explode('_', $file->getBasename('.mp4'));

            if (count($basenameArray) != 2) {
                $output->writeln(sprintf('Invalid file name <info>%s</info> <error>deleting</error>', $file->getFilename()));

                $logger->crit(sprintf('Invalid file name <info>%s</info> <error>deleting</error>', $file->getFilename()));

                $filesystem->remove($file);
                continue;
            }

            list($testerSessionId, $projectUrlId) = $basenameArray;

            $project = $doctrine
                ->getRepository('ProjectBundle:Project')
                ->findOneBy(array('url_id' => $projectUrlId));

            if (!$project) {
                $output->writeln(sprintf('Invalid Project ID <info>%s</info> <error>reporting file.</error>', $projectUrlId));

                $logger->crit(sprintf('Invalid Project ID <info>%s</info> <error>reporting file.</error>', $projectUrlId));

                //$filesystem->remove($file);
                continue;
            }

            $tester = $doctrine
                ->getRepository('ContentBundle:Tester')
                ->findOneBySessionId($testerSessionId);

            if (!$tester) {
                $output->writeln(sprintf('Tester not found. Autogenerating.'));

                $tester = new \UserScan\ContentBundle\Entity\Tester();

                $tester->setProject($project);
                $tester->setSessionId($testerSessionId);
                $tester->setName('Anonymous');
                $tester->setEmail('');
                $tester->setUserAgent('Anonymous');
                $tester->setIp('Anonymous');
                $tester->setCreatedAt(new \DateTime('now'));

                $project->addTester($tester);
            }

            $originalFile = $file->getRealPath();
            //rename to testersessionId
            //$targetFile = $target . DIRECTORY_SEPARATOR . $file->getFilename();
            $targetFile = $target . DIRECTORY_SEPARATOR . $testerSessionId . '.mp4';

            $filesystem->copy($originalFile, $targetFile);
            $output->writeln(sprintf('File <info>%s</info> moved to <info>%s</info> directory', $file->getFilename(), $target));
            $filesystem->remove($file);

            $tester->setUploaded(true);
            $em->persist($tester);

            $user = $project->getUser();

            $message = \Swift_Message::newInstance()
                ->setSubject(sprintf('%s adlı projenize yeni bir video kaydı eklendi', $project->getName()))
                ->setFrom('contact@userscan.com', 'UserScan.com')
                ->setSender('contact@userscan.com', 'UserScan.com')
                ->setReplyTo('contact@userscan.com', 'UserScan.com')
                ->setTo($user->getUsername(), $user->getFullname())
                ->setReturnPath('hello@userscan.com')
                ->setBcc(array(
                    'miniyarov@gmail.com' => 'Ulugbek, Video Uploaded!',
                    'acikgoz.furkan@gmail.com' => 'Furkancim Bak video yuklediler :)'
                ))
                ->setBody($this->getContainer()->get('templating')->render('ProjectBundle:Project:email.new.video.html.twig', array(
                    'name' => $user->getFullname(),
                    'testerSessionId' => $testerSessionId,
                    'project' => $project
                )), 'text/html');

            try {
                $mailerResult = $this->getContainer()->get('mailer')->send($message);
            } catch (\Exception $e) {
                error_log($e->getMessage());

                $logger->crit(sprintf('Mailer Error: %s', $e->getMessage()));
                $mailerResult = 0;
            }

            $count++;
        }

        $em->flush();

        $output->writeln(sprintf('<info>%s</info> new file(s) were copied.', $count));

        $finder = new Finder();
        $finder->files()->notName('*.mp4');

        foreach ($finder->in($path) as $file) {

            $output->writeln(sprintf('Invalid filetype <info>%s</info> <error>deleted</error>', $file->getFilename()));

            $logger->crit(sprintf('Invalid filetype <info>%s</info> <error>deleted</error>', $file->getFilename()));

            $filesystem->remove($file);
        }
    }
}