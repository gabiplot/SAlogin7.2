<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\AdminBundle\Datagrid\ListMapper;

use Doctrine\ORM\EntityManagerInterface;

use Sonata\AdminBundle\Route\RouteCollection;

class ObjetoInventarioTotalesAdminController extends CRUDController
{    

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * TOTAL
     */
    public function totalAction(Request $request): Response
    {

        $object = $this->admin->getSubject();
        //dd("total");
        $datagrid = $this->admin->getDatagrid();          

        $datagrid->getForm()->handleRequest($request);

        $objeto_actual = -1;
        $unidad_actual = -1;
        
        if ($datagrid->getForm()->isSubmitted() && $datagrid->getForm()->isValid()) { 
            //dd("valid");
            $data = $datagrid->getForm()->getData();            

            
            if ($data['objeto']['value'] != null){
                $objeto_actual = $data['objeto']['value']->getId();
            } else {
                $objeto_actual = '%';
            }

            if ($data['inventario__unidad']['value'] != null){
                //dump($data['inventario__unidad']['value']);
                $unidad_actual = $data['inventario__unidad']['value']->getId();
            } else {
                $unidad_actual = '%';
            }

        }

        //dump($objeto_actual);
        //dump($unidad_actual);

        $conn = $this->entityManager->getConnection();        

        $sql = "
        SELECT 
            unidad.nombre as unidad,
            objeto.nombre as objeto,
            objeto.id as objeto_id, 
            sum(alta) as alta, 
            sum(baja) as baja, 
            sum(alta-baja) as subtotal 
        FROM objeto_inventario 
        LEFT JOIN inventario ON objeto_inventario.inventario_id = inventario.id 
        LEFT JOIN objeto ON objeto_inventario.objeto_id = objeto.id 
        LEFT JOIN unidad ON inventario.unidad_id = unidad.id 
        WHERE objeto_inventario.objeto_id like :xobjeto AND unidad.id like :xunidad GROUP by objeto_inventario.objeto_id, inventario.unidad_id";          

        $stmt = $conn->prepare($sql);

        $stmt->bindValue("xobjeto", $objeto_actual);
        $stmt->bindValue("xunidad", $unidad_actual);        

        $resultSet = $stmt->executeQuery();

        $objects = $resultSet->fetchAllAssociative();

             

        $formView = $datagrid->getForm()->createView();
        
        return $this->render('inventario/totalfullAction.html.twig', [
            'action' => 'total',
            'objects' => $objects,
            'object' => $object, 
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'form' => $formView,            
        ]);
    }    

}