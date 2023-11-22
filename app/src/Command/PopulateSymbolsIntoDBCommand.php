<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Entity\CompanySymbol;

/**
 * This command is NOT unit tested.
 * It is used as a utility tool.
 */
#[AsCommand(
    name: 'data:populate:symbols',
    description: 'Insert COmpany SUmbols from provided API',
)]
class PopulateSymbolsIntoDBCommand extends Command
{
    const URL="https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json";
    const BATCH_SIZE=300;


    private $entityManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        // We just need to download and read Json contents GuzzleHttp is a hassle for something simple.
        $data = file_get_contents(self::URL);
        $data = json_decode($data,true);

        $i=0;
        foreach($data as $item){

            $symbol = new CompanySymbol();
            $symbol->setName($item['Company Name']);
            $symbol->setSymbol($item['Symbol']);
            $io->info("Reading item ${i}: ${item['Company Name']} ( ${item['Symbol']} )");
            $this->entityManager->persist($symbol);
            if(($i%self::BATCH_SIZE)==0){
                $io->note("Flusing Data");
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            ++$i;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }
}
