<?php

namespace App\Controller;
use App\Entity\CoachSkills;
use App\Entity\Jeux;
use App\Entity\Planning;
use App\Form\CoachPlanningType;
use App\Form\UpdateCourseType;
use App\Form\UserOnlineCoachingType;
use App\Repository\CoachRepository;
use App\Repository\CoachSkillsRepository;
use App\Repository\JeuxRepository;
use App\Repository\PlanningRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Coach;
use App\Entity\Cours;
use App\Entity\Gamer;
use App\Entity\UserCourses;
use App\Form\AddCourseType;
use App\Repository\CoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;


class CoachingController extends BaseController
{                       /****************ADMIN****************/

    /****************AFFICHER LES COURS POUR LADMIN****************/
    #[Route('/Admin/allCourses', name: 'Admincoaching')]
    public function adminallCourses(ManagerRegistry $doctrine): Response
    {
        $courses= $doctrine->getRepository(Cours::class)->findAll();
        return $this->render('coaching/adminSeeCourses.html.twig',['courses'=>$courses]);
    }

    /****************ACCEPTER UN COUR****************/
    #[Route('/Course/true/{id}', name: 'updateStateTrue')]
    public function acceptCourse(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id): Response
    {
        $course = $doctrine->getRepository(Cours::class)->find($id);
        if($course)
        {
            $course->setEtat(1);
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('Admincoaching',['courseFound'=>true]);
        }else
            return $this->redirectToRoute('Admincoaching',['courseFound'=>false]);
    }

    /****************REFUSER UN COUR****************/
    #[Route('/Course/false/{id}', name: 'updateStateFalse')]
    public function refusertCourse(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id): Response
    {
        $course = $doctrine->getRepository(Cours::class)->find($id);
        if($course)
        {
            $course->setEtat(-1);
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('Admincoaching',['courseFound'=>true]);
        }else
            return $this->redirectToRoute('Admincoaching',['courseFound'=>false]);
    }


    /****************GAMER+COACH****************/

    /****************AJOUTER COURS****************/
    #[Route('/coaching/addC', name: 'addC')]
    public function addC(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger): Response
    {
        $course =new Cours();
        $course->setEtat(0);
        //get coach with id logged in now
        $coach= $this->managerRegistry->getRepository(Coach::class)->findOneBy((['id' => $request->getSession()->get('Coach_id')]));
        $course->setIdCoach($coach);
        $coachId = $course->getIdCoach()->getId();
        $form =$this->createForm(AddCourseType::class,$course);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {

            $photoC = $form->get('picture')->getData();
            $videoC =  $form->get('videoC')->getData();

            if ($photoC && $videoC) {
                //sans extension du nom
                $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);
                $originalVidName = pathinfo($videoC->getClientOriginalName(), PATHINFO_FILENAME);

                $safeImgname = $slugger->slug($originalImgName);
                $safeVidname = $slugger->slug($originalVidName);
                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();
                $newVidename = $safeVidname.'-'.uniqid().'.'.$videoC->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoC->move(
                        $this->getParameter('img_directory'),
                        $newImgename
                    );
                    $videoC->move(
                        $this->getParameter('vid_directory'),
                        $newVidename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $course->setImage($newImgename);
                $course->setVideo($newVidename);
            }


            $em =$doctrine->getManager();
            $em->persist($course);
            $em->flush();
            return $this->redirectToRoute('CoachCourses', ['id' => $coachId, 'etat' => 0]);

        }
        return $this->renderForm('coaching/addCourse.html.twig',
            [
                "form"=>$form
            ]);
    }

    #[Route('/coaching/addCAPI/new', name: 'addCAPI', methods: ['GET'])]
    public function addCAPI(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, SerializerInterface $serializer): Response {
        $course = new Cours();
        $course->setEtat(0);

        $coach= $this->managerRegistry->getRepository(Coach::class)->findOneBy((['id' => $request->getSession()->get('Coach_id')]));
        $course->setIdCoach($coach);

        $course->setTitre($request->query->get('titre'));
        $course->setDescription($request->query->get('description'));
        $course->setVideo($request->query->get('video'));
        $course->setImage($request->query->get('image'));
        $course->setPrix($request->query->get('prix'));
        $course->setNiveau($request->query->get('niveau'));


        $em = $doctrine->getManager();
        $em->persist($course);
        $em->flush();

        $json = $serializer->serialize($course, 'json');
        return new JsonResponse(['status' => 'success', 'data' => $json]);
    }

    /****************AFFICHER COURS****************/
    #[Route('/coaching/allCourses/{idGame}', name: 'app_coaching')]
    public function allCourses(ManagerRegistry $doctrine, Request $request,CoursRepository $coursRepository,int $idGame): Response
    {
        $courses = $this->getDoctrine()->getRepository(Cours::class)->findAll();
        $jeux = $this->getDoctrine()->getRepository(Jeux::class)->findAll();
        if($idGame != -1) $courses = $coursRepository->findByGame($idGame);


        return $this->render('coaching/allCoaching.html.twig', [
            'courses' => $courses,
            'jeux' => $jeux,
        ]);
    }

    /****************AFFICHER API****************/
    #[Route('/coaching/allCoursesAPI', name: 'coachingAPI')]
    public function allCoursesAPI(ManagerRegistry $doctrine,SerializerInterface $normalizer): Response
    {
        $courses= $doctrine->getRepository(Cours::class)->findAll();
        $json = $normalizer->serialize($courses,'json',['groups'=>"Cours"]);
        return new Response($json);
    }

    /****************SUPPRIMER COURS****************/
    #[Route('/Course/Delete/{id}', name: 'suppC')]
    public function supp(ManagerRegistry $doctrine , int $id,Filesystem $filesystem): Response
    {
        $course= $doctrine->getRepository(Cours::class)->find($id);
        $coachId= $course->getIdCoach()->getId();


        // get the filename of the image associated with the course
        $imageFileName = $course->getImage();
        $vidFileName = $course->getVideo();

        $em = $doctrine->getManager();
        $em->remove($course);
        $em->flush();

        if ($imageFileName && $filesystem->exists($this->getParameter('img_directory').'/'.$imageFileName)) {
            $filesystem->remove($this->getParameter('img_directory').'/'.$imageFileName);
        }

        if ($vidFileName && $filesystem->exists($this->getParameter('vid_directory').'/'.$vidFileName)) {
            $filesystem->remove($this->getParameter('vid_directory').'/'.$vidFileName);
        }

        return $this->redirectToRoute('CoachCourses', [
            'id' => $coachId,
            'etat' => 1,
            'enjoy'=>"course deleted succesfuly !"
        ]);
    }
    /****************SUPPRIMER COURS****************/
    #[Route('/Course/DeleteAPI/{id}', name: 'suppCAPI')]
    public function suppAPI(ManagerRegistry $doctrine , int $id,Filesystem $filesystem,SerializerInterface $normalizer): Response
    {
        $course= $doctrine->getRepository(Cours::class)->find($id);

        $em = $doctrine->getManager();
        $em->remove($course);
        $em->flush();
        $json = $normalizer->serialize($course,'json',['groups'=>"Cours"]);

        return new Response($json);

    }

    /****************MODIFIER COURS****************/
    #[Route('/Course/update/{id}', name: 'updateC')]
    public function updateC(\Doctrine\Persistence\ManagerRegistry $doctrine,  Filesystem $filesystem,Request $request, int $id, SluggerInterface $slugger): Response
    {
        $course = $doctrine->getRepository(Cours::class)->find($id);
        $coachId = $course->getIdCoach()->getId();
        $form = $this->createForm(UpdateCourseType::class, $course);
        $form->handleRequest($request);

        $imageFileName = $course->getImage();
        $vidFileName = $course->getVideo();

        if ($form->isSubmitted() && $form->isValid()) {

            $photoC = $form->get('picture')->getData();
            $videoC =  $form->get('videoC')->getData();

            if ($photoC ) {
                if ($imageFileName && $filesystem->exists($this->getParameter('img_directory').'/'.$imageFileName)) {
                    $filesystem->remove($this->getParameter('img_directory').'/'.$imageFileName);
                }
                $originalImgName = pathinfo($photoC->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);
                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoC->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoC->move(
                        $this->getParameter('img_directory'),
                        $newImgename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $course->setImage($newImgename);
            }
            if($videoC)
            {
                if ($vidFileName && $filesystem->exists($this->getParameter('vid_directory').'/'.$vidFileName)) {
                    $filesystem->remove($this->getParameter('vid_directory').'/'.$vidFileName);
                }
                $originalVidName = pathinfo($videoC->getClientOriginalName(), PATHINFO_FILENAME);
                $safeVidname = $slugger->slug($originalVidName);
                $newVidename = $safeVidname.'-'.uniqid().'.'.$videoC->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $videoC->move(
                        $this->getParameter('vid_directory'),
                        $newVidename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $course->setVideo($newVidename);

            }

            $course->setEtat(0);
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('CoachCourses', [
                'id' => $coachId,
                'etat' => 0,
                'enjoy' => "Course updated successfully! You must wait until the admin review and accept it!"
            ]);
        }

        return $this->renderForm('Coaching/updateCourse.html.twig', [
            'form' => $form
        ]);
    }

    /****************MODIFIER COURS API****************/
    #[Route('/Course/updateAPI/{id}', name: 'updateCAPI')]
    public function updateCAPI(\Doctrine\Persistence\ManagerRegistry $doctrine,  Filesystem $filesystem,Request $request, int $id, SluggerInterface $slugger): JsonResponse
    {
        $course = $doctrine->getRepository(Cours::class)->find($id);

        $titre = $request->query->get('titre');
        $description = $request->query->get('description');
        $video = $request->query->get('video');
        $image = $request->query->get('image');
        $prix = $request->query->get('prix');
        $niveau = $request->query->get('niveau');

        if(!$course || !$titre || !$description || !$video || !$image || !$prix || !$niveau) {
            return new JsonResponse(['error' => 'Invalid parameters'], Response::HTTP_BAD_REQUEST);
        }

        $course->setTitre($titre);
        $course->setDescription($description);
        $course->setVideo($video);
        $course->setImage($image);
        $course->setPrix($prix);
        $course->setNiveau($niveau);
        $course->setEtat(0);

        $em = $doctrine->getManager();
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    /****************AFFICHER LES COURS D'UN COACH****************/
    #[Route('/coach/{id}/courses/{etat}', name: 'CoachCourses')]
    public function showCoachCourses($id,int $etat, CoursRepository $courseRepository,CoachRepository $coachRep)
    {
        $EtatCourses = $courseRepository->findCoursesByCoachIdEtat($id,$etat);
        $UserViewCourses = $courseRepository->profileCoachEtat1($id);
        $Coach = $coachRep->getCoach($id);

        return $this->render('coaching/oneCoachCourses.html.twig', [
            'UserViewCourses' => $UserViewCourses,
            'EtatCourses' => $EtatCourses,
            'Coach' => $Coach[0]
        ]);
    }

    /****************AFFICHER LES DETAILLES D'UN COUR****************/
    #[Route('/coaching/oneCourse/{id}', name: 'oneCourse')]
    public function oneCourse(int $id, ManagerRegistry $doctrine ,Request $request)
    {

        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $coach= $this->managerRegistry->getRepository(Coach::class)->findOneBy((['id' => $request->getSession()->get('Coach_id')]));

        $em =$doctrine->getManager();
        $course = $doctrine->getRepository(Cours::class)->find($id);

        $isOwner = ($course->getIdCoach() == $coach);


        $isFavorite = $em->getRepository(UserCourses::class)->findOneBy(['idGamer' => $gamer, 'idCours' => $course, 'favori' => true]);
        $isBuyed = $em->getRepository(UserCourses::class)->findOneBy(['idGamer' => $gamer, 'idCours' => $course, 'acheter' => true]);
        if(!$isBuyed) $isBuyed = false;
        return $this->render('coaching/CourseDetails.html.twig', [
            'course' => $course,
            'isFavorite'=>$isFavorite,
            'isBuyed'=>$isBuyed,
            'isOwner' => $isOwner

        ]);
    }

    /****************AJOUTER AU FAVORI UN COURS****************/
    #[Route('/course/{id}/toFavori', name: 'favori_course')]
    public function addToFavoriCourse(ManagerRegistry $doctrine,Request $request, int $id): Response
    {
        $gamer= $doctrine->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $course = $doctrine->getRepository(Cours::class)->find($id);

        $gamersCourse = $doctrine->getRepository(UserCourses::class)->findOneBy([
            'idGamer' => $gamer->getId(),
            'idCours' => $course->getId(),
        ]);
        $em =$doctrine->getManager();
        if ($gamersCourse) {
            if ($gamersCourse->isAcheter()) {
                $gamersCourse->setFavori(true);
                $em->flush();
                // Le gamer a déjà ajouté le cours à sa liste de favoris
                return $this->redirectToRoute('GamerCourses',[
                    'enjoy'=>"course added successfully to wishlist :D !",
                    'cours'=>$course
                ]);
            }

            if ($gamersCourse->isFavori()) {
                // Le gamer a déjà acheté le cours
                return $this->redirectToRoute('GamerCourses',[
                    'error' =>"le cours est deja au favori"
                ]);
            }
        }else{
            $gamersCourse = new UserCourses();
            $gamersCourse->setIdGamer($gamer);
            $gamersCourse->setIdCours($course);
            $gamersCourse->setFavori(true);
            $gamersCourse->setAcheter(false);
            $em->persist($gamersCourse);
            $em->flush();
        }

        return $this->redirectToRoute('GamerCourses',[
            'enjoy'=>"course added to wishlist succesfuly :D !"
        ]);
    }

    /****************SUPPRIMER DE LA WISHLIST UN COURS****************/
    #[Route('/course/{id}/removeFromFavori', name: 'removeFromFavoriCourse')]
    public function removeFromFavoriCourse(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $em =$doctrine->getManager();
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $course = $doctrine->getRepository(Cours::class)->find($id);
        $userCourse =$em->getRepository(UserCourses::class)->findOneBy(['idGamer' => $gamer, 'idCours' => $course, 'favori' => true]);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }else
        {
            if($userCourse->isAcheter()){
                $userCourse->setFavori(false);
                $em->flush();
                return $this->redirectToRoute('GamerCourses',[
                    'enjoy'=>"course removed from wishlist :( !",

                ]);
            }else
            {
                $em->remove($userCourse);
                $em->flush();
                return $this->redirectToRoute('GamerCourses',[
                    'enjoy'=>"course removed from wishlist :( !",
                ]);
            }
        }
    }

    /****************AFFICHER LA LISTE DE WISHLIST(COURS)****************/
    #[Route('/gamer/courses', name: 'GamerCourses')]
    public function showWishlist(Request $request)
    {
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $GamerWishlist = $gamer->getUserCourses();

        return $this->render('coaching/gamerCourses.html.twig', [
            'wishlist' => $GamerWishlist,
        ]);
    }

    /****************ACHETER UN COURS****************/
    #[Route('/course/{id}/userBuyC', name: 'buy_course')]
    public function buyCourse(ManagerRegistry $doctrine,Request $request, int $id): Response
    {
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $course = $doctrine->getRepository(Cours::class)->find($id);

        $gamersCourse = $doctrine->getRepository(UserCourses::class)->findOneBy([
            'idGamer' => $gamer->getId(),
            'idCours' => $course->getId(),
        ]);

        $prix = $course->getPrix();
        $gamerP= $gamer->getPoint();
        $em =$doctrine->getManager();
        if ($gamersCourse){
            if ($gamersCourse->isFavori()) {

                if($prix>$gamerP) return $this->redirectToRoute('GamerCourses',[
                    'error' =>"votre solde des points est insufisant"
                ]);
                else
                {
                    $gamer->setPoint($gamerP-$prix);
                    $this->session->set('Gamer_point', $gamer->getPoint());
                    $gamersCourse->setAcheter(true);
                    //updateCoach points
                    $newPoints = $course->getIdCoach()->getPoint()+$course->getPrix();
                    $course->getIdCoach()->setPoint($newPoints);
                    $em->flush();
                }

                // Le gamer a déjà ajouté le cours à sa liste de favoris
                return $this->redirectToRoute('GamerCourses',[
                    'enjoy'=>"enjoy ur new course :D !",
                    'cours'=>$course
                ]);
            }

            if ($gamersCourse->isAcheter()) {
                // Le gamer a déjà acheté le cours
                return $this->redirectToRoute('GamerCourses',[
                    'error' =>"le cours est deja acheter"
                ]);
            }
        }else{
            if($prix>$gamerP){
                return $this->redirectToRoute('GamerCourses',[
                    'error' =>"votre solde des points est insufisant"
                ]);
            }else{
                $gamersCourse = new UserCourses();
                $gamersCourse->setIdGamer($gamer);
                $gamersCourse->setIdCours($course);
                $gamer->setPoint($gamerP-$prix);

                //updateCoach points
                $newPoints = $course->getIdCoach()->getPoint()+$course->getPrix();
                $course->getIdCoach()->setPoint($newPoints);

                $this->session->set('Gamer_point', $gamer->getPoint());
                $gamersCourse->setFavori(false);
                $gamersCourse->setAcheter(true);

                $em->persist($gamersCourse);
                $em->flush();


            }
        }

        return $this->redirectToRoute('GamerCourses',[
            'enjoy'=>"enjoy ur new course :D !",
            'cours' => $course->getId()
        ]);
    }

    /****************GENERER FACTURE PDF****************/
    #[Route('/generate-invoice/{id}', name: 'generate_invoice')]
    public function generateInvoice(Request $request,int $id,ManagerRegistry $doctrine)
    {
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $course = $doctrine->getRepository(Cours::class)->find($id);

        $html = $this->renderView('./coaching/pdfFacture.html.twig', [
            'course' => $course,
            'gamer' => $gamer,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', $_SERVER['DOCUMENT_ROOT']);


        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s_%s.pdf"', $gamer->getNom() , $course->getTitre() ));

        return $response;
    }

    /****************PLANIFIER ONLINE COACHING****************/
    #[Route('/CoachingOnline/{idCaoch}', name: 'online_coaching')]
    public function planifierOnlineCoaching(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request,int $idCaoch,CoachSkillsRepository $csRep): Response
    {

        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $coach = $this->getDoctrine()->getRepository(Coach::class)->find($idCaoch);
        $planning=new Planning();
        $planning->setEtat(0);
        $planning->setIdCoach($coach);
        $planning->setIdGamer($gamer);

        $form = $this->createForm(UserOnlineCoachingType::class, $planning, [
            'coach_skills' => $csRep->getSkillsOfCOach($idCaoch)
        ]);
        $form->handleRequest($request);

        $em =$doctrine->getManager();

        if($form->isSubmitted()&&$form->isValid())
        {

            $prixT = $planning->getNbreHeureSeance() * $planning->getIdCoach()->getPrixHeure();
            $planning->setPrixT($prixT);

            $em->persist($planning);
            $em->flush();
            return $this->redirectToRoute('GamerCourses');
        }
        return $this->renderForm('coaching/UPSCoaching.html.twig', ["form"=>$form,"Coach"=>$coach]);
    }

    #[Route('/planning/{idCoach}', name: 'planning_list')]
    public function listPlanningForCoach(Request $request,PlanningRepository $plRep,int $idCoach): Response
    {
        $coach= $this->managerRegistry->getRepository(Coach::class)->findOneBy((['id' => $request->getSession()->get('Coach_id')]));
        $plannings = $plRep->getPlaningOfCoach($idCoach);
        $gamerPlannings = $plRep->getPlaningOfGamer($idCoach);
        return $this->render('coaching/listPlanning.html.twig', [
            'plannings' => $plannings,
            'gamerPlannings'=>$gamerPlannings
        ]);
    }

    /**
     * @Route("/summoner/{name}", name="summoner", requirements={"name"=".*"})
     */
    public function getSummoner(ClientInterface $httpClient, RequestFactoryInterface $requestFactory,Request $request,CoursRepository $coursRepository): Response
    {
        $name = $request->query->get('name');
        $DAK = "RGAPI-c055ebdf-dab0-4f25-8c13-14eb59d5147b";
        // Get summoner information
        $summonerUri = 'https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . urlencode($name) . '?api_key='.$DAK;
        $summonerRequest = $requestFactory->createRequest('GET', $summonerUri);
        $summonerResponse = $httpClient->sendRequest($summonerRequest);
        $summonerStatusCode = $summonerResponse->getStatusCode();
        if ($summonerStatusCode != 200) {
            // Handle non-200 responses here
            return new Response('Account didnt exist in lieague data base');
        }

        $summoner = json_decode($summonerResponse->getBody(), true);
        $encryptedSummonerId = $summoner['id'];
        $iconId = $summoner['profileIconId'];

        // Get league information
        $leagueUri = 'https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/' . $encryptedSummonerId . '?api_key='.$DAK;
        $leagueRequest = $requestFactory->createRequest('GET', $leagueUri);
        $leagueResponse = $httpClient->sendRequest($leagueRequest);

        $leagueStatusCode = $leagueResponse->getStatusCode();
        if ($leagueStatusCode != 200 or $leagueResponse ==null ) {
            // Handle non-200 responses here
            return new Response('ID NOT FOUND  ' . $leagueStatusCode, $leagueStatusCode);
        }

        $leagues = json_decode($leagueResponse->getBody(), true);

        if($leagues == null){
            return new Response('account exist but didnt play the 10 ranked game  ' . $leagueStatusCode, $leagueStatusCode);
        }


        $rank = $leagues[0]['tier'];


        // Filter courses based on user's rank

        if ($rank !== null) {
            $courses = $coursRepository->findByRankAndGame($rank, 'LEAGUE OF LEGENDS');
        }else $courses = $coursRepository->findByGame(3);

        return $this->render('coaching/summoner.html.twig', [
            'icon'=>$iconId,
            'summoner' => $summoner,
            'leagues' => $leagues,
            'courses' => $courses
        ]);
    }





    /****************ACCEPTER UN COUR****************/
    #[Route('/Planning/true/{id}', name: 'axeptPlanning')]
    public function acceptSeanceCoaching(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id,Request $request): Response
    {
        $planning = $doctrine->getRepository(Planning::class)->find($id);

        $planning->setEtat(1);
        //get coach with id logged in now
        $coachId = $planning->getIdCoach()->getId();
        $form =$this->createForm(CoachPlanningType::class,$planning);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()) {

            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('planning_list', ['idCoach' => $coachId]);
        }

        return $this->renderForm('coaching/coachAxeptCourse.html.twig',
            [
                "form"=>$form
            ]);

    }

    /****************ACCEPTER UN COUR****************/
    #[Route('/Planning/false/{id}', name: 'declinePlanning')]
    public function adeclinetSeanceCoaching(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id,Request $request): Response
    {
        $planning = $doctrine->getRepository(Planning::class)->find($id);
        $planning->setEtat(-1);
        $coachId = $planning->getIdCoach()->getId();
        $em =$doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('planning_list', ['idCoach' => $coachId]);

    }


    /**
     * @Route("/summonerApi/{name}", name="summonerApi", requirements={"name"=".*"})
     */
    public function getSummonerApi(ClientInterface $httpClient, RequestFactoryInterface $requestFactory,Request $request,SerializerInterface $normalizer): Response
    {
        $nameS = $request->query->get('name');
        $DAK = "RGAPI-8a2eecdc-ca78-42ee-a067-29f2fea4fc00";
        // Get summoner information
        $summonerUri = 'https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . urlencode($nameS) . '?api_key='.$DAK;
        $summonerRequest = $requestFactory->createRequest('GET', $summonerUri);
        $summonerResponse = $httpClient->sendRequest($summonerRequest);
        $summonerStatusCode = $summonerResponse->getStatusCode();
        if ($summonerStatusCode != 200) {
            // Handle non-200 responses here
            return new Response('Account didnt exist in lieague data base');
        }

        $summoner = json_decode($summonerResponse->getBody(), true);
        $encryptedSummonerId = $summoner['id'];
        $iconId = $summoner['profileIconId'];

        // Get league information
        $leagueUri = 'https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/' . $encryptedSummonerId . '?api_key='.$DAK;
        $leagueRequest = $requestFactory->createRequest('GET', $leagueUri);
        $leagueResponse = $httpClient->sendRequest($leagueRequest);

        $leagueStatusCode = $leagueResponse->getStatusCode();
        if ($leagueStatusCode != 200 or $leagueResponse ==null ) {
            // Handle non-200 responses here
            return new Response('ID NOT FOUND  ' . $leagueStatusCode, $leagueStatusCode);
        }

        $leagues = json_decode($leagueResponse->getBody(), true);

        if($leagues == null){
            return new Response('account exist but didnt play the 10 ranked game  ' . $leagueStatusCode, $leagueStatusCode);
        }


        $rank = $leagues[0]['tier'];

        $json = $normalizer->serialize($leagues,'json');
        return new Response($json);
    }

}
