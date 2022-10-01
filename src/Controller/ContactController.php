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
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom($request->get('Email'))
        ->setTo('benkhalifahalima@hotmail.com')
        ->setBody(
            $request->get('Message')
        );

     
        
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
