<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class UserAdmin extends AbstractAdmin
{

    public function getBatchActions()
    {        
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }    

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')
            ->add('unidad')
            ->add('email')
            ->add('password')            
            //->add('roles')
            //->add('lastLogin')
            //->add('isSuspended')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            //->add('id')
            ->add('unidad')
            ->add('email')
            ->add('password')
            ->add('rol')            
            //->add('roles')
            //->add('lastLogin')
            //->add('isSuspended')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            //->add('id')
            ->add('unidad')
            ->add('email')
            ->add('password')            
            //->add('roles')
            //->add('lastLogin')
            //->add('isSuspended')
            ->add('rol', ChoiceType::class,[
                'label'=> 'Rol de Sistema',
                'choices'  => [
                    'ROLE_ADMIN' => 'ROLE_SUPER_ADMIN', //MODIFIQUE ESTO
                    'ROLE_USER' => 'ROLE_USER',
                ],
            ])            
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            //->add('id')
            ->add('unidad')
            ->add('email')
            ->add('password')
            ->add('rol')
            //->add('roles')
            //->add('lastLogin')
            //->add('isSuspended')
            ;
    }
}
