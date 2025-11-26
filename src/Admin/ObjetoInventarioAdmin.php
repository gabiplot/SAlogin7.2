<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;

use Sonata\Form\Validator\ErrorElement;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Sonata\AdminBundle\Form\Type\ModelHiddenType;

//use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Acl\Exception\Exception;

use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ObjetoInventarioAdmin extends AbstractAdmin
{
    
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        // display the first page (default = 1)
        //$sortValues['_page'] = 1;

        // reverse order (default = 'ASC')
        $sortValues['_sort_order'] = 'DESC';

        // name of the ordered field (default = the model's id field, if any)
        $sortValues['_sort_by'] = 'id';
    }   

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('objeto')
            ->add('alta')
            ->add('baja')
            ->add('estado')
            ;
    }

    public function configureBatchActions($actions)
    {
        if (isset($actions['delete'])) {
            unset($actions['delete']);
        }

        return $actions;
    }    

    protected function configureListFields(ListMapper $listMapper): void
    {
        /*
        dump($this->getModelManager()
                   ->getEntityManager($this->getClass())
                   ->getRepository($this->getClass())
                   ->movimientoCorrecto(0,0,2,30,3,4));
        */

        $listMapper
            ->add('objeto',null, ['header_class' =>'col-md-6 text-center'])
            ->add('estipobien',null,['header_class' =>'col-md-1 text-center',
                                     'label'=>'Tipo',
                                     'row_align' => 'center'])           
            ->add('alta',null, ['header_class' =>'col-md-1 text-center'])
            ->add('baja',null, ['header_class' =>'col-md-1 text-center'])
            ->add('estadoactual',null, ['header_class' =>'col-md-1 text-center', 
                                        'label'=>'Estado',
                                        'row_align' => 'right'])
            ->add('_action', null, [
                'header_class' =>'col-md-2 text-center',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->with('Objeto', ['class'=>'col-md-9']);
            
        if($this->ischild()){
            $id = $this->getRequest()->get('id');
            $formMapper
                ->add('inventario', ModelHiddenType::class,['attr'=>['value'=>$id]], ['admin_code' => 'inventario'])
                ;
            } else {
            $formMapper
                ->add('inventario',null,[], ['admin_code' => 'inventario'])
                ;
        }
        
        $formMapper->add('objeto',null, ['help'=>'Seleccione el objeto que va a dar de alta'] )
            ->end()
            ->with('Tipo Bien',['class'=>'col-md-3'])
            ->add('tipo_bien',ChoiceType::class,[
                'label'=> 'Tipo de Bien',
                'choices'  => [
                    'CAPITAL' => false,
                    'PROPIO' => true,
                ],
                'help'=>'Indique el tipo de bien CAPITAL / PROPIO'
            ])
            ->end()
            ->with('Alta Baja', ['class'=>'col-md-4'])
            ->add('alta_baja', ChoiceFieldMaskType::class, [
                'label' => 'Seleccione Alta o Baja',
                'choices' => [
                    'ALTA' => 0,
                    'BAJA' => 1,
                    'MODIFICA ESTADO' => 2,
                ],
                'map' => [
                    0 => ['alta'],
                    1 => ['baja'],
                    2 => []
                ],
                'placeholder' => 'Elija Una Opción',                
                'required' => true,
                'help'=>'Seleccione si va a realizar un Alta / Baja'
            ])         
            ->end()
            ->with('Alta/Baja', ['class'=>'col-md-4'])    
            ->add('alta',null, ['help'=>'Indique la cantidad de elementos que va a dar de ALTA'])
            ->add('baja',null, ['help'=>'Indique la cantidad de elementos que va a dar de ALTA'])
            ->end()
            ->with('Estado', ['class' =>'col-md-4'])
            ->add('estado', ChoiceType::class,[
                'label'=> 'Estado del Articulo',
                'choices'  => [
                    'NUEVO' => 4,
                    'MUY BUENO' => 3,                    
                    'BUENO' => 2,
                    'REGULAR' => 1,
                    'MALO' => 0 
                ],
                'help'=>'Indique el estado del ARTICULO'
            ])
            ->end()
            ->with('Motivo')
            ->add('motivo',null, ['required'=>false, 'help'=>'Indique REMITO Nº / RESOLUCION / MOTIVO del Alta/Baja'])
            ->end()
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('alta')
            ->add('baja')
            ->add('estado')
            ;
    }

    public function movimientoCorrecto($alta, $baja, $objeto_id, $unidad_id, $id)
    {      
        return $this->getModelManager()
                    ->getEntityManager($this->getClass())
                    ->getRepository($this->getClass())
                    ->movimientoCorrecto($alta, $baja, $objeto_id, $unidad_id, $id);

    }     

    /*
    public function eliminaCorrecto($objeto_id, $unidad_id, $id)
    {      
        return $this->getModelManager()
                    ->getEntityManager($this->getClass())
                    ->getRepository($this->getClass())
                    ->eliminaCorrecto($objeto_id, $unidad_id, $id);

    }  
    */    

    public function preValidate($object)
    {
        if ($object->getAltaBaja() == 0){
            $object->setBaja(0);
        } else if ($object->getAltaBaja() == 1){
            $object->setAlta(0);
        } else {            
            $object->setAlta(0);
            $object->setBaja(0);
        }

    }   

    public function preRemove($object)
    {
        if ($object->getAltaBaja() == 0){
            $object->setBaja(0);
        } else if ($object->getAltaBaja() == 1){
            $object->setAlta(0);
        } else {            
            $object->setAlta(0);
            $object->setBaja(0);
        }
        
        $unidad_id = $object->getInventario()->getUnidad()->getId();
        $objeto_id = $object->getObjeto()->getId();
        $id = $object->getId();

        if (!$this->movimientoCorrecto(0,0,$objeto_id,$unidad_id, $id)){
        //if (!$this->eliminaCorrecto($objeto_id,$unidad_id, $id)){
            throw new ModelManagerException(); //lanza la exception en ENV = PRODUCTION
            $redirection = new RedirectResponse($this->generateUrl('list'));
            return $redirection;
        }
        
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        
        $unidad_id = $object->getInventario()->getUnidad()->getId();
        $objeto_id = $object->getObjeto()->getId();
        $id = $object->getId();            

        $alta = $object->getAlta();
        $baja = $object->getBaja();

        if (!$this->movimientoCorrecto($alta,$baja,$objeto_id,$unidad_id, $id)){
            $errorElement
            ->with('alta_baja')
                ->addViolation('La acción hace que LAS BAJAS superen las ALTAS, NO se puede Realizar. Verifique los totales Menu VerTotales->Total')
            ->end();
        }
    }    


}
