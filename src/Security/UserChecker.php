<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if ($user->getIsVerified()==false) {
            throw new CustomUserMessageAuthenticationException("votre E-mail non verifié");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}
?>