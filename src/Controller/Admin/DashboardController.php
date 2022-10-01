<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Dislike;
use App\Entity\Hashtag;
use App\Entity\Like;
use App\Entity\Media;
use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    public function EntityCount(string $className)
    {
        $Repo = $this->getDoctrine()->getManager()->getRepository($className);
        return $Repo->countItems();
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $stat=[
        'articles'=>$this->EntityCount(Article::class),
        'medias'=>$this->EntityCount(Media::class),
        'users'=>$this->EntityCount(User::class),
        'categories'=>$this->EntityCount(Categorie::class),
        'commentaires'=>$this->EntityCount(Commentaire::class),
        'sites'=>$this->EntityCount(Site::class),
        'likes'=>$this->EntityCount(Like::class),
        'dislikes'=>$this->EntityCount(Dislike::class),
        'hashtags'=>$this->EntityCount(Hashtag::class)
        ];
        //return parent::index();
        return $this->render('dashboard.html.twig', [
            'stat'=>$stat,
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
            'dashboard_controller_class' => (new \ReflectionClass(static::class))->getShortName(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Genodicsnet');
    }

    public function configureMenuItems(): iterable
    {
        $admin=in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true);
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-undo','app_home')->setPermission('ROLE_USER');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home')->setPermission('ROLE_USER');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Articles', 'fa fa-file-text', Article::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Categorie::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Hashtag', 'fa fa-hashtag', Hashtag::class)->setPermission('ROLE_ADMIN');

       yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Commentaire::class)->setPermission('ROLE_USER');
       yield MenuItem::linkToCrud("J'aime", 'fa fa-thumbs-up', Like::class)->setPermission('ROLE_USER');
       yield MenuItem::linkToCrud("Je n'aime pas", 'fa fa-thumbs-down', Dislike::class)->setPermission('ROLE_USER');

        yield MenuItem::linkToCrud('Media', 'fas fa-video', Media::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Site', 'fas fa-globe', Site::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToLogout('Déconnecter', 'fa fa-sign-out')->setPermission('ROLE_USER');
    
    }
    public function configureUserMenu(UserInterface $user): UserMenu
    {
      return parent::configureUserMenu($user)
            ->setAvatarUrl('uploads/users/'.$user->getPhoto())
            ->displayUserAvatar(true);
           // ->setGravatarEmail($user->getEmail())

    }
}
