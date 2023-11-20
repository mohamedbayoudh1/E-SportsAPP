<?php

namespace App\Controller;

use App\Entity\Classement;
use App\Entity\Gamer;
use App\Entity\Groupe;
use App\Entity\Membre;
use App\Entity\Notif;
use App\Entity\Team;
use App\Entity\Tournoi;
use App\Form\ClassementType;
use App\Form\GroupeType;
use App\Form\Team2Type;
use App\Form\Tournoi2Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\TeamType;
use App\Form\TournoiType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Notifier\Notification\Notification;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Extension\AbstractExtension;

class TournoiController extends BaseController
{



    #[Route('/tournoi', name: 'tournoi')]
    public function tournoi(ManagerRegistry $doctrine,Request $request): Response
    {
        $tournoi = $doctrine->getRepository(Tournoi::class)->findAll();
        $classemen=$doctrine->getRepository(Classement::class)->findAll();
        return $this->renderForm('tournoi/tournoi.html.twig',['tournoi'=>$tournoi ,'classment'=>$classemen]);

    }
    #[Route('/send-notification/{id}', name: 'send_notification')]
    public function sendNotification(Request $request, int $id, ManagerRegistry $doctrine): JsonResponse
    {
        $gamer = $this->getDoctrine()->getRepository(Gamer::class)->find($id);

        if (!$gamer) {
            return new JsonResponse(['success' => false, 'message' => 'Classement not found for ID ' . $id]);
        }

        $score = $request->request->get('score');
        $teamName = $request->request->get('team_name');
        $notif = new Notif();
        $em = $doctrine->getManager();
        $notif->setOwner($gamer);
        $notif->setContenet("Team name: " . $teamName . " Score: " . $score);
        $em->persist($notif);
        $em->flush();

        return new JsonResponse(['success' => true, 'message' => 'Notification sent successfully.']);
    }




    #[Route('/tournoiapi', name: 'tournoiapi')]
    public function tournoiapi(ManagerRegistry $doctrine,Request $request,SerializerInterface $normalizer): Response
    {
        $tournois = $doctrine->getRepository(Tournoi::class)->findAll();
        $json = $normalizer->serialize($tournois,'json',['groups'=>"Tournoi"]);
        return new Response($json);
    }
    #[Route('/mytournoi', name: 'mytournoi')]
    public function mytournoi(ManagerRegistry $doctrine,Request $request): Response
    {

        $tournois = $doctrine->getRepository(Tournoi::class)->findAll();
        return $this->renderForm('tournoi/mytournoi.html.twig',['tournois'=>$tournois]);


    }


    #[Route('/backtournoi', name: 'backtournoi')]
    public function batournoi(ManagerRegistry $doctrine): Response
    {
        $tournois= $doctrine->getRepository(Tournoi::class)->findAll();

        return $this->render('tournoi/tournoiback.html.twig',['tournoi'=>$tournois]);

    }



    #[Route('/teams', name: 'teams')]
    public function teams(ManagerRegistry $doctrine): Response
    {
        $teams = $doctrine->getRepository(Team::class)->findAll();

        $teamMemberCounts = [];
        foreach ($teams as $team) {
            $memberCount = $doctrine->getRepository(Membre::class)->count([
                'idTeam' => $team->getId(),
            ]);
            $teamMemberCounts[$team->getId()] = $memberCount;
        }

        return $this->render('tournoi/teams.html.twig', [
            'teams' => $teams,
            'teamMemberCounts' => $teamMemberCounts,
        ]);
    }

    #[Route('/myteam', name: 'myteam')]
    public function myteam(ManagerRegistry $doctrine): Response
    {
        $teams= $doctrine->getRepository(Team::class)->findAll();

        return $this->render('tournoi/myteam.html.twig',['teams'=>$teams]);

    }
    /**
     * @Route("/jtournoi/{id}", name="join")
     */
    public function joinTeam(Request $request, $id, ManagerRegistry $doctrine)
    {

        $classement = new Classement();
        $gamerId = $request->getSession()->get('Gamer_id');
        $classement->setGamer($doctrine->getRepository(Gamer::class)->find($gamerId));
        $form = $this->createForm(ClassementType::class, $classement, [
            'gamer_id' => $gamerId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData(); // This will contain the data submitted in the form
            $idTeam = $data->getIdTeam();

            return $this->redirectToRoute('jeoin', ['id2' => $idTeam, 'id' => $id]);
        }

        return $this->renderForm('tournoi/rejoinder.html.twig', [
            'form' => $form,
            'i' => $id
        ]);
    }
    /**
     * @Route("/jetournoi/{id}/{id2}", name="jeoin")
     */
    public function jeoinTeam(Request $request, $id, $id2, ManagerRegistry $doctrine)
    {
        $classementRepo = $doctrine->getRepository(Classement::class);
        $classement = $classementRepo->findOneBy([
            'idTournois' => $id,
            'idTeam' => $id2
        ]);

        if ($classement) {
            $errorMessage = 'This team is already in the Classement for this tournament.';
            $this->addFlash('danger', $errorMessage);
            return $this->redirectToRoute('tournoi');
        }


        $classement = new Classement();
        $team = $doctrine->getRepository(Team::class)->find($id2);
        $tournoi = $doctrine->getRepository(Tournoi::class)->find($id);
        $classement->setIdTournois($tournoi);
        $classement->setIdTeam($team);
        $classement->setScore(0);
        $classement->setEtat(0);

        $membreRepo = $doctrine->getRepository(Membre::class);
        $membersCount = $membreRepo->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.idTeam = :idTeam')
            ->setParameter('idTeam', $id2)
            ->getQuery()
            ->getSingleScalarResult();

        if ($membersCount < $tournoi->getNbJoueurTeam() || $membersCount>$tournoi->getNbJoueurTeam() ) {
            $errorMessage = 'This team has more members than the maximum number of players allowed in the tournament.';
            $this->addFlash('danger', $errorMessage);
            return $this->redirectToRoute('tournoi');
        }

        $creator = $team->getOwnerteam();
        $joinedGroup = $classementRepo->createQueryBuilder('c')
            ->join('c.idTeam', 't')
            ->where('t.ownerteam = :ownerteam')
            ->andWhere('c.idTournois = :idTournois')
            ->setParameter('ownerteam', $creator)
            ->setParameter('idTournois', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if ($joinedGroup) {
            $errorMessage = 'The creator of this team has already joined another group in the tournament.';
            $this->addFlash('danger', $errorMessage);
            return $this->redirectToRoute('tournoi');
        }


        $notif=new Notif();
        $notif->setOwner($team->getOwnerteam());
        $notif->setContenet("voter team a ete inscrit");
        $nbT=$classement->getIdTournois()->getNbParticipant();
        $classement->getIdTournois()->setNbParticipant($nbT+1);

        $em = $doctrine->getManager();
        $em->persist($classement);
        $em->persist($notif);
        $em->flush();

        $this->addFlash('success', 'Team added to the Classement for this tournament.');

        $classements = $classementRepo->findBy(['idTournois' => $id]);

        $tournoil = $doctrine->getRepository(Tournoi::class)->findAll();
        $classemen=$doctrine->getRepository(Classement::class)->findAll();
  //  $count1
        return $this->render('tournoi/tournoi.html.twig', [
            'tournoi' => $tournoil,'classment'=>$classemen
        ]);
    }




    /**
     * @Route("/incrementScore", name="increment_score")
     */
    public function incrementScore(Request $request)
    {
        $classementId = $request->request->get('id');
        $doctrine = $this->getDoctrine();

        $classement = $doctrine->getRepository(Classement::class)->find($classementId);
        $win= $classement->getIdTeam()->getWin();
        $etat= $classement->getEtat();
        $classement->getIdTeam()->setWin($win + 1);

        if (!$classement) {
            throw $this->createNotFoundException('No Classement found for id ' . $classementId);
        }

        $score = $classement->getScore();
        $classement->setScore($score + 1);
        $classement->setEtat($etat+1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($classement);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/notifi", name="notifi")
     */
    public function Notifi(Request $request)
    {
        $tournoiId = $request->request->get('tournoiId');

        $gamers = $this->managerRegistry->getRepository(Gamer::class)
            ->findBy(['idTournois' => $tournoiId]);

        $notif = new Notif();
        $notif->setOwner($gamers);
        $notif->setContenet("noter score est");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($notif);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }


    /**
     * @Route("/decrementScore", name="decrement_score")
     */
    public function decrementScore(Request $request)
    {
        $classementId = $request->request->get('id');
        $doctrine = $this->getDoctrine();

        $classement = $doctrine->getRepository(Classement::class)->find($classementId);
        $lose= $classement->getIdTeam()->getLose();
        $classement->getIdTeam()->setLose($lose + 1);
        if (!$classement) {
            throw $this->createNotFoundException('No Classement found for id ' . $classementId);
        }

        $score = $classement->getScore();
        $classement->setEtat(-1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($classement);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/consultetournoi/{id}", name="consultetournoi")
     */
    public function consultetournoi(Request $request, $id, ManagerRegistry $doctrine)
    {

// Retrieve data from database

$classementRepo = $doctrine->getRepository(Classement::class);
$tournoi = $doctrine->getRepository(Tournoi::class)->find($id);
$classements = $classementRepo->findBy(['idTournois' => $id]);

// Get number of teams from first Classement object
$nbT = 0;
if (count($classements) > 0) {
    $classement = $classements[0];
    if (is_object($classement) && method_exists($classement, 'getIdTournois')) {
        $nbT = $classement->getIdTournois()->getNbParticipant();
    }
}

        $em = $this->getDoctrine()->getManager();
        $classements = $classementRepo->findBy(['idTournois' => $id]);
        $qb = $em->createQueryBuilder();

// Build the query
// Build the query
        $qb->select('COUNT(c.id)')
            ->from('App\Entity\Classement', 'c')
            ->where('c.etat = :etat')
            ->setParameter('etat', 1);

        $count = $qb->getQuery()->getSingleScalarResult();

// Execute the query and fetch the result
// Pass data to Twig template

return $this->render('tournoi/division.html.twig', [
    'classements1' => $classements,
    'tournoi' => $tournoi,
    'nbTeam' => $nbT,
    'counts'=>$count,
]);

    }

    /**
     * @Route("/changenextround", name="change_next_round")
     */
    public function changenextround(Request $request, ManagerRegistry $doctrine)
    {
        $classementRepo = $doctrine->getRepository(Classement::class);
        $classement1 = $classementRepo->find($request->get('classement1Id'));
        $classement2 = $classementRepo->find($request->get('classement2Id'));

        if ($classement1 && $classement2) {
            $em = $doctrine->getManager();
            // Swap the teams in the two classements
            $tempId = $classement1->getIdTeam()->getId();
            $classement1->setIdTeam($classement2->getIdTeam());
            $classement2->setIdTeam($em->getReference(Team::class, $tempId));

            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Teams swapped successfully',
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'message' => 'Failed to swap teams',
        ]);
    }




    #[Route('/mygroup/{id}', name: 'mygroup')]
    public function mygroup(int $id, Request $request , ManagerRegistry $doctrine): Response
    {

        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy(['id' => $request->getSession()->get('Gamer_id')]);
        $team = $doctrine->getRepository(Team::class)->find($id);
        $count = $doctrine->getRepository(Membre::class)->count(['idTeam' => $id]);

        // Check if gamer is already a member of the team
        $existingMember = $doctrine->getRepository(Membre::class)->findOneBy(['idGamer' => $gamer, 'idTeam' => $team]);

        if ($team && !$existingMember && $count < $team->getNbJoueurs()) {
            $groupemember = new Membre();
            $groupemember->setIdGamer($gamer);
            $groupemember->setIdTeam($team);
            $groupemember->setPoint(0); // Set the
            $em = $doctrine->getManager();
            $em->persist($groupemember);
            $em->flush();
        }

        $groupemember= $doctrine->getRepository(Membre::class)->findBy(['idTeam' => $id]);
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Wins',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'data' => [],
                ],
                [
                    'label' => 'Losses',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => [],
                ],
            ],
        ];
        $chartData['labels'][] = $team->getNomTeam(); // Add team name to labels array
        $chartData['datasets'][0]['data'][] = $team->getWin(); // Add wins to first dataset
        $chartData['datasets'][1]['data'][] = $team->getLose(); // Add losses to second dataset

        return $this->render('tournoi/mygroup.html.twig', [
            't' => $team,
            'c' => $count,
            'm' => $groupemember,
            'id'=>$id,
            'chartData' => json_encode($chartData),


        ]);
    }
    #[Route('/deletemember/{id}', name: 'kickmember')]
    public function deletemember(int $id, Request $request , ManagerRegistry $doctrine): Response
    {
        $m = $doctrine
            ->getRepository(Membre::class)
            ->find($id);

        $em = $doctrine->getManager();
        $em->remove($m);
        $em->flush() ;
        return $this->redirectToRoute('teams');

    }

    #[Route('/addteam', name: 'addteam')]
    public function addgroup(SluggerInterface $slugger,ManagerRegistry $doctrine, Request  $request): Response
    {
        $id=$this->session->get('Gamer_id');
        $gamer= $doctrine->getRepository(Gamer::class)->find($id);
        $team = new Team() ;
        $team->setOwnerteam($gamer);
        $groupemember = new Membre();
        $groupemember->setIdGamer($gamer);
        $groupemember->setIdTeam($team);
        $groupemember->setPoint(0);
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $photoC = $form->get('logos')->getData();
            if ($photoC ) {
                $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);

                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $photoC->move(
                        $this->getParameter('imgteam_directory'),
                        $newImgename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $team->setlogo($newImgename);
            }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

            $em = $doctrine->getManager();
            $em->persist($team);
            $em->persist($groupemember);
            $em->flush();
            return $this->redirectToRoute('teams');
        }
        return $this->renderForm("tournoi/addteam.html.twig",
            ["form"=>$form ]) ;
    }
    #[Route('/addtournoi', name: 'addtourno')]
    public function addtournoi(SluggerInterface $slugger,ManagerRegistry $doctrine, Request  $request): Response
    {

        $id=$this->session->get('Gamer_id');
        $gamer= $doctrine->getRepository(Gamer::class)->find($id);
        $tournoi = new Tournoi() ;
        $tournoi->setNbParticipant(0);
        $tournoi->setOwnertournoi($gamer);
        $form = $this->createForm(TournoiType::class, $tournoi);

        $form->handleRequest($request);

    if($form->isSubmitted()&& $form->isValid()){
        $tournoi->setEtat(0);
        $photoC = $form->get('logos')->getData();
        if ($photoC ) {
            $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);

            // this is needed to safely include the file name as part of the URL
            $safeImgname = $slugger->slug($originalImgName);

            $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();


            // Move the file to the directory where brochures are stored
            try {
                $photoC->move(
                    $this->getParameter('imgteam_directory'),
                    $newImgename
                );

            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $tournoi->setImage($newImgename);
        }
            $em = $doctrine->getManager();
            $em->persist($tournoi);
            $em->flush();
            return $this->redirectToRoute('tournoi');
        }
        return $this->renderForm("tournoi/addtournoi.html.twig",
            ["form"=>$form ]) ;
    }
    #[Route('/addtournoiApi/new', name: 'addapitournoi', methods: ['GET'])]
    public function addtournoiapi(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, SerializerInterface $serializer): Response {
        $tournoi = new Tournoi() ;
        $tournoi->setEtat(0);
        $tournoi->setNbTeam($request->query->get('nb_team'));
        $tournoi->setNbJoueurTeam($request->query->get('nb_joueur_team'));
        $tournoi->setNomtournoi($request->query->get('nomtournoi'));
        $tournoi->setDevice($request->query->get('device'));
        $tournoi->setImage($request->query->get('logos'));
        $em = $doctrine->getManager();
        $em->persist($tournoi);
        $em->flush();

        $json = $serializer->serialize($tournoi, 'json');
        return new JsonResponse(['status' => 'success', 'data' => $json]);
    }
    #[Route('/updateteam/{id}', name: 'updateteam')]
    public function  updateteam(SluggerInterface $slugger,ManagerRegistry $doctrine,$id,  Request  $request) : Response
    {
        $team = $doctrine
        ->getRepository(Team::class)
        ->find($id);

        $form = $this->createForm(Team2Type::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $photoC = $form->get('logos')->getData();
            if ($photoC ) {
                $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);

                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $photoC->move(
                        $this->getParameter('imgteam_directory'),
                        $newImgename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $team->setlogo($newImgename);
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents

            $em = $doctrine->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('teams' );
        }
        return $this->renderForm("tournoi/updateteam.html.twig",
            ["form"=>$form,

            ]) ;


    }
    #[Route("/deleteteam/{id}", name:'deleteteam')]
    public function delete($id, ManagerRegistry $doctrine)
    {

        $t = $doctrine
        ->getRepository(Team::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($t);
        $em->flush() ;
        return $this->redirectToRoute('teams');
    }
    #[Route('/updatetournoi/{id}', name: 'updatetournoi')]
    public function  updatetournoi(SluggerInterface $slugger,ManagerRegistry $doctrine,$id,  Request  $request) : Response
    {

        $tournoi = $doctrine
        ->getRepository(Tournoi::class)
        ->find($id);
        $form = $this->createForm(Tournoi2Type::class, $tournoi);

        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid())
        {
            $photoC = $form->get('logos')->getData();
            if ($photoC ) {
                $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);

                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $photoC->move(
                        $this->getParameter('imgteam_directory'),
                        $newImgename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $tournoi->setImage($newImgename);
            }
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('tournoi');
        }
        return $this->renderForm("tournoi/updatetournoi.html.twig",
            ["form"=>$form]) ;


    }#[Route('/apiupdatetournoi/{id}', name: 'apiupdatetournoi', methods: ['GET'])]
    public function  apiupdatetournoi(SluggerInterface $slugger,ManagerRegistry $doctrine,$id,  Request  $request, SerializerInterface $serializer) : Response
    {
        $tournoi = $doctrine
            ->getRepository(Tournoi::class)
            ->find($id);
        $tournoi->setNbTeam($request->query->get('nb_team'));
        $tournoi->setNbJoueurTeam($request->query->get('nb_joueur_team'));
        $tournoi->setNomtournoi($request->query->get('nomtournoi'));
        $tournoi->setDevice($request->query->get('device'));

        $em = $doctrine->getManager();
        $em->flush();

        $json = $serializer->serialize($tournoi, 'json');
        return new JsonResponse(['status' => 'success', 'data' => $json]);


    }
    #[Route("/deletetournoi/{id}", name:'deletetournoi')]
    public function deletetournoi($id, ManagerRegistry $doctrine)
    {

        $t = $doctrine
        ->getRepository(Tournoi::class)
        ->find($id);
        $em = $doctrine->getManager();
        $em->remove($t);
        $em->flush() ;
        return $this->redirectToRoute('tournoi');
    }
    #[Route("/apideletetournoi/{id}", name:'apideletetournoi')]
    public function apideletetournoi($id, ManagerRegistry $doctrine,SerializerInterface $normalizer)
    {

        $t = $doctrine->getRepository(Tournoi::class)->find($id);

        $em = $doctrine->getManager();
        $em->remove($t);
        $em->flush() ;
        $json = $normalizer->serialize($t,'json',['groups'=>"Tournoi"]);
        return new Response($json);
    }
    #[Route('/tournoi/true/{id}', name: 'updattournoi')]
    public function accepttournoi(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request, int $id): Response
    {
        $tournoi = $doctrine->getRepository(Tournoi::class)->find($id);
        if($tournoi)
        {
            $tournoi->setEtat(1);
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('backtournoi',['tournoi'=>true]);
        }else
            return $this->redirectToRoute('backtournoi',['tournoi'=>false]);
    }

    #[Route('/tournoi/false/{id}', name: 'tournoiFalse')]
    public function refuserttournoi(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request, int $id): Response
    {
        $notif=new Notif();
        $t = $doctrine->getRepository(Tournoi::class)->find($id);

        if($t)
        {
             $t->setEtat(-1);
            $notif->setOwner($t->getOwnertournoi());
            $notif->setContenet("voter tournoi est refuser");
            $em =$doctrine->getManager();
            $em->persist($notif);

            $em->flush();
            return $this->redirectToRoute('backtournoi',['tournoiFound'=>true]);
        }else
            return $this->redirectToRoute('backtournoi',['tournoiFound'=>false]);
    }
    #[Route('/showgroup/{id}', name: 'showgroup')]
    public function showgroup(int $id, Request $request , ManagerRegistry $doctrine): Response
    {
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy(['id' => $request->getSession()->get('Gamer_id')]);
        $team = $doctrine->getRepository(Team::class)->find($id);
        $count = $doctrine->getRepository(Membre::class)->count(['idTeam' => $id]);

        $groupemember= $doctrine->getRepository(Membre::class)->findBy(['idTeam' => $id]);
        // Prepare chart data
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Wins',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'data' => [],
                ],
                [
                    'label' => 'Losses',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => [],
                ],
            ],
        ];
        $chartData['labels'][] = $team->getNomTeam(); // Add team name to labels array
        $chartData['datasets'][0]['data'][] = $team->getWin(); // Add wins to first dataset
        $chartData['datasets'][1]['data'][] = $team->getLose(); // Add losses to second dataset

        return $this->render('tournoi/mygroup.html.twig', [
            't' => $team,
            'm' => $groupemember,
            'id'=>$id,
            'c'=>$count,
            'chartData' => json_encode($chartData),
        ]);

    }
    #[Route('/exportexcel', name: 'export_excel')]
    public function exportToExcel(): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the color of the first row to red
        // Set the color of the first row to black
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getStyle('1:1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('1:1')->getFill()->getStartColor()->setARGB('000000');

        // Add logo
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('front_office/assets/img/logo1.png'); // replace with actual path to logo image file
        $drawing->setCoordinates('A1');
        $drawing->setHeight(50);
        $drawing->setWorksheet($sheet);

        // Add table headers
        $sheet->setCellValue('A2', 'Team Name');
        $sheet->setCellValue('B2', 'Score');
        $sheet->getStyle('A2:B2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A2:B2')->getFill()->getStartColor()->setARGB('D8BFD8');
        // Add table data
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $classements = $this->getDoctrine()->getRepository(Classement::class)->findAll();
        $row = 3;
        foreach ($classements as $classement) {
            $sheet->setCellValue('A' . $row, $classement->getIdTeam()->getNomteam());
            $sheet->setCellValue('B' . $row, $classement->getScore());

            // Set the color of the row to green
            $sheet->getRowDimension($row)->setRowHeight(25);
            $sheet->getStyle($row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($row)->getFill()->getStartColor()->setARGB('D8BFD8');

            // Center align content in cells
            $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Center align content in header cells
        $sheet->getStyle('A2:B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getStyle('2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('2')->getFill()->getStartColor()->setARGB('D8BFD8');
        // Set overall table style
        $sheet->getStyle('A2:B' . ($row-1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Create Excel file as string
        $writer = new Xlsx($spreadsheet);
        $filename = 'export.xlsx';

        // Start output buffering
        ob_start();

        // Write the Excel file to output buffer
        $writer->save('php://output');

        // Get the output buffer contents and end buffering
        $content = ob_get_contents();
        ob_end_clean();

        // Set response headers and return
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;

    }


    #[Route('/static', name: 'static')]
    public function myAction()
    {

        $chartData = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Dataset 1',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => [65, 59, 80, 81, 56, 55, 40],
                ],
                [
                    'label' => 'Dataset 2',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'data' => [28, 48, 40, 19, 86, 27, 90],
                ],
            ],
        ];

        return $this->render('tournoi/charti.html.twig', [
            'chartData' => json_encode($chartData),

        ]);

    }
}
