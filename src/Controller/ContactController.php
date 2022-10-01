<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
    /**
     * @Route("/sendmail", name="sendmail", methods={"POST"})
     */
    public function sendmailAction(Request $request, \Swift_Mailer $mailer): Response
    {
        //dd($request);
        /* $email = (new Email())
            ->from(new Address($request->get('Email'), $request->get('nomPrenom')))
            ->to('contact@genodics.net')
            ->subject($request->get('Subject'))
            ->text($request->get('Message'));
        $mailer->send($email); */
        //dd($mailer);
        $name= $request->get('nomPrenom');
        $email=$request->get('Email');
        $body="<ul>";
        $body.="<li>"."Nom & Prénom: ".$name."</li>"."<br>";
        $body.="<li>"."E-mail: <a href='mailto:$email'>".$request->get('Email')."</a>"."</li>"."<br>";
        $body.="<li>"."Message: ".$request->get('Message')."</li>";
        $body.="</ul>";
        $message = (new \Swift_Message('Formulaire de contact [genodics.be]'))
        ->setFrom('Rachi@genodics.net')
        ->setTo('Rachi@genodics.net')
        ->setBody(
            $body,
            'text/html'
            
        )
        ;

     
        
        try {
            $mailer->send($message);
            $msg='Email envoyé avec succée';
        } catch (\Throwable $th) {
            $msg="Problème d'envoie de votre email";
        }
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'Contact',
            'msg' => $msg
        ]);
    }
}
