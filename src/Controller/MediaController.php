<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @Route("/media", name="app_media")
     */
    public function index(MediaRepository $mediaRepository): Response
    {
        $titre="Media";
        $medias=$mediaRepository->findAll();
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'medias' => $medias
        ]);
    }
        /**
     * @Route("/media/detail/{id}", name="media_detail")
     */
    public function mediaById(MediaRepository $mediaRepository, $id): Response
    {
        $titre="Media";
        
        try {
            $media=$mediaRepository->find($id);
        } catch (\Throwable $th) {
            throw $this->createNotFoundException("pas de media");
        }
        return $this->render('media/detail.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'media' => $media
        ]);
    }
    /**
     * @Route("/media/categorie/{id}", name="media_categorie")
     */
    public function categoryMediaAction(MediaRepository $mediaRepository, CategorieRepository $categorieRepository, $id): Response
    {
        //dd($categorieRepository->find($id));
        try {
            $titre=$categorieRepository->find($id)->getLabel();
            $medias=$categorieRepository->findOneBy(array('label'=>$titre))->getMedia();
       
        } catch (\Throwable $th) {
            throw $this->createNotFoundException("pas de catégorie media");
        }
        
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'medias' => $medias
        ]);
    }
    /**
     * @Route("/media/presse", name="presse")
     */
    public function presseAction(MediaRepository $mediaRepository, CategorieRepository $categorieRepository): Response
    {
        $titre="Presse";
        $medias=$categorieRepository->findOneBy(array('label'=>'Presse'))->getMedia();
       
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'medias' => $medias
        ]);
    }
    /**
     * @Route("/media/temoignages", name="temoignages")
     */
    public function temoignagesAction(MediaRepository $mediaRepository, CategorieRepository $categorieRepository): Response
    {
        $titre="Témoignages";
        $medias=$categorieRepository->findOneBy(array('label'=>'Temoignages'))->getMedia();
       
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'medias' => $medias
        ]);
    }
    /**
     * @Route("/media/reportages", name="reportages")
     */
    public function reportagesAction(MediaRepository $mediaRepository, CategorieRepository $categorieRepository): Response
    {
        $titre="Reportages";
        $medias=$categorieRepository->findOneBy(array('label'=>'Reportages'))->getMedia();
       
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
            'titre'=>$titre,
            'medias' => $medias
        ]);
    }
}
