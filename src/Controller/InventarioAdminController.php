<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\AdminBundle\Datagrid\ListMapper;

use Doctrine\ORM\EntityManagerInterface;

class InventarioAdminController extends CRUDController
{    

    private $entityManager;

    private $estado_enum = ['0'=>'MALO', '1' => 'REGULAR', '2' => 'BUENO', '3' => 'MUY BUENO', '4' => 'NUEVO'];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }  

    public function getEstado($id){
        $conn = $this->entityManager->getConnection();      
        //$conn = $this->entityManager->getConnection();
        $sql = "SELECT objeto_inventario.estado FROM objeto_inventario WHERE id = :xid";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue("xid", $id);        

        $resultSet = $stmt->executeQuery();
       
        $resultado = $resultSet->fetch();

        if ($resultado != null){
            return $this->estado_enum[$resultado['estado']];
        }

        return null;
    }        
    
    /**
     * TOTAL
     */
    public function totalAction($id): Response
    {

        $object = $this->admin->getSubject();
        $conn = $this->entityManager->getConnection();
        
        $sql = "SELECT 
        max(objeto_inventario.id) as max_id,
        sum(if(objeto_inventario.inventario_id < :xinventario, objeto_inventario.alta, 0) - if(objeto_inventario.inventario_id < :xinventario, objeto_inventario.baja, 0)) as ea,
        sum(if(objeto_inventario.inventario_id = :xinventario, objeto_inventario.alta, 0)) as alta, sum(if(objeto_inventario.inventario_id = :xinventario, objeto_inventario.baja, 0)) as baja,
        objeto_inventario.estado,
        objeto_inventario.objeto_id, objeto.nombre, sub_rubro.categoria as sub_rubro, rubro.categoria as rubro, objeto.codigo 
        FROM objeto_inventario 
        LEFT JOIN inventario ON objeto_inventario.inventario_id = inventario.id
        LEFT JOIN objeto ON objeto_inventario.objeto_id = objeto.id
        LEFT JOIN sub_rubro ON objeto.subrubro_id = sub_rubro.id
        LEFT JOIN rubro ON sub_rubro.rubro_id = rubro.id
        WHERE inventario.unidad_id = :xunidad and objeto_inventario.tipo_bien = 1 GROUP BY objeto_inventario.objeto_id";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindValue("xinventario", $object->getId());
        $stmt->bindValue("xunidad", $object->getUnidad()->getId());

        $resultSet = $stmt->executeQuery();

        $objects = $resultSet->fetchAllAssociative();
        
        return $this->render('inventario/totalAction.html.twig', [
            'action' => 'total',
            'objects' => $objects,
            'object' => $object,            
            'this' => $this
        ]);
    }


    /**
     * TOTAL
     */
    public function altaAction($id): Response
    {

        $object = $this->admin->getSubject();
        $conn = $this->entityManager->getConnection();
        
        $sql = "SELECT
		objeto_inventario.alta,
        objeto_inventario.motivo,
        objeto.nombre, sub_rubro.categoria as sub_rubro, rubro.categoria as rubro, objeto.codigo 
        FROM objeto_inventario 
        LEFT JOIN inventario ON objeto_inventario.inventario_id = inventario.id
        LEFT JOIN objeto ON objeto_inventario.objeto_id = objeto.id
        LEFT JOIN sub_rubro ON objeto.subrubro_id = sub_rubro.id
        LEFT JOIN rubro ON sub_rubro.rubro_id = rubro.id
        WHERE inventario.unidad_id = :xunidad AND objeto_inventario.inventario_id = :xinventario AND objeto_inventario.alta_baja = 0";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindValue("xinventario", $object->getId());
        $stmt->bindValue("xunidad", $object->getUnidad()->getId());

        $resultSet = $stmt->executeQuery();

        $objects = $resultSet->fetchAllAssociative();
        
        return $this->render('inventario/altaAction.html.twig', [
            'action' => 'total',
            'objects' => $objects,
            'object' => $object,            
        ]);
    }    

    /**
     * TOTAL
     */
    public function bajaAction($id): Response
    {

        $object = $this->admin->getSubject();
        $conn = $this->entityManager->getConnection();
        
        $sql = "SELECT 		
		objeto_inventario.baja,
        objeto_inventario.motivo,
        objeto.nombre, sub_rubro.categoria as sub_rubro, rubro.categoria as rubro, objeto.codigo 
        FROM objeto_inventario 
        LEFT JOIN inventario ON objeto_inventario.inventario_id = inventario.id
        LEFT JOIN objeto ON objeto_inventario.objeto_id = objeto.id
        LEFT JOIN sub_rubro ON objeto.subrubro_id = sub_rubro.id
        LEFT JOIN rubro ON sub_rubro.rubro_id = rubro.id
        WHERE inventario.unidad_id = :xunidad AND objeto_inventario.inventario_id = :xinventario AND objeto_inventario.alta_baja = 1";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindValue("xinventario", $object->getId());
        $stmt->bindValue("xunidad", $object->getUnidad()->getId());

        $resultSet = $stmt->executeQuery();

        $objects = $resultSet->fetchAllAssociative();
        
        return $this->render('inventario/bajaAction.html.twig', [
            'action' => 'total',
            'objects' => $objects,
            'object' => $object,            
        ]);
    }

    /**
     * TOTAL
     */
    public function totalfullAction(): Response
    {

        $object = $this->admin->getSubject();
        $conn = $this->entityManager->getConnection();
        

        $sql = "
        SELECT 
            unidad.nombre as unidad,
            objeto.nombre as objeto, 
            sum(alta), 
            sum(baja), 
            sum(alta-baja) 
        FROM objeto_inventario 
        LEFT JOIN inventario ON objeto_inventario.inventario_id = inventario.id 
        LEFT JOIN objeto ON objeto_inventario.objeto_id = objeto.id 
        LEFT JOIN unidad ON inventario.unidad_id = unidad.id 
        WHERE 1 GROUP by objeto_inventario.objeto_id, inventario.unidad_id";
        
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        $objects = $resultSet->fetchAllAssociative();

        $datagrid = $this->admin->getDatagrid();

        $formView = $datagrid->getForm()->createView();
        
        return $this->render('inventario/totalfullAction.html.twig', [
            'action' => 'list',
            'objects' => $objects,
            'object' => $object, 
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'form' => $formView,
        ]);
    }    

}