<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
 

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
   
    public function configureFields(string $pageName): iterable
    {
        return [
           // IdField::new('id'),
           ImageField::new('photo')
                ->setBasePath('uploads/users/')
                ->setUploadDir('public/uploads/users/'),
            EmailField::new('email'),
            TextField::new('password')->setFormType(PasswordType::class),
            TextField::new('nom'),
            TextField::new('prenom'),
            ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER',
            ]),
           
        ];
    }
     public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;
        $plainPassword=$entityInstance->getPassword();
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'auto'],
            'memory-hard' => ['algorithm' => 'auto'],
        ]);
        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($plainPassword);
        $entityInstance->setPassword($hash);
        parent::persistEntity($entityManager, $entityInstance);
       // $entityManager->persist($entityInstance);
       // $entityManager->flush();
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;
        $plainPassword=$entityInstance->getPassword();
        $tempUser= new User();
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'auto'],
            'memory-hard' => ['algorithm' => 'auto'],
        ]);
        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($plainPassword);
        $entityInstance->setPassword($hash);
        //dd($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
       // $entityManager->persist($entityInstance);
       // $entityManager->flush();
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
