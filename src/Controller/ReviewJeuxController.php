<?php

namespace App\Controller;

use App\Entity\ReviewJeux;
use App\Form\ReviewJeuxType;
use App\Repository\JeuxRepository;
use App\Repository\ReviewJeuxRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewJeuxController extends BaseController
{

    /**
     * @param Request $request
     * @param JeuxRepository $jeuxRepository
     * @param ReviewJeuxRepository $reviewRepository
     * @param $id
     * @return Response
     * Cette fonction envoie le rating que l'utilisateur a mis pour un jeu a la BD
     */
    #[Route('/review/{id}', name: 'review')]
    public function review(Request $request, JeuxRepository $jeuxRepository, ReviewJeuxRepository $reviewRepository, $id): Response
    {
        $jeux = $jeuxRepository->findOneBy(['id' => $id]);
        $user = $this->getUserFromSession();
        // Check if user has already reviewed the game
        $reviewCount = $reviewRepository->count(['user' => $user, 'idJeux' => $jeux]);
        $canAddReview = $reviewCount < 1;
        $reviewjeux = new ReviewJeux();
        $reviewjeux->setUser($user);
        $reviewjeux->setIdJeux($jeux);
        $form = $this->createForm(ReviewJeuxType::class, $reviewjeux);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManagerForClass(ReviewJeux::class);
            $em->persist($reviewjeux);
            $em->flush();
            return $this->redirectToRoute('afficher_les_jeux');
        }
        $form->getErrors();
        return $this->render('jeux/review.html.twig', array(
            'form' => $form->createView(),
            'rh' => $reviewjeux,
            'canAddReview' => $canAddReview,
        ));
    }


}
