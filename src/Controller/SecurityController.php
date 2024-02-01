<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\GithubClient;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{

    private $facebookProvider; 
    private $googleProvider;

    public function __construct()
   {
        $this->facebookProvider=new Facebook([
            'clientId'          => $_ENV['FACEBOOK_ID'],
            'clientSecret'      => $_ENV['FACEBOOK_SECRET'],
            'redirectUri'       => $_ENV['FACEBOOK_CALLBACK'],
            'graphApiVersion'   => 'v15.0',
        ]);


        $this->googleProvider=new Google([
            'clientId'          => $_ENV['GOOGLE_ID'],
            'clientSecret'      => $_ENV['GOOGLE_SECRET'],
            'redirectUri'       => $_ENV['GOOGLE_CALLBACK'],
            'accessType'   => 'offline',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    } 

    #[Route('/connect/facebook', name: 'facebook_connect')]
    public function fcbLogin(): Response
    { 
        $helper_url=$this->facebookProvider->getAuthorizationUrl();
        return $this->redirect($helper_url);
    }

    #[Route('/fcb-callback', name: 'connect_facebook_check')]
    public function fcbCallBack(UserRepository $userDb, EntityManagerInterface $manager): Response
    { 
       $token = $this->facebookProvider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
        ]);
        dd($token);
       try { 

           $user=$this->facebookProvider->getResourceOwner($token); 
           $user=$user->toArray(); 
           $email=$user['email']; 
           $nom=$user['name']; 
           dd($user);
           $user_exist=$userDb->findOneByEmail($email); 
           if($user_exist)
           {
                $user_exist->setNom($nom);
                $manager->flush(); 
                return $this->redirectToRoute('home_app', [
                    'nom'=>$nom,
                ]);  
           }else {
                $new_user=new User(); 
                $new_user->setNom($nom)
                      ->setEmail($email)
                      ->setPassword(sha1(str_shuffle('abscdop123390hHHH;:::OOOI')));
              
                $manager->persist($new_user); 
                $manager->flush(); 
                return $this->redirectToRoute('home_app', [
                    'nom'=>$nom,
                ]);  
           }


       } catch (\Throwable $th) { 
          return $th->getMessage();
       }  
    } 
    #[Route('/connect/google', name: 'google_connect')]
    public function googleLogin(): Response
    {
         
        $helper_url=$this->googleProvider->getAuthorizationUrl();
        return $this->redirect($helper_url);
    } 

    #[Route('/connect/google/check', name:'connect_google_check')]
    public function googleConnectCheck(): response
    {
        $token = $this->googleProvider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
            ]);
        
        try {

            // We got an access token, let's now get the owner details
            $user = $googleProvider->getResourceOwner($token);
            $user=$user->toArray(); 
            $email=$user['email']; 
            $name=$user['name'];  
            if($user_exist)
           {
                $user_exist->setNom($name);
                $manager->flush(); 
                return $this->render('home/index.html.twig', [
                    'name'=>$name,
                ]);  
           }else {
                $new_user=new User(); 
                $new_user->setNom($nom)
                      ->setEmail($email)
                      ->setPassword(sha1(str_shuffle('abscdop123390hHHH;:::OOOI')));
              
                $manager->persist($new_user); 
                $manager->flush(); 
                return $this->render('home/index.html.twig', [
                    'name'=>$name,
                ]); 
           }
    
        } catch (\Throwable $th) { 
            return $th->getMessage();
         }
         
        
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
