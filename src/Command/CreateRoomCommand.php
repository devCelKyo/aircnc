<?php

namespace App\Command;

use App\Entity\Room;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;


class CreateRoomCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    protected static $defaultName = 'app:create-room';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->em = $container->get('doctrine')->getManager();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('summary', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('description', InputArgument::REQUIRED, 'Argument description');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sum = $input->getArgument('summary');
        $desc = $input->getArgument('description');
        $room = new Room();
        $room->setSummary($sum);
        $room->setDescription($desc);

        $this->em->persist($room);
        
        $this->em->flush();

        $output->writeln('<success>C\'est bon<success>');

        return 0;
    }
}
