<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInt;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    
    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, 
                            UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator,
                             EntityManagerInterface $entityManager, \Swift_Mailer $swiftMailer): Response
    {
       
        $user = new User();
        

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            
            
            $nom= $form->get('nom')->getData();
        $prenom= $form->get('prenom')->getData();
        $user->setNom((string)$nom);
        $user->setPrenom((string)$prenom);
         $photo=   $form->get('photo');
         $newImg= new File($user->getPhoto());
         $nomImg= md5(uniqid()).'.'.$newImg->guessExtension();
         $newImg->move("uploads/users/", $nomImg);
         $user->setPhoto($nomImg);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $authenticator,
            //     $request
            // );
            

         // handle the user registration form and persist the new user...
    
         $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'registration_confirmation_route',
            $user->getId(),
            $user->getEmail()
        );
    
   /*  $email = new TemplatedEmail();
    $email->from('send@example.com');
    $email->to($user->getEmail());
    $email->htmlTemplate('registration/confirmation_email.html.twig');
    $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
    $this->mailer->send($email);
     */
    $message = (new \Swift_Message('Genodics Email Verification'))
    ->setFrom('dabbekchakib@gmail.com')
    ->setTo($user->getEmail())
    ->setBody(
        $signatureComponents->getSignedUrl()
    );
    $swiftMailer->send($message);


    

    // generate and return a response for the browser

            $this->addFlash('success', 'Votre inscription est validée. Connectez-vous à présent !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'titre' => 'Inscription'
        ]);
    }
        /**
     * @Route("/verify", name="registration_confirmation_route")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        // Mark your user as verified. e.g. switch a User::verified property to true

        $this->addFlash('success', 'Your e-mail address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
