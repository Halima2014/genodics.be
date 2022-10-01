<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\HashtagRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(HashtagRepository $hashtagRepository,ArticleRepository $articleRepository,SiteRepository $siteRepository, CategorieRepository $categorieRepository): Response
    {
        $listeCategories=$categorieRepository->findOneBy(['label'=>'Publications'])->getCategories();
        //$listeCategories=$listeCategories->getCategories();
        //dd($listeCategories);
        $publications=[];
        foreach ($listeCategories as $categorie) {
            //echo $categorie->getLabel();
            if (count($categorie->getArticles())>=1) {
                $articles=$categorie->getArticles();
                foreach ($articles as $article) {
                    $publications[]= $article;
                }
                
            }
            
        }
        //dd($publications);
        $hashtags=$hashtagRepository->findAll();
        $sites=$siteRepository->findAll();
        $categories=$categorieRepository->findBy(['parent'=>null]);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sites'=> $sites,
            'hashtags'=> $hashtags,
            'categories'=> $categories,
            'publications'=> $publications
        ]);
    }
}
