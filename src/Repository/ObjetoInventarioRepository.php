<?php

namespace App\Repository;

use App\Entity\ObjetoInventario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ObjetoInventario>
 *
 * @method ObjetoInventario|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObjetoInventario|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObjetoInventario[]    findAll()
 * @method ObjetoInventario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetoInventarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjetoInventario::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ObjetoInventario $entity, bool $flush = true): void
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
    public function remove(ObjetoInventario $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //CUANDO ES UNA EDICION
    public function movimientoCorrecto($alta, $baja, $objeto_id, $unidad_id, $id): ?int
    {   
        $tmp = $this->createQueryBuilder('o')
                    ->select('((sum(o.alta) + :xalta) - (sum(o.baja) + :xbaja )) as total')
                    ->leftJoin('o.inventario', 'i')
                    ->andWhere('o.objeto = :xobjeto_id')
                    ->andWhere('i.unidad = :xunidad_id')
                    ->setParameter('xalta', $alta)
                    ->setParameter('xbaja', $baja)
                    ->setParameter('xunidad_id', $unidad_id)
                    ->setParameter('xobjeto_id', $objeto_id);
        
        if ($id != null ){
        $tmp = $tmp->andWhere('o.id <> :xid')
                   ->setParameter('xid', $id);
        }

        $tmp = $tmp->getQuery()->getOneOrNullResult();
        
        //dd($tmp);
        $ret = false;

        if ($tmp != null){
            if($tmp['total'] == null ) {
                dump("total = null");
                if ($baja == 0){
                    $ret = true;
                }               
            } else {
                dump("total != null");
                if(intval($tmp['total']) >= 0 ){
                    $ret = true;
                }
            }
        }

        dump("baj");
        dump($baja);
        dump("alta");
        dump($alta);
        dump($tmp);
        //  dd($ret);

        return $ret;
    }    

    //CUANDO SE ELIMINA UN ELEMENTO
    public function eliminaCorrecto($objeto_id, $unidad_id, $id): ?int
    {   
        $tmp = $this->createQueryBuilder('o')
                    ->select('((sum(o.alta)) - (sum(o.baja))) as total')
                    ->leftJoin('o.inventario', 'i')
                    ->andWhere('o.objeto = :xobjeto_id')
                    ->andWhere('i.unidad = :xunidad_id')
                    ->setParameter('xunidad_id', $unidad_id)
                    ->setParameter('xobjeto_id', $objeto_id);
        
        if ($id != null ){
        $tmp = $tmp->andWhere('o.id <> :xid')
                   ->setParameter('xid', $id);
        }

        $tmp = $tmp->getQuery()->getOneOrNullResult();
        //$tmp->getQuery();
        
        //dd($tmp);

        if ($tmp != null){
            if (intval($tmp['total']) < 0 || $tmp['total'] == null){
                return false;
            } else {
                return true;
            }
        }
        return false;
    }    

    // /**
    //  * @return ObjetoInventario[] Returns an array of ObjetoInventario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ObjetoInventario
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
