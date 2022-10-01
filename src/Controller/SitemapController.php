<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap/sitemap.xml", name="app_sitemap")
     */
    public function index(Request $request): Response
    {
        
        $urls = array();
        $hostname = $request->getSchemeAndHttpHost();
 
        
       
 
        
 
            $urls[] = array(
                'loc' => $this->generateUrl('app_home'),
            );
            $urls[] = array(
                'loc' => $this->generateUrl('app_contact'),
            );
            $urls[] = array(
                'loc' => $this->generateUrl('app_media'),
            );
            $urls[] = array(
                'loc' => $this->generateUrl('app_register'),
            );
            $urls[] = array(
                'loc' => $this->generateUrl('app_login')
            );

            
                
               
                
        
       
 
        // return response in XML format
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', array( 'urls' => $urls,
                'hostname' => $hostname)),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');
 
        return $response;

    }
}
