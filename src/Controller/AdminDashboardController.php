<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Coach;
use App\Entity\Gamer;
use App\Form\LoginType;
use App\Repository\CoachRepository;
use App\Repository\GamerRepository;
use App\Repository\HistoriquePointRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends BaseController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(HistoriquePointRepository $historiquePointRepository,GamerRepository $gamerRepository): Response
    {
        $revenu=$historiquePointRepository->pointpositive();
        $count=$gamerRepository->countt();
        $revenumagasin=$gamerRepository->countrevenu();

        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController','revenu'=>$revenu,'count'=>$count*8000,'revenumagasin'=>$revenumagasin
        ]);
    }


    #[Route('/admin', name: 'admin')]
    public function admin_login() {
        $formLogin = $this->createForm(LoginType::class);
            $formLogin->handleRequest($this->request);
                if ($formLogin->isSubmitted() && $formLogin->isValid()) {
                    $data = $formLogin->getData();
                    $admin=$this->managerRegistry->getRepository(Admin::class)->findOneBy((['Email' => $data->getEmail()]));
                    if($admin && password_verify($data->getPassword(), $admin->getPassword())){
                        $this->session->set('User_name',$admin->getNom() );
                        $this->session->set('Admin_id',$admin->getId() );
                        $this->session->set('Session_time', new DateTime());
                        return $this->redirect("/admin/dashboard");
                    }
                }
        return $this->renderForm('admin_dashboard/login.html.twig',[
            'controller_name' => 'AdminDashboardController','formLogin'=>$formLogin
        ]);
    }

    #[Route('/logout_admin', name: 'logout_admin')]
    public function logout() {
        $this->session->invalidate();
        return $this->redirect("/admin");
    }

    #[Route('/gamers', name: 'gamers')]
    public function gamers(GamerRepository $gamerrepo):Response {
        $this->session->invalidate();
        $gamers=$gamerrepo->findByban(false);
        $banned=$gamerrepo->findByban(true);
        return $this->render('admin_dashboard/gamers.html.twig',[
            'controller_name' => 'AdminDashboardController','gamers'=>$gamers,'banned'=>$banned
        ]);
    }

    #[Route('/coach', name: 'coach')]
    public function coachs(CoachRepository $coachrepo):Response {
        $this->session->invalidate();
        $demande=$coachrepo->finddemande(0,false);
        $banned=$coachrepo->findByban(true);
        $coach=$coachrepo->findactive(1,false);
        return $this->render('admin_dashboard/coach.html.twig',[
            'controller_name' => 'AdminDashboardController','coach'=>$coach,'banned'=>$banned,'demande'=>$demande
        ]);
    }

    #[Route('/bannir/{id}', name: 'bannir')]
public function bannir(int $id): Response
{
    $em = $this->managerRegistry->getManagerForClass(Gamer::class);
    $em2 = $this->managerRegistry->getRepository(Gamer::class);
    $gamer=new Gamer();
    $gamer = $em2->findOneBy(['id' => $id]);
    if($gamer->isBannir()){
        $gamer->setBannir(False);
    }else if(! $gamer->isBannir()){
        $gamer->setBannir(True);
    }
   
    $em->persist($gamer);
    $em->flush();
    return $this->redirect("/gamers");
}

#[Route('/bannircoach/{id}', name: 'bannircoach')]
public function bannircoach(int $id): Response
{
    $em = $this->managerRegistry->getManagerForClass(Coach::class);
    $em2 = $this->managerRegistry->getRepository(Coach::class);
    $coach=new Coach();
    $coach = $em2->findOneBy(['id' => $id]);
    if($coach->isBannir()){
        $coach->setBannir(False);
    }else if(! $coach->isBannir()){
        $coach->setBannir(True);
    }
   
    $em->persist($coach);
    $em->flush();
    return $this->redirect("/coach");
}


#[Route('/demandercoach/{id}', name: 'demandercoach')]
public function demandercoach(int $id): Response
{
    $em = $this->managerRegistry->getManagerForClass(Coach::class);
    $em2 = $this->managerRegistry->getRepository(Coach::class);
    $coach=new Coach();
    $coach = $em2->findOneBy(['id' => $id]);
    $coach->setApprouver(1);
    $em->persist($coach);
    $em->flush();
    return $this->redirect("/coach");
}
}
