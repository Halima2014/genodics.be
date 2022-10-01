<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class SiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Site::class;
    }

  
    public function configureFields(string $pageName): iterable
    {
        return [
            UrlField::new('url'),
            TextField::new('titre'),
            ImageField::new('image')
                ->setBasePath('uploads/sites/')
                ->setUploadDir('public/uploads/sites/'),
            TextEditorField::new('description'),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN');
    }
    public function configureActions(Actions $actions): Actions
    {
        $admin = in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true);

        if ($admin) {
            return parent::configureActions($actions);
        } else {
            return $actions
                ->remove(Crud::PAGE_INDEX, Action::NEW)
                ->remove(Crud::PAGE_INDEX, Action::EDIT)
                ->remove(Crud::PAGE_INDEX, Action::DELETE);
        }
    }
    
}
