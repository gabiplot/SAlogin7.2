<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

use Sonata\AdminBundle\Form\Type\ModelHiddenType;

use Sonata\AdminBundle\Route\RouteCollection;

final class ObjetoInventarioTotalesAdmin extends AbstractAdmin
{

    protected $baseRouteName = 'admin_app_objetoinventariototales'; 
    protected $baseRoutePattern = 'app/objetoinventariototales';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('total', 'total');
    }     
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('objeto')
            ->add('inventario.unidad')
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
        //dump($listMapper);
        ///dump($listMapper);
        
        $listMapper  
            ->add('inventario.unidad')
            ->add('objeto',null, ['header_class' =>'col-md-7 text-center'])
            
            ->add('alta',null, ['header_class' =>'col-md-1 text-center'])
            ->add('sclr',null, ['header_class' =>'col-md-1 text-center'])
            ->add('alt',null, ['header_class' =>'col-md-1 text-center'])
            ->add('baja',null, ['header_class' =>'col-md-1 text-center'])
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
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
    }


}
