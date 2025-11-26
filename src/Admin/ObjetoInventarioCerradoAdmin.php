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

final class ObjetoInventarioCerradoAdmin extends AbstractAdmin
{

    protected $baseRouteName = 'admin_app_objetoinventariocerrado'; 
    protected $baseRoutePattern = 'app/objetoinventariocerrado';      

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
        $listMapper  
            ->add('objeto',null, ['header_class' =>'col-md-7 text-center'])           
            ->add('alta',null, ['header_class' =>'col-md-1 text-center'])
            ->add('baja',null, ['header_class' =>'col-md-1 text-center'])
            ->add('estado',null, ['header_class' =>'col-md-1 text-center'])
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
        $formMapper->with('Objeto');
            
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
            
        $formMapper->add('inventario',ModelHiddenType::class,[], ['admin_code' => 'inventario'])            
            ->add('objeto',null, ['help'=>'Seleccione el objeto que va a dar de alta'] )
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

    public function validate(ErrorElement $errorElement, $object)
    {

        //VERIFICAR QUE NO HAY NEGATIVO
        

        //dump($object);
        //dd("validate");
        /*
        $errorElement
            ->with('name')
                ->assertMaxLength(['limit' => 32])
            ->end();
        */
    }    


}
