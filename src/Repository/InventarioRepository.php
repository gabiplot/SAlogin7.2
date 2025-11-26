<?php

namespace App\Repository;

use App\Entity\Inventario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventario>
 *
 * @method Inventario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inventario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inventario[]    findAll()
 * @method Inventario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventario::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Inventario $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Inventario $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getEstadoActivo(Inventario $entity): ?Inventario
    {         
         return  $this->createQueryBuilder('i')                    
                    ->andWhere('i.unidad = :val')
                    ->andWhere('i.estado = true')
                    ->setParameter('val', $entity->getUnidad())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    // /**
    //  * @return Inventario[] Returns an array of Inventario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inventario
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
