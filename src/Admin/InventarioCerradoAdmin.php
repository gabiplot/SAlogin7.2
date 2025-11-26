<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\Form\Validator\ErrorElement;

use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

use Sonata\AdminBundle\Route\RouteCollection;

use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;

use App\Traits\InventarioTrait;

final class InventarioCerradoAdmin extends AbstractAdmin
{
    use InventarioTrait;

    protected $baseRouteName = 'admin_app_inventariocerrado';
    protected $baseRoutePattern = 'app/inventariocerrado';    

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('total', $this->getRouterIdParameter().'/total')
            ->add('alta', $this->getRouterIdParameter().'/alta')
            ->add('baja', $this->getRouterIdParameter().'/baja')
        ;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {

        if (!$childAdmin && !in_array($action, ['edit', 'show','total'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild('Inventario', $admin->generateMenuUrl('show', ['id' => $id]));

        if ($this->isGranted('LIST')) {
            
            $menu->addChild('Detalle', $admin->generateMenuUrl('admin.objeto_inventariocerrado.list', ['id' => $id]));

            $menu->addChild('Ver Totales')->setAttribute('dropdown', true);

            $menu['Ver Totales']->addChild('Totales', $admin->generateMenuUrl('admin.inventariocerrado.total', ['id' => $id]));

            $menu['Ver Totales']->addChild('Altas', $admin->generateMenuUrl('admin.inventariocerrado.alta', ['id' => $id]));

            $menu['Ver Totales']->addChild('Bajas', $admin->generateMenuUrl('admin.inventariocerrado.baja', ['id' => $id]));

        }        
    } 

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        $query = parent::configureQuery($query);

        $alias = current($query->getRootAliases());

        $query->andWhere($alias . ".estado = false");        
        $query->andWhere($alias . ".unidad = :unidad");

        $query->setParameter('unidad', $user->getUnidad()->getId());

        return $query;
    }  

    protected function configureListFields(ListMapper $listMapper): void
    {

        $listMapper
            ->add('fecha',null,['format' => 'd-m-Y'])
            ->add('fecha_cierre',null,['format' => 'd-m-Y'])
            ->add('nombre')
            ->add('unidad')
            ->add('estado')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'detail' => [
                        'template' => 'inventariocerrado/list__action_detail.html.twig',
                    ],
                ],
            ]);
    }

    /*
    public function getBatchActions()
    {        
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('total', $this->getRouterIdParameter().'/total')
            ->add('alta', $this->getRouterIdParameter().'/alta')
            ->add('baja', $this->getRouterIdParameter().'/baja')
        ;
    }     
    
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {

        if (!$childAdmin && !in_array($action, ['edit', 'show','total'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild('Inventario', $admin->generateMenuUrl('show', ['id' => $id]));

        if ($this->isGranted('LIST')) {
            
            $menu->addChild('Detalle', $admin->generateMenuUrl('admin.objeto_inventariocerrado.list', ['id' => $id]));

            $menu->addChild('Ver Totales')->setAttribute('dropdown', true);

            $menu['Ver Totales']->addChild('Totales', $admin->generateMenuUrl('admin.inventariocerrado.total', ['id' => $id]));

            $menu['Ver Totales']->addChild('Altas', $admin->generateMenuUrl('admin.inventariocerrado.alta', ['id' => $id]));

            $menu['Ver Totales']->addChild('Bajas', $admin->generateMenuUrl('admin.inventariocerrado.baja', ['id' => $id]));

        }        
    }      

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        //dump($user->getUnidad()->getId());

        $query = parent::configureQuery($query);

        $alias = current($query->getRootAliases());

        $query->andWhere($alias . ".estado = false");        
        $query->andWhere($alias . ".unidad = :unidad");
        $query->OrderBy($alias.".id", 'DESC');

        $query->setParameter('unidad', $user->getUnidad()->getId());

        return $query;
    }    

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('fecha')
            ->add('nombre')
            ->add('unidad')
            ->add('estado')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {

        $listMapper
            ->add('id')
            ->add('fecha',null,['format' => 'd-m-Y'])
            ->add('nombre')
            ->add('unidad')
            ->add('estado')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'detail' => [
                        'template' => 'inventariocerrado/list__action_detail.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();        

        $formMapper->with("fecha",['class' => 'col-md-4']);
        $formMapper->add('fecha',null,[
                                        'widget'=>'single_text',
                                        'row_attr'=>['class'=>'col-md-2'],
                                        'required'=>true,
                                        'help'=>'La Fecha que figurara en los reportes'
                                      ]);
        $formMapper->end();
        $formMapper->with("nombre",['class' => 'col-md-8']);
        $formMapper->add('nombre',null, ['help'=>'Descripción breve del inventario Ej: PRIMER SEMESTRE 2024']);
        
        if ($this->isCurrentRoute('create')) {
            $formMapper->add('estado', HiddenType::class, ['data'=>'1', 'help'=>'Seleccione Solamente para cerrar el INVENTARIO']);            
            $formMapper->add('unidad', ModelHiddenType::class, ['attr'=>['value'=>$user->getUnidad()->getId()]]);
          }
          else {
            //CAMBIE EL CHECK POR UN CHOICE
            $formMapper->add('estado',ChoiceType::class, [
                'choices'  => [
                    'INVENTARIO HABILITADO' => true,
                    'CERRAR INVENTARIO' => false,
                ],
                'help'=>'Seleccione Solamente para cerrar el INVENTARIO, una vez cerrado NO se puede MODIFICAR'
            ]);            
            $formMapper->add('unidad', ModelHiddenType::class);
          }           
                
        $formMapper->end(); 
    
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('fecha')
            ->add('nombre')
            ->add('unidad')
            ->add('estado')
            ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        //dd($object);
        if ($this->isCurrentRoute('create')){
            //SI ES NULL HAY UN INVENTARIO ACTIVO...
            if ($this->getEstadoActivo($object) != null){
                $errorElement
                    ->with('unidad')
                        ->addViolation("ANTES DE CREAR UN INVENTARIO, DEBE CERRAR EL ANTERIOR")
                    ->end()
                ;
            }
        }
        else
        {
            if ($this->isCurrentRoute('edit'))
            {
               if ($object->getEstado()) //SI EL ESTADO ES TRUE VERIFICAR...
               {    
                    //FALTA QUITAR EXCEPCION SI ES EL MISMO INVENTARIO QUE LO SALTEE..
                    if ($this->getEstadoActivo($object) != null)
                    {
                        
                        //$errorElement
                        //    ->with('unidad')
                        //        ->addViolation("ANTES DE CREAR UN INVENTARIO, DEBE CERRAR EL ANTERIOR")
                        //    ->end()
                        //;
                        
                    }                  
               }
            }
        }
    }

    public function getEstadoActivo($inventario){      

        $ea = $this->getModelManager()
                   ->getEntityManager($this->getClass())
                   ->getRepository($this->getClass())
                   ->getEstadoActivo($inventario)
                    ;      

        return $ea;
    }    
    */
}
