<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Form\NewsType2;
use App\Repository\NewsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewsController extends BaseController
{


    /*
     * fonction d'affichage pour les actualités
     */
    /**#[Route( '/news' , name : 'afficher_les_news' )]
    public function afficher_les_news ( PaginatorInterface $paginator , Request $request , ) : Response
    {

        $newsRepository = $this -> managerRegistry -> getRepository ( News::class );
        $queryBuilder   = $newsRepository -> createQueryBuilder ( 'n' )
            -> orderBy ( 'n.dateN' , 'DESC' );
        $news           = $paginator -> paginate ( $queryBuilder , $request -> query -> getInt ( 'page' , 1 ) , 10 );
        return $this -> render ( 'news/index.html.twig' , array ( 'news' => $news ) );
    }**/
    #[Route('/news', name: 'afficher_les_news')]
    public function afficher_les_news(PaginatorInterface $paginator, Request $request): Response
    {
        $newsRepository = $this->managerRegistry->getRepository(News::class);
        $queryBuilder = $newsRepository->createQueryBuilder('n')
            ->orderBy('n.dateN', 'DESC');
    
        // Retrieve the search term and selected date from the request object
        // Retrieve the search term and date from the request object
$searchTerms = $request->query->get('search');
$date = $request->query->get('date');

// If a search term was entered, add a WHERE clause to the query to filter by the search term
if ($searchTerms) {
    $queryBuilder->andWhere('n.titre LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $searchTerms . '%');
}

// If a date was selected, add a WHERE clause to the query to filter by the selected date
if ($date) {
    $queryBuilder->andWhere('n.dateN >= :startDate')
        ->andWhere('n.dateN < :endDate')
        ->setParameter('startDate', new \DateTime($date))
        ->setParameter('endDate', (new \DateTime($date))->modify('+1 day'));
}

// Paginate the query results
$news = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 4);

// Render the appropriate template based on whether a search term was provided
if ($searchTerms) {
    return $this->render('news/news_list.html.twig', [
        'news' => $news,
    ]);
} else {
    return $this->render('news/indexxx.html.twig', [
        'news' => $news,
        'searchTerms' => $searchTerms,
        'date' => $date,
    ]);
}

    }
    
    
    
    



    #[Route( '/news_back' , name : 'afficher_les_news_back' )]
    public function newsBack ( PaginatorInterface $paginator , Request $request , SluggerInterface $slugger ) : Response
    {
        $newsRepository = $this -> managerRegistry -> getRepository ( News::class );
        $queryBuilder   = $newsRepository -> createQueryBuilder ( 'n' )
            -> orderBy ( 'n.dateN' , 'DESC' );
        $news           = $paginator -> paginate ( $queryBuilder , $request -> query -> getInt ( 'page' , 1 ) , 4);

        $newNews = new News();
        $form    = $this -> createForm ( NewsType::class , $newNews );


        $form -> handleRequest ( $request );

        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $photoJ = $form -> get ( 'picture' ) -> getData ();
            if ( $photoJ ) {
                $originalImgName = pathinfo ( $photoJ -> getClientOriginalName () , PATHINFO_FILENAME );
                $safeImgname     = $slugger -> slug ( $originalImgName );
                $newImgename     = $safeImgname . '-' . uniqid () . '.' . $photoJ -> guessExtension ();
                try {
                    $photoJ -> move (
                        $this -> getParameter ( 'imgj_directory' ) ,
                        $newImgename
                    );
                } catch ( FileException $e ) {
                }
                $newNews -> setImage ( $newImgename );
            }
            $entityManager = $this -> managerRegistry -> getManagerForClass ( News::class );
            $entityManager -> persist ( $newNews );
            $entityManager -> flush ();

            // Redirect to the same page to avoid resubmitting the form on refresh
            return $this -> redirectToRoute ( 'afficher_les_news_back' );
        }

        $pageCount = ceil ( $news -> count () / $news -> getItemNumberPerPage () );
        return $this -> render ( 'news/newsback.html.twig' , [
            'news' => $news ,
            'form' => $form -> createView () ,
            'pageCount' => $pageCount
        ] );
    }


    #[Route( '/delete_news/{id}' , name : 'supprimer_news' )]
    public function delete_news ( $id ) : Response
    {
        $entityManager = $this -> managerRegistry -> getManagerForClass ( News::class );
        $news          = $entityManager -> getRepository ( News::class ) -> find ( $id );

        if ( ! $news ) {
            throw $this -> createNotFoundException ( 'No news found for id ' . $id );
        }

        $entityManager -> remove ( $news );
        $entityManager -> flush ();
        return $this -> redirectToRoute ( 'afficher_les_news_back' );
    }


    #[Route( '/modifiernews/{id}' , name : 'modifier_news' )]
    public function modifier_news ( Request $request , NewsRepository $repo , int $id ) : Response
    {
        $news = $repo -> findOneBy ( [ 'id' => $id ] );
        $form = $this -> createForm ( NewsType2::class , $news );
        $form -> handleRequest ( $request );
        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $em = $this -> managerRegistry -> getManagerForClass ( News::class );
            $em -> persist ( $news );
            $em -> flush ();
            return $this -> redirect ( $this -> generateUrl ( 'afficher_les_news_back' ) );
        }
        return $this -> renderForm ( 'news/modifiernewsback.html.twig' , [
            'form' => $form ,
            'news' => $news
        ] );
    }


    /**
     * Parsing Json
     */

    #[Route('/Afficher_news_Json', name: 'AffichernewsJson')]
    public function Afficher_news_Json(NewsRepository $newsRepository,NormalizerInterface $normalizer)
    {
        $news=$newsRepository->findAll();
        $newsNormalises = $normalizer->normalize($news,'json',['groups'=>"news"]);
        $json = json_encode($newsNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

    #[Route('/Rechercher_news_Json/{id}', name: 'RecherchernewsJson')]
    public function Rechercher_news_Json(NewsRepository $newsRepository,NormalizerInterface $normalizer,$id)
    {
        $news=$newsRepository->find($id);
        $newsNormalises = $normalizer->normalize($news,'json',['groups'=>"news"]);
        $json = json_encode($newsNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

    #[Route('/Ajouter_news_Json/new', name: 'AjouternewsJson')]
    public function Ajouter_news_Json(Request $req,NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $news= new News();
        $news->setTitre($req->get('titre'));
        $news->setDescription($req->get('description'));
        $news->setDateN(new \DateTime());
        $em->persist($news);
        $em->flush();
        $jsonContent = $normalizer->normalize($news,'json',['groups'=>'news']);
        return new  Response(json_encode($jsonContent));
    }

    #[Route('/modifiernews/{id}', name: 'modifiernewsJson')]
    public function modifiernews_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $news= $em->getRepository(News::class)->find($id);
        $news->setTitre($req->get('titre'));
        $news->setDescription($req->get('description'));
        $news->setDateN(new \DateTime());
        $em->flush();
        $jsonContent = $normalizer->normalize($news,'json',['groups'=>'news']);
        return new  Response("news updated",json_encode($jsonContent));
    }

    #[Route('/supprimernews/{id}', name: 'supprimernewsJson')]
    public function supprimernews_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $news= $em->getRepository(News::class)->find($id);
        $em->remove($news);
        $em->flush();
        $jsonContent = $normalizer->normalize($news,'json',['groups'=>'news']);
        return new  Response("news deleted",json_encode($jsonContent));
    }

}
