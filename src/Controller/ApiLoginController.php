<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Gamer;
use App\Entity\Token;
use App\Security\Users;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiLoginController extends BaseController
{ 
    //   200 ---> succés 
    //   403 ---> email existe
    //   404 ---> not found
    //   405 ---> mdp incorect
    
    
    
    #[Route('/loginmobile/{email}/{password}', name: 'loginmobile')]
    public function loginmobile(NormalizerInterface $normalizer,string $email,string $password)
    {

       
        
        $em2 = $this->managerRegistry->getRepository(Gamer::class);
        $emc = $this->managerRegistry->getRepository(Coach::class);
        $em = $this->managerRegistry->getManagerForClass(Gamer::class);            
        $gamer=new Gamer();
        $gamer = $em2->findOneBy(['email' => $email]);
        if (!$gamer) {
            $coach=new Coach();
            $em2 = $this->managerRegistry->getRepository(Coach::class);
            $coach = $em2->findOneBy(['email' => $email]);
            if (!$coach) {
                $response = [
                    'status' => '404',
                    'message' => 'Utilisateur n\'existe pas',
                ];
            }
            else if (!password_verify($password, $coach->getPassword())) {
                $response = [
                    'status' => '405',
                    'message' => 'Mot de passe incorecte',
                ];
            }else if($coach->isApprouver() && !$coach->isBannir()){
                $response = [
                    'status' => '200',
                    'message' => 'succés',
                    'id'=>$coach->getId(),
                    'nom'=>$coach->getNom(),
                    'prenom'=>$coach->getPrenom(),
                    'email'=>$coach->getEmail(),
                    'about'=>$coach->getAbout(),
                    'solde'=>$coach->getPoint(),
                    'photo'=>$coach->getNom().$coach->getPrenom().'/'.$coach->getPhotoProfil(),
                    'role'=>'coach'
                ];
                $coach->setStatus(True);
                $em->persist($coach);
                $em->flush();
            }
        }
        else if (!password_verify($password, $gamer->getPassword())) {
            $response = [
                'status' => '405',
                'message' => 'Mot de passe incorecte',
            ];
           
        }else if(!$gamer->isBannir()){
            $response = [
                'status' => '200',
                'message' => 'succés',
                'id'=>$gamer->getId(),
                'nom'=>$gamer->getNom(),
                'prenom'=>$gamer->getPrenom(),
                'tag'=>$gamer->getTag(),
                'email'=>$gamer->getEmail(),
                'about'=>$gamer->getAbout(),
                'solde'=>$gamer->getPoint(),
                'photo'=>$gamer->getTag().'/'.$gamer->getPhotoProfil(),
                'role'=>'gamer'
            ];
            
            $gamer->setStatus(True);
            $em->persist($gamer);
            $em->flush();
        }
        
        $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
        $json = json_encode($jeuxNormalises);
        return new  Response($json);
    }


     
    #[Route('/signupgamer/{tag}/{nom}/{prenom}/{phone}/{date}/{about}/{email}/{password}', name: 'signupgamer')]
    public function signupgamer(NormalizerInterface $normalizer,string $tag,string $nom,string $prenom,string $phone,DateTime $date,string $about,string $email,string $password)
    {
        $em = $this->managerRegistry->getManagerForClass(Gamer::class);
        $user = new Gamer();
        $users=new Users();
        $user->setPoint(8000);
        $em2 = $this->managerRegistry->getRepository(Gamer::class);
        $emc = $this->managerRegistry->getRepository(Coach::class);
                if(!$em2->findOneBy(['email' => $email]) && !$emc->findOneBy(['email' => $email])){
                    $user->setTag($tag);
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
                    $user->setPhone($phone);
                    $user->setDateNaissance($date);
                    $user->setEmail($email);
                    $user->setAbout($about);
                $em2 = $this->managerRegistry->getRepository(Gamer::class);
                $hashedPassword = $this->passwordhash->hashPassword(
                    $users,
                   $password
                );
                $user->setPhotoProfil('noimage.jpg');
                $user->setStatus(False);
                $user->setDateCreation(new DateTime());
                $user->setPassword($hashedPassword);
                $user->setBannir(False);
                $em->persist($user);
                $em->flush();
                $response = [
                    'status' => '200',
                    'message' => 'succés',
                ]; 
            
            }else{
                $response = [
                    'status' => '403',
                    'message' => 'email deja utilisé',
                ]; 
            }
            
        $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
        $json = json_encode($jeuxNormalises);
        return new  Response($json);
            }

    

            #[Route('/getuser/{id}', name: 'getuser')]
            public function getuserprofile(NormalizerInterface $normalizer,int $id)
            {
                $gamer = new Gamer();
                $em = $this->managerRegistry->getRepository(Gamer::class);
                $coach = new Coach();
                $em2 = $this->managerRegistry->getRepository(Coach::class);
                $gamer=$em->findOneBy(['id'=>$id]);
                $coach=$em2->findOneBy(['id'=>$id]);
                if($gamer){
                    $response = [
                        'status' => '200',
                        'message' => 'succés',
                        'gamer'=>$gamer,
                    ]; 
                    $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
                    $json = json_encode($jeuxNormalises);
                    return new  Response($json);
                }else if($coach){
                    $response = [
                        'status' => '200',
                        'message' => 'succés',
                        'coach'=>$coach,
                    ]; 
                    $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
                    $json = json_encode($jeuxNormalises);
                    return new  Response($json);
                }else{
                    $response = [
                        'status' => '404',
                        'message' => 'erreur',
                    ]; 
                    $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
                    $json = json_encode($jeuxNormalises);
                    return new  Response($json);
                }

            }



            
    #[Route('/sendmail2/{mail}', name: 'sendmail2')]
    public function sendmail(Request $request,string $mail,NormalizerInterface $normalizer)
{
       $emgamer = $this->managerRegistry->getRepository(Gamer::class);
   $emcoach = $this->managerRegistry->getRepository(Coach::class);
   $gamer=$emgamer->findOneBy(['email'=>$mail]);
   $coach=$emcoach->findOneBy(['email'=>$mail]);
   if($coach){
       $user=new Coach();
       $user=$coach;
   }else{
       $user=new Gamer();
       $user=$gamer;
   }
       $token=new Token();
       $token->setCode(uniqid(10));
       $token->setValidation(new DateTime());
       $token->setUser($user);
       $tokenem=$this->managerRegistry->getManagerForClass(Token::class);
       $tokenem->persist($token);
       $tokenem->flush();
       $this->sendEmail($user->getEmail(),$token->getCode());
       $response = [
        'status' => '200',
        'message' => 'sussés',
    ]; 
    $jeuxNormalises = $normalizer->normalize($response,'json',['groups'=>"jeux"]);
                    $json = json_encode($jeuxNormalises);
                    return new  Response($json);
   }
   
     


}
