<?php

namespace App\Repository;

use App\Entity\CompanySymbol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanySymbol>
 *
 * @method CompanySymbol|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanySymbol|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanySymbol[]    findAll()
 * @method CompanySymbol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanySymbolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanySymbol::class);
    }

//    /**
//     * @return CompanySymbol[] Returns an array of CompanySymbol objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CompanySymbol
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
