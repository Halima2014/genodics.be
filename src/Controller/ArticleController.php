<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use App\Entity\Dislike;
use App\Entity\Like;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use App\Repository\DislikeRepository;
use App\Repository\HashtagRepository;
use App\Repository\LikeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $titre='Articles';
        $articles=$articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'titre'=>$titre,
            'articles'=>$articles
        ]);
    }
    /**
     * @Route("/article/{id}", name="article_single")
     */
    public function articleByIdAction(DislikeRepository $dislikeRepository,ArticleRepository $articleRepository,LikeRepository $likeRepository, $id): Response
    {   
        // dd($id);
        if (!$article=$articleRepository->find($id)) {
            throw $this->createNotFoundException("pas d'article");
        }
        
        $commentaires=$article->getCommentaires();
        $idArticle = $article->getId();
        $user=$this->getUser();
        $islike='true';
        $isdislike='true';
        if ($like=$likeRepository->findOneBy(['user'=>$user,'article'=>$article])) {
            $islike='false';
        }
        if ($dislike=$dislikeRepository->findOneBy(['user'=>$user,'article'=>$article])) {
            $isdislike='false';
        }

        return $this->render('article/detail.html.twig', [
            'controller_name' => 'ArticleController',
            'article'=>$article,
            'commentaires'=>$commentaires,
            'idArticle'=> $idArticle,
            'isdislike' => $isdislike,
            'islike' => $islike    
           ]);
    }
    /**
     * @Route("/article/categorie/{categorie}", name="article_categorie")
     */
    public function articleByCategoryAction(CategorieRepository $categorieRepository,$categorie): Response
    {
        if (!$categorie=$categorieRepository->find($categorie)) {
            throw $this->createNotFoundException("pas de catégorie");
        }
        
        $childCats=$categorieRepository->findBy(['parent'=>$categorie->getId()]);
        $articles=$categorie->getArticles();
        foreach ($childCats as $cat) {
            $child_articles=$cat->getArticles();
            foreach ($child_articles as $article) {
                $articles[]=$article;
            }
        }
       $titre=$categorie->getLabel();
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'titre'=>$titre,
            'articles'=>$articles
        ]);
    }

     /**
     * @Route("/article/ajout/comment", name="ajout_comment", methods={"POST"})
     */
    public function AjoutComment(Request $request, CommentaireRepository $commentaireRepository ,ArticleRepository $articleRepository, EntityManagerInterface $em ): Response
    {
        try {
            $parent= $commentaireRepository->find($request->get('parent'));
        } catch (\Throwable $th) {
            $parent= null;
        }
        $message = $request->get('Message');
        $user = $this->getUser();
        $idArticle = $request->get('idArticle');
        $article= $articleRepository->find($idArticle);
        $comment = new Commentaire();
        $comment->setUtilisateur($user);
        $comment->setArticle($article);
        $comment->setContenu($message);
        $comment->setPublier(false);
        $comment->setParent($parent);
        $comment->setCreatedAt(new DateTimeImmutable('now'));

        //$parentId = $request->get('Message')->getData();
        //$parent = $em->getRepository(Commentaire::class)->find($parentId);
       // $comment->setParent($parent);

        $em->persist($comment);
        $em->flush();
        $titre= 'article';
        // return $this->render('article/detail.html.twig', [
        //     'controller_name' => 'ArticleController',
        //     'titre'=>$titre,
        //     'article'=>$article
        // ]);
        return $this->redirectToRoute("article_single",[
            'id'=> $idArticle
        ]);
    }
        /**
     * @Route("/article/hashtag/{label}", name="article_hashtag")
     */
    public function articleByHashtagAction(HashtagRepository $hashtagRepository, $label): Response
    {
       
        try {
            $articles=$hashtagRepository->findOneBy(['label'=>$label])->getArticles();
        } catch (\Throwable $th) {
            throw $this->createNotFoundException("pas de hashtag");
        }
       $titre=$label;
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'titre'=>$titre,
            'articles'=>$articles
        ]);
    }
    /**
     * @Route("/article/{idarticle}/like/{on}", name="ajout_like", methods={"GET"})
     */
    public function AjoutLike(Request $request,$idarticle,$on,LikeRepository $likeRepository, ArticleRepository $articleRepository, EntityManagerInterface $em ): Response
    {

        $user = $this->getUser();
        if ($user==null) {
            return $this->redirectToRoute("app_login");
        }
        $article=$articleRepository->find($idarticle);
        $like=new Like();
        $like->setUser($user);
        $like->setArticle($article);
       if ($on=='true') {
        $em->persist($like);
        $em->flush();
       } else {
            $like=$likeRepository->findOneBy(['user'=>$user,'article'=>$article]);
            //dd($like);
            $em->remove($like);
            $em->flush();
       }
            return $this->redirectToRoute("article_single",[
                'id'=> $idarticle
            ]);
      
        
    }

    /**
     * @Route("/article/{idarticle}/dislike/{on}", name="ajout_dislike", methods={"GET"})
     */
    public function AjoutDislike(Request $request,$idarticle,$on,DislikeRepository $dislikeRepository, ArticleRepository $articleRepository, EntityManagerInterface $em ): Response
    {

        $user = $this->getUser();
        if ($user==null) {
            return $this->redirectToRoute("app_login");
        }
        $article=$articleRepository->find($idarticle);
        $dislike=new Dislike();
        $dislike->setUser($user);
        $dislike->setArticle($article);
       if ($on=='true') {
        $em->persist($dislike);
        $em->flush();
       } else {
            $dislike=$dislikeRepository->findOneBy(['user'=>$user,'article'=>$article]);
            //dd($like);
            $em->remove($dislike);
            $em->flush();
       }
            return $this->redirectToRoute("article_single",[
                'id'=> $idarticle
            ]);
      
        
    }
     
}
