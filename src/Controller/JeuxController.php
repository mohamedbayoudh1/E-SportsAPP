<?php

namespace App\Controller;


use App\Repository\JeuxRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Jeux;
use App\Form\JeuxType;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Form\JeuxType2;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class JeuxController extends BaseController
{
    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/jeux', name: 'afficher_les_jeux')]
    public function afficherjeux(PaginatorInterface $paginator,Request $request): Response
    {


        $repo = $this->managerRegistry->getRepository(Jeux::class);
        $queryBuilder = $repo->createQueryBuilder('n')
            ->leftJoin('n.reviewJeuxes', 'r')
            ->orderBy('n.DateAddGame', 'DESC');
        $jeux = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 4);

        $ratingJeux = [];
        foreach ($jeux as $jeu) {
            $reviews = $jeu->getReviewJeuxes();
            if ($reviews->isEmpty()) {
                $ratingJeux[$jeu->getId()] = null;
                continue;
            }

            $totalRating = 0;
            $reviewCount = 0;
            foreach ($reviews as $review) {
                $totalRating += $review->getRating();
                $reviewCount++;
            }

            $ratingJeux[$jeu->getId()] = $totalRating / $reviewCount;

        }
        return $this->render('jeux/index.html.twig', array(
            'jeux' => $jeux,
            'ratingJeux' => $ratingJeux,

        ));
    }


    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/jeux_back', name: 'afficher_les_jeux_back')]
    public function afficherjeux_back(PaginatorInterface $paginator,Request $request,SluggerInterface $slugger): Response
    {
        $repo = $this->managerRegistry->getRepository(Jeux::class);
        $queryBuilder = $repo->createQueryBuilder('n')
            ->leftJoin('n.reviewJeuxes', 'r')
            ->orderBy('n.DateAddGame', 'DESC');
        $jeux = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 4);

        $ratingJeux = [];
        foreach ($jeux as $jeu) {
            $reviews = $jeu->getReviewJeuxes();
            if ($reviews->isEmpty()) {
                $ratingJeux[$jeu->getId()] = null;
                continue;
            }
            $totalRating = 0;
            $reviewCount = 0;
            foreach ($reviews as $review) {
                $totalRating += $review->getRating();
                $reviewCount++;
            }

            $ratingJeux[$jeu->getId()] = $totalRating / $reviewCount;
        }

        $jeux_form = new Jeux();
        $ajouter_form = $this->createForm(JeuxType::class, $jeux_form);
        $ajouter_form->handleRequest($request);

        if ($ajouter_form->isSubmitted() && $ajouter_form->isValid()) {
            $nomGame = $jeux_form->getNomGame();
            $gameExists = $repo->findOneBy(['nomGame' => $nomGame]);
            if ($gameExists) {
                // Game already exists, display error message or redirect to edit page
                return $this->render('jeux/error.html.twig', ['message' => 'Ce nom de jeux est deja utilisé','nomGame' => $nomGame]);
            }
            $photoJ = $ajouter_form->get('picture')->getData();
            if ($photoJ) {
                $originalImgName = pathinfo($photoJ->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImgname = $slugger->slug($originalImgName);
                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoJ->guessExtension();
                try {
                    $photoJ->move(
                        $this->getParameter('imgj_directory'),
                        $newImgename
                    );
                } catch (FileException $e) {
                }
                $jeux_form->setImage($newImgename);
            }
            $em = $this->managerRegistry->getManagerForClass(Jeux::class);
            $em->persist($jeux_form);
            $em->flush();
            return new RedirectResponse($this->generateUrl('afficher_les_jeux_back'));
        }
        return $this->render('jeux/jeuxback.html.twig', array(
            'jeux' => $jeux,
            'ratingJeux' => $ratingJeux,
            'form' => $ajouter_form->createView(),
        ));
    }

    /**
     * @param $id
     * @return Response
     */
    #[Route('/delete_jeux/{id}', name: 'supprimer_jeux')]
    public function delete_jeux($id): Response
    {
        $entityManager = $this->managerRegistry->getManagerForClass(Jeux::class);
        $jeux = $entityManager->getRepository(Jeux::class)->find($id);

        if (!$jeux) {
            throw $this->createNotFoundException('No jeux found for id '.$id);
        }

        $entityManager->remove($jeux);
        $entityManager->flush();
        return $this->redirectToRoute('afficher_les_jeux_back');
    }


    /**
     * @param Request $request
     * @param JeuxRepository $repo
     * @param int $id
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/modifierjeu/{id}', name: 'modifier_jeu')]
    public function modifier_jeu(Request $request,JeuxRepository $repo, int $id, SluggerInterface $slugger): Response
    {


        $jeux=$repo->findOneBy(['id' => $id]);
        $form = $this->createForm(JeuxType2::class, $jeux);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoJ = $form->get('picture')->getData();
            if ($photoJ) {
                $originalImgName = pathinfo($photoJ->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImgname = $slugger->slug($originalImgName);
                $newImgename = $safeImgname.'-'.uniqid().'.'.$photoJ->guessExtension();
                try {
                    $photoJ->move(
                        $this->getParameter('imgj_directory'),
                        $newImgename
                    );
                } catch (FileException $e) {
                }
                $jeux->setImage($newImgename);
            }
            $em = $this->managerRegistry->getManagerForClass(Jeux::class);
            $em->persist($jeux);
            $em->flush();
            return $this->redirect($this->generateUrl('afficher_les_jeux_back'));
        }
        return $this->renderForm('jeux/modifierjeuxback.html.twig', [
            'form' => $form,
            'jeux'=> $jeux,

        ]);
    }

   

    /**
     * Parsing Json
     */
    #[Route('/Afficher_jeux_Json', name: 'AfficherJeuxJson')]
    public function Afficher_jeux_Json(JeuxRepository $jeuxRepository,NormalizerInterface $normalizer)
    {
        $jeux=$jeuxRepository->findAll();
        $jeuxNormalises = $normalizer->normalize($jeux,'json',['groups'=>"jeux"]);
        $json = json_encode($jeuxNormalises);
        return new  Response($json);
    }

    #[Route('/Rechercher_jeux_Json/{id}', name: 'RechercherJeuxJson')]
    public function Rechercher_jeux_Json(JeuxRepository $jeuxRepository,NormalizerInterface $normalizer,$id)
    {
        $jeux=$jeuxRepository->find($id);
        $jeuxNormalises = $normalizer->normalize($jeux,'json',['groups'=>"jeux"]);
        $json = json_encode($jeuxNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

    #[Route('/Ajouter_Jeux_Json/new', name: 'AjouterJeuxJson')]
    public function Ajouter_jeux_Json(Request $req,NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $jeux= new Jeux();
        $jeux->setNomGame($req->get('nomGame'));
        $jeux->setMaxPlayers($req->get('maxPlayers'));
        $jeux->setDescription($req->get('description'));
        $jeux->setDateAddGame(new \DateTime());
        $em->persist($jeux);
        $em->flush();
        $jsonContent = $normalizer->normalize($jeux,'json',['groups'=>'jeux']);
        return new  Response(json_encode($jsonContent));
    }

    #[Route('/modifierjeu/{id}', name: 'modifierjeuJson')]
    public function modifierjeu_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $jeux= $em->getRepository(Jeux::class)->find($id);
        $jeux->setNomGame($req->get('nomGame'));
        $jeux->setMaxPlayers($req->get('maxPlayers'));
        $jeux->setDescription($req->get('description'));
        $jeux->setDateAddGame(new \DateTime());
        $em->flush();
        $jsonContent = $normalizer->normalize($jeux,'json',['groups'=>'jeux']);
        return new  Response("Jeux updated",json_encode($jsonContent));
    }

    #[Route('/supprimerjeu/{id}', name: 'supprimerjeuJson')]
    public function supprimerjeu_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $jeux= $em->getRepository(Jeux::class)->find($id);
        $em->remove($jeux);
        $em->flush();
        $jsonContent = $normalizer->normalize($jeux,'json',['groups'=>'jeux']);
        return new  Response("Jeux deleted",json_encode($jsonContent));
    }



   
}
