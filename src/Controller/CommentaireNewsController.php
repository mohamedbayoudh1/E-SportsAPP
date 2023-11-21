<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\CommentaireNews;
use App\Entity\Gamer;
use App\Entity\News;
use App\Entity\User;
use App\Form\CommentaireNewsType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\NewsRepository;
use App\Repository\CommentaireNewsRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommentaireNewsController extends BaseController
{
    /**
     * @param array $comments
     * @return array
     * retourner la liste des utilisateur qui on postez un commentaire
     */
    private function getUserNames (array $comments ) : array
    {
        $names = [];
        foreach ( $comments as $comment ) {
            $user    = $comment -> getUser ();
            $names[] = [
                'id' => $user -> getId () ,
                'name' => $user -> getNom () . ' ' . $user -> getPrenom (),
                'tag' => $user->getTag(),
                'PhotoProfil'=> $user -> getPhotoProfil()
            ];
        }
        return $names;
    }

    /**
     * @param Request $request
     * @param NewsRepository $newsRepository
     * @param CommentaireNewsRepository $commentRepository
     * @param $id
     * @return Response
     */
    #[Route( '/news/{id}' , name : 'news' )]
    public function news ( Request $request , NewsRepository $newsRepository , CommentaireNewsRepository $commentRepository , $id ) : Response
    {
        // Chercher l'actualité avec l'ID
        $news        = $newsRepository -> findOneBy ( [ 'id' => $id ] );
        // Retourner l'utilisateur courant dans la session
        $user        = $this -> getUserFromSession ();
        $commentaire = new CommentaireNews();
        $commentaire -> setUser ( $user );
        $commentaire -> setIdNews ( $news );
        $form = $this -> createForm ( CommentaireNewsType::class , $commentaire );
        $form -> handleRequest ( $request );
        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $em = $this -> managerRegistry -> getManagerForClass ( CommentaireNews::class );
            $em -> persist ( $commentaire );
            $em -> flush ();
            return $this -> redirectToRoute ( 'news' , [ 'id' => $id ] );
        }
        $offset        = $request -> query -> get ( 'offset' , 0 );
        $limit         = 100;
        $comments      = $commentRepository -> findBy ( [ 'idNews' => $news ] , [ 'date' => 'DESC' ] , $limit , $offset );
        $totalComments = $commentRepository -> count ( [ 'idNews' => $news ] );
        $game          = $news -> getIdJeux ();
        $gameName      = $game -> getNomGame ();
        $names         = $this -> getUserNames ( $comments );
        $loadMoreUrl   = $this -> generateUrl ( 'news' , [ 'id' => $id , 'offset' => $offset + $limit ] );
        // Verifier si l'utilisateur a postez deja 3 commentaire sur cette article
        $commentCount = $commentRepository->count(['user' => $user, 'idNews' => $news]);
        $canAddComment = $commentCount < 3;
        return $this -> render ( 'news/comments.html.twig' , [
            'news' => $news ,
            'comments' => $comments ,
            'names' => $names ,
            'gameName' => $gameName ,
            'form' => $form -> createView () ,
            'loadMoreUrl' => $loadMoreUrl ,
            'hasMoreComments' => ( $offset + $limit < $totalComments ) ,
            'canAddComment' => $canAddComment ,
        ] );
    }

    /**
     * @param Request $request
     * @param NewsRepository $newsRepository
     * @param CommentaireNewsRepository $commentRepository
     * @param $id
     * @return Response
     */
    #[Route( '/newsback/{id}' , name : 'comment_back' )]
    public function news_back ( Request $request , NewsRepository $newsRepository , CommentaireNewsRepository $commentRepository , $id ) : Response
    {
        $news        = $newsRepository -> findOneBy ( [ 'id' => $id ] );
        $user        = $this -> getUserFromSession ();
        $commentaire = new CommentaireNews();
        $commentaire -> setUser ( $user );
        $commentaire -> setIdNews ( $news );
        $form = $this -> createForm ( CommentaireNewsType::class , $commentaire );
        $form -> handleRequest ( $request );
        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $em = $this -> managerRegistry -> getManagerForClass ( CommentaireNews::class );
            $em -> persist ( $commentaire );
            $em -> flush ();
        }
        $comments = $commentRepository -> findBy ( [ 'idNews' => $news ] );
        $game     = $news -> getIdJeux ();
        $gameName = $game -> getNomGame ();
        $names    = $this -> getUserNames ( $comments );
        return $this -> render ( 'news/commentsback.html.twig' , [
            'news' => $news ,
            'comments' => $comments ,
            'names' => $names ,
            'gameName' => $gameName ,
            'form' => $form -> createView () ,
        ] );
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    #[Route( '/supprimercommentaire/{id}' , name : 'supprimer_commentaire' )]
    public function delete_comment ( Request $request , $id ) : Response
    {
        $entityManager = $this -> managerRegistry -> getManagerForClass ( CommentaireNews::class );
        $comment       = $entityManager -> getRepository ( CommentaireNews::class ) -> find ( $id );

        if ( ! $comment ) {
            throw $this -> createNotFoundException ( 'No comment found for id ' . $id );
        }
        $entityManager -> remove ( $comment );
        $entityManager -> flush ();
        $referer = $request -> headers -> get ( 'referer' );

        return $this -> redirect ( $referer );
    }

    /**
     * @param Request $request
     * @param CommentaireNewsRepository $commentRepository
     * @param $id
     * @return Response
     */
    #[Route( '/news_comment/{id}' , name : 'news_comment' )]
    public function news_comment_edit ( Request $request , CommentaireNewsRepository $commentRepository , $id ) : Response
    {
        $jeux = $commentRepository -> findOneBy ( [ 'id' => $id ] );
        $form = $this -> createForm ( CommentaireNewsType::class , $jeux );
        $form -> handleRequest ( $request );
        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $em = $this -> managerRegistry -> getManagerForClass ( CommentaireNews::class );
            $em -> persist ( $jeux );
            $em -> flush ();
            return $this -> redirectToRoute ( 'news' , [ 'id' => $jeux -> getIdNews () -> getId () ] );
        }
        return $this -> renderForm ( 'news/comments_edit.html.twig' , [
            'form' => $form ,
        ] );
    }

    #[Route('/Rechercher_comments_news_Json/{id}', name: 'RecherchercommentsnewsJson')]
    public function Rechercher_news_comments_Json( CommentaireNewsRepository $CommentairenewsRepository , NormalizerInterface $normalizer , $id , NewsRepository $newsRepository )
    {
        $news = $newsRepository -> findOneBy( [ 'id' => $id ] );
        $comments = $CommentairenewsRepository -> createQueryBuilder( 'cn' )
            -> select( 'cn' , 'u' )
            -> leftJoin( 'cn.user' , 'u' )
            -> where( 'cn.idNews = :news_id' )
            -> setParameter( 'news_id' , $news -> getId() )
            -> getQuery()
            -> getResult();

        $commentsNormalized = [];
        foreach ( $comments as $comment ) {
            $userNormalized = $normalizer -> normalize( $comment -> getUser() , 'json' , [ 'groups' => ['user', 'comment'] ] );
            $commentNormalized = $normalizer -> normalize( $comment , 'json' , [ 'groups' => 'comment' ] );
            $commentNormalized[ 'user' ] = $userNormalized;
            $commentsNormalized[] = $commentNormalized;
        }
        $json = json_encode( $commentsNormalized , JSON_PRETTY_PRINT );
        return new Response( $json );
    }

    #[Route('/Ajouter_Commentaire_Json', name: 'AjouterCommentaireJson')]
    public function Ajouter_Commentaire_Json(Request $req,NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();

        // Fetch the News entity corresponding to the ID value
        $newsId = $req->get('idNews');
        $news = $em->getRepository(News::class)->find($newsId);

        // Create and persist the CommentaireNews entity
        $commentairenews= new CommentaireNews();
        $commentairenews->setIdNews($news);

        $userId = $req->get('user');
        $userG = $em->getRepository(Gamer::class)->find($userId);
        $userC = $em->getRepository(Coach::class)->find($userId);
        if ($userG == null)
        {
            $commentairenews->setUser($userC);
        }else{
            $commentairenews->setUser($userG);
        }
        $commentairenews->setDescription($req->get('description'));
        $commentairenews->setDate(new \DateTime());
        $em->persist($commentairenews);
        $em->flush();

        // Serialize and return the CommentaireNews entity as JSON
        $jsonContent = $normalizer->normalize($commentairenews,'json',['groups'=>'comment']);
        return new Response(json_encode($jsonContent));
    }


}
