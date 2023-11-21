<?php

namespace App\Controller;
use App\Entity\Coach;
use App\Entity\Gamer;
use App\Form\CoachType;
use App\Form\GamerType;
use App\Form\LoginType;
use App\Security\Users;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionEsportController extends BaseController
{
    
    #[Route('/acceuil', name: 'acceuil')]
    public function acceuil(Request $request): Response{
        $this->session=$request->getSession();
        if(!$this->check_session()){
            return $this->redirect("/");
        }
        return $this->renderForm('Front_Template/acceuil.html.twig',[
            'controller_name' => 'GestionEsportController',
        ]);
    }

    #[Route('/confirmemail/{mail}', name: 'confirmemail')]
    public function confirmemail(Request $request, string $mail): Response
    {
        $em2 = $this->managerRegistry->getRepository(Gamer::class);
        $emc = $this->managerRegistry->getRepository(Coach::class);

        $gamer = $em2->findOneBy(['email' => $mail]);
        $coach = $emc->findOneBy(['email' => $mail]);

        $entityManager = null;

        if ($gamer) {
            $entityManager = $this->managerRegistry->getManagerForClass(Gamer::class);
            $gamer->setValidEmail(1);
        } elseif ($coach) {
            $entityManager = $this->managerRegistry->getManagerForClass(Coach::class);
            $coach->setValidEmail(true);
        }

        if ($entityManager) {
            $entityManager->persist($gamer ?? $coach);
            $entityManager->flush();
            return $this->redirect("/");
        } else {
            // Aucun utilisateur trouvé avec cet e-mail
            // Gérez cette situation en redirigeant vers une page d'erreur ou en affichant un message approprié.
            // Vous pouvez personnaliser cela en fonction de vos besoins.
            throw $this->createNotFoundException('Aucun utilisateur trouvé avec cet e-mail.');
        }
    }


    #[Route('/logout', name: 'logout')]
    public function logout() {
        if($this->session->has('Gamer_id')){
            $em = $this->managerRegistry->getManagerForClass(Gamer::class);
            $em2 = $this->managerRegistry->getRepository(Gamer::class);
            $gamer=new Gamer();
            $gamer = $em2->findOneBy(['id' => $this->session->get('Gamer_id')]);
            $gamer->setStatus(False);
            $em->persist($gamer);
            $em->flush();
            $this->session->invalidate();
            return $this->redirect("/");
        }else if($this->session->has('Coach_id')){
            $em = $this->managerRegistry->getManagerForClass(Coach::class);
            $em2 = $this->managerRegistry->getRepository(Coach::class);
            $gamer=new Coach();
            $gamer = $em2->findOneBy(['id' => $this->session->get('Coach_id')]);
            $gamer->setStatus(False);
            $em->persist($gamer);
            $em->flush();
            $this->session->invalidate();
            return $this->redirect("/");
        }
    }
     
    #[Route('/', name: 'home')]
    public function signin_signout(Request $request): Response
    {   $this->session=$request->getSession();
        if($this->check_session()){
            return $this->redirect("/acceuil");
        }
            $em = $this->managerRegistry->getManagerForClass(Gamer::class);
            $user = new Gamer();
            $users=new Users();
            $user->setPoint(8000);
            $form = $this->createForm(GamerType::class, $user);
            $em2 = $this->managerRegistry->getRepository(Gamer::class);
            $emc = $this->managerRegistry->getRepository(Coach::class);
            $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    if(!$em2->findOneBy(['email' => $form->get('email')->getData()]) && !$emc->findOneBy(['email' => $form->get('email')->getData()])){

                   
                    $photoP = $form->get('photoprofile')->getData();
                    
                        // Handle image upload
                   
                    if ($photoP) {
                        if (in_array($photoP->guessExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $originalImgName = pathinfo($photoP->getClientOriginalName(), PATHINFO_FILENAME);
        
                        // this is needed to safely include the file name as part of the URL
                        $safeImgname = $this->slugger->slug($originalImgName);
                        $newImgename = $safeImgname . '-' . uniqid() . '.' . $photoP->guessExtension();
                        // Move the file to the directory where brochures are stored
                        try {
                            $filesystem = new Filesystem();
                            $imgDirectory = $this->getParameter('img_profile_directory') . '/' . $user->getTag();
                            if (!$filesystem->exists($imgDirectory)) {
                                $filesystem->mkdir($imgDirectory);
                            }                            
        
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }
        
                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $user->setPhotoProfil($newImgename);
                        $photoP->move($imgDirectory, $newImgename);
                    } 
                   

                    $em2 = $this->managerRegistry->getRepository(Gamer::class);
                    
                    $hashedPassword = $this->passwordhash->hashPassword(
                        $users,
                        $user->getPassword()
                    );
                    $user->setStatus(False);
                    $this->validemail($user->getEmail(),$user->getNom());
                    $user->setValidEmail(false);
                    $user->setDateCreation(new DateTime());
                    $user->setPassword($hashedPassword);
                    $user->setBannir(False);
                    $em->persist($user);
                    $em->flush();
                } else {
                    $this->addFlash('error', 'Il faut une image');
                     }
                }else{
                    $form->get('email')->addError(new FormError('Email deja utilisé'));
                }
                }
            $formLogin = $this->createForm(LoginType::class);
            $formLogin->handleRequest($request);
                if ($formLogin->isSubmitted() && $formLogin->isValid()) {
                    $data = $formLogin->getData();
                    $gamer=new Gamer();
                    $gamer = $em2->findOneBy(['email' => $data->getEmail()]);
                    if (!$gamer) {
                        $coach=new Coach();
                        $em2 = $this->managerRegistry->getRepository(Coach::class);
                        $coach = $em2->findOneBy(['email' => $data->getEmail()]);
                        if (!$coach) {
                            $formLogin->get('email')->addError(new FormError('Email n\' existe pas'));
                            return $this->renderForm('Front_Template/welcome.html.twig',[
                                'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                            ]);  
                        }
                        else if (!password_verify($data->getPassword(), $coach->getPassword())) {
                            $formLogin->get('password')->addError(new FormError('Mot de passe incorrecte'));
                            return $this->renderForm('Front_Template/welcome.html.twig',[
                                'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                            ]);  
                        }else if(!$coach->isValidEmail()){
                            $formLogin->get('email')->addError(new FormError('Votre email n\'est pas verifier'));
                            return $this->renderForm('Front_Template/welcome.html.twig',[
                                'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                            ]);  
                        }
                        else if($coach->isApprouver() && !$coach->isBannir()){
                            $coach->setStatus(True);
                            $em->persist($coach);
                            $em->flush();
                            $this->session->set('Coach_id', $coach->getId());
                            $this->session->set('Solde', $coach->getPoint());
                            $this->session->set('User_name', $coach->getNom());
                            $this->session->set('Session_time', new DateTime());
                            $this->session->set('Photo', $coach->getNom().$coach->getPrenom().'/'.$coach->getPhotoProfil());
                            return $this->redirect("/acceuil");
                        }
                    }
                    else if (!password_verify($data->getPassword(), $gamer->getPassword())) {
                        $formLogin->get('password')->addError(new FormError('Mot de passe incorrecte'));
                        return $this->renderForm('Front_Template/welcome.html.twig',[
                            'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                        ]);  
                    }else if(!$gamer->isValidEmail()){
                        $formLogin->get('email')->addError(new FormError('Votre email n\'est pas verifier'));
                        return $this->renderForm('Front_Template/welcome.html.twig',[
                            'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                        ]);  
                    }else if(!$gamer->isBannir()){
                        $this->session->set('User_name', $gamer->getTag());
                        $this->session->set('Solde', $gamer->getPoint());
                        $this->session->set('Gamer_id', $gamer->getId());
                        $this->session->set('Session_time', new DateTime());
                        $this->session->set('Photo', $gamer->getTag().'/'.$gamer->getPhotoProfil());
                        $gamer->setStatus(True);
                        $em->persist($gamer);
                        $em->flush();
                        return $this->redirect("/acceuil");
                    }
                }
                return $this->renderForm('Front_Template/welcome.html.twig',[
                    'controller_name' => 'GestionEsportController','formcreateuser' => $form,'formLogin'=>$formLogin
                ]);  
    }


    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response{
        $em = $this->managerRegistry->getManagerForClass(Coach::class);
        $coach=new Coach();
            $users=new Users();
            $coach->setPoint(100);
        $form = $this->createForm(CoachType::class, $coach);
        $emg = $this->managerRegistry->getRepository(Gamer::class);
            $form->handleRequest($request);
            $emcoach = $this->managerRegistry->getRepository(Coach::class);
            if ($form->isSubmitted() && $form->isValid()) {
                if(!$emcoach->findOneBy(['email' => $form->get('email')->getData()]) && !$emg->findOneBy(['email' => $form->get('email')->getData()])){

               
                $photoP = $form->get('photoprofile')->getData();
                if ($photoP) {
                    if (in_array($photoP->guessExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $originalImgName = pathinfo($photoP->getClientOriginalName(), PATHINFO_FILENAME);
    
                    // this is needed to safely include the file name as part of the URL
                    $safeImgname = $this->slugger->slug($originalImgName);
                    $newImgename = $safeImgname . '-' . uniqid() . '.' . $photoP->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $filesystem = new Filesystem();
                        $imgDirectory = $this->getParameter('img_profile_directory') . '/' . $coach->getNom().$coach->getPrenom();
                        if (!$filesystem->exists($imgDirectory)) {
                            $filesystem->mkdir($imgDirectory);
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
    
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $coach->setPhotoProfil($newImgename);
                }}
                $photoC = $form->get('cv')->getData();
                if ($photoC) {
                    $originalImgName2 = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);
    
                    // this is needed to safely include the file name as part of the URL
                    $safeImgname2 = $this->slugger->slug($originalImgName2);
                    $newImgename2 = $safeImgname2 . '-' . uniqid() . '.' . $photoC->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $filesystem = new Filesystem();
                        $imgDirectory2 = $this->getParameter('img_profile_directory') . '/' . $coach->getNom().$coach->getPrenom();
                        if (!$filesystem->exists($imgDirectory2)) {
                            $filesystem->mkdir($imgDirectory2);
                        }
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
    
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $coach->setCv($newImgename2);
                }
                $hashedPassword = $this->passwordhash->hashPassword(
                    $users,
                    $coach->getPassword()
                );
               
                $coach->setReview(2);
                $coach->setDateCreation(new DateTime());
                $this->validemail($coach->getEmail(),$coach->getNom());
                $photoP->move($imgDirectory, $newImgename);
                $photoC->move($imgDirectory2, $newImgename2);
                $coach->setPassword($hashedPassword);
                $coach->setValidEmail(false);
                $coach->setBannir(False);
                $coach->setStatus(0);
                $coach->setApprouver(0);
                $em->persist($coach);
                $em->flush();
                return $this->redirect("/acceuil");
            }else{
                $form->get('email')->addError(new FormError('Email deja utilisé'));
                return $this->renderForm('Front_Template/contact.html.twig',[
                    'controller_name' => 'GestionEsportController','form'=>$form
                ]);
            }
            }
        return $this->renderForm('Front_Template/contact.html.twig',[
            'controller_name' => 'GestionEsportController','form'=>$form
        ]);
    }
    
}
