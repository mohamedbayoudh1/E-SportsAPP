<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Gamer;
use App\Entity\HistoriqueAchat;
use App\Entity\Produit;
use App\Entity\RechargeCode;
use App\Form\CategorieType;
use App\Form\ProduitType;
use App\Form\UpdateProduitpType;

use App\Repository\CategorieRepository;
use App\Repository\HistoriqueAchatRepository;
use App\Repository\ProduitRepository;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Illuminate\Database\Eloquent\Builder;


class ProductController extends BaseController
{
    /*n************************************************************************************* categorie ***************************************************************n*/
    #[Route('/consultecategg/{id}', name: 'affichecategg')]
    public function consultcateg(ManagerRegistry $doctrine,Request $request,int $id): Response
    {

        $categorie= $doctrine->getRepository(Categorie::class)->find($id);
        $produit=$categorie->getProduits();

        $pointAmount = $request->request->get('pointAmount');
        $dinarAmount = $pointAmount / 230;


        return $this->renderForm('product/ConsultCategorie.html.twig',
            [
                'produit'=>$produit,
                'c'=>$categorie,
                'dinarAmount' =>round($dinarAmount) ,
                'pointAmount' => $pointAmount
            ]);
    }




    #[Route('/addc', name: 'ajoutCategorie')]
    public function add(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
            $em = $doctrine->getManager();
            $existingCategorie = $em->getRepository(Categorie::class)->findOneBy(['nom' => $nom]);

            if ($existingCategorie) {
                $this->addFlash('danger', 'Une catégorie avec ce nom existe déjà.');
                return $this->redirectToRoute('ajoutCategorie');
            }

            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');
            return $this->redirectToRoute('ad_categorie');
        }

        return $this->renderForm('product/categorie.html.twig', [
            'form' => $form
        ]);
    }




    //list categorie
    #[Route('/categoriead', name: 'ad_categorie')]
    public function adlistec(ManagerRegistry $doctrine): Response
    {
        $categorie =$doctrine->getRepository(Categorie::class)->findAll();

        //dd($produit);
        return $this->render('product/adListeCategorie.html.twig', [
            'adcategorie' => $categorie


        ]);
    }



    /*supp categ*/
    #[Route("/deletec/{id}", name:'supprimercategorie')]
    public function deletec($id, ManagerRegistry $doctrine, ProduitRepository $postRepository)
    {
        $em = $doctrine->getManager();

        // Récupérer le groupe correspondant à l'id
        $categorie= $doctrine->getRepository(Categorie:: class)->find($id);

        // Récupérer tous les produit associés
        $produits = $categorie->getProduits();

        // Supprimer chaque produit
        foreach ($produits as $produit) {
            $em->remove($produit);
        }


        // Supprimer la categorie lui-même
        $em->remove($categorie);

        $em->flush();

        return $this->redirectToRoute('ad_categorie');
    }




    #[Route('/modifierc/{id}', name: 'modifierCategorie')]
    public function updatec(Request $request, EntityManagerInterface $em, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('ad_categorie', ['id' => $categorie->getId()]);
        }

        return $this->renderForm('product/admodifiercategorie.html.twig', [
            'form' => $form,
            'categorie' => $categorie,
        ]);
    }






    /*n************************************************************************************* Produit ***************************************************************n*/

    #[Route('/product', name: 'app_product')]
    public function index(ManagerRegistry $doctrine,Request $request): Response
    {
        $produit =$doctrine->getRepository(Produit::class)->findAll();
        $categorie= $doctrine->getRepository(Categorie::class)->findAll();

        return $this->render('product/store.html.twig', [
            'produit' => $produit,
            'c'=>$categorie

        ]);
    }
    #[Route('/productCarte', name: 'app_product_carte')]
    public function indexcarte(ManagerRegistry $doctrine,Request $request): Response
    {
        $produit =$doctrine->getRepository(Produit::class)->findAll();
        $categorie= $doctrine->getRepository(Categorie::class)->findAll();

        return $this->render('product/produitCarte.html.twig', [
            'produit' => $produit,
            'c'=>$categorie

        ]);
    }




    #[Route('/consulteproduct/{id}', name: 'afficheproduit')]
    public function oneProduct(int $id, ProduitRepository $produitRepository, ManagerRegistry $doctrine)
    {
        $produit = $produitRepository->find($id);
        $etat=false;
        // Récupérer le produit à ajouter au panier à partir de l'ID
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $gamerId = $this->session->get('Gamer_id');
        $gamer = $this->getDoctrine()->getRepository(Gamer::class)->find($gamerId);

        $entityManager = $doctrine->getManager();

        // Vérifier si le produit est déjà dans le panier du gamer
        $historiqueAchat = $this->getDoctrine()->getRepository(HistoriqueAchat::class)
            ->findOneBy(['id_gamer' => $gamer, 'idproduit' => $produit,'etatachat'=>false]);

        if ($historiqueAchat) {
            $etat=true;
        }


        return $this->render('product/produit.html.twig', [
            'p'=>$produit,
            'etat'=>$etat

        ]);

    }




    //list product
    #[Route('/productad', name: 'ad_product')]
    public function adlistep(ManagerRegistry $doctrine): Response
    {
        $produit =$doctrine->getRepository(Produit::class)->findAll();

        //dd($produit);
        return $this->render('product/adListeProduit.html.twig', [
            'adproduit' => $produit

        ]);
    }

    //ajout product ad
    #[Route('/addp/{idCategorie}', name: 'ajoutproduit')]
    public function addp(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request,SluggerInterface $slugger, $idCategorie): Response
    {

        $produit =new Produit();

        $form =$this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            /* img*/
            $photoP = $form->get('imagep')->getData();
            if ($photoP) {
                $originalImgName = pathinfo($photoP->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);

                $newImgename = $safeImgname . '-' . uniqid() . '.' . $photoP->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $photoP->move(
                        $this->getParameter('imgpr_directory'),
                        $newImgename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newImgename);
            }
            // Récupérer l'entité Catégorie correspondante à l'ID de catégorie fourni

            /*endimg*/
            // Définir la catégorie pour le produit

            $em =$doctrine->getManager();



            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('ad_categorie');
        }
        return $this->renderForm('product/addproduct.html.twig', [
            'form'=>$form,'id'=>$idCategorie
        ]);

    }
    #[Route('/deletep/{id}', name: 'supprimerproduit')]
    public function deletep($id, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'id '.$id);
        }

        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('ad_product');
    }
    #[Route('/modifier/{id}', name: 'modifierProduit')]
    public function update(Request $request, EntityManagerInterface $em, Produit $produit,SluggerInterface $slugger): Response
    {
        $categorie=new Categorie();
        $categorie=$produit->getIdCategorie();
        $form = $this->createForm(UpdateProduitpType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* img*/
            $photoP = $form->get('imagep')->getData();
            if ($photoP) {
                $originalImgName = pathinfo($photoP->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeImgname = $slugger->slug($originalImgName);

                $newImgename = $safeImgname . '-' . uniqid() . '.' . $photoP->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $photoP->move(
                        $this->getParameter('imgpr_directory'),
                        $newImgename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newImgename);
            }
            // Récupérer l'entité Catégorie correspondante à l'ID de catégorie fourni

            /*endimg*/
            $produit->setIdCategorie($categorie);
            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('ad_product', ['id' => $produit->getId()]);
        }

        return $this->renderForm('product/admodifierproduit.html.twig', [
            'form' => $form,
            'produit' => $produit,
        ]);
    }











    /*---------------panier------------------*/

    /**
     * @Route("/ajouterAuPanier/{id}", name="ajoutpanier")
     */
    public function ajouterAuPanier(Request $request, $id, ManagerRegistry $doctrine)
    {
        // Récupérer le produit à ajouter au panier à partir de l'ID
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $gamerId = $this->session->get('Gamer_id');
        $gamer = $this->getDoctrine()->getRepository(Gamer::class)->find($gamerId);
        $checkP=false;
        $entityManager = $doctrine->getManager();

        // Vérifier si le produit est déjà dans le panier du gamer
        $historiqueAchat = $this->getDoctrine()->getRepository(HistoriqueAchat::class)
            ->findOneBy(['id_gamer' => $gamer, 'idproduit' => $produit,'etatachat'=>false]);

        if ($historiqueAchat) {
            // Si le produit est déjà dans le panier, retourner une réponse d'erreur
           //return new Response('Ce produit est déjà dans votre panier.');
            $checkP=true;
            return $this->redirect('/consulteproduct/'.$id);

        } else {
            // Ajouter le produit au panier
            $pannier = new HistoriqueAchat();
            $pannier->setIdGamer($gamer);
            $pannier->setIdproduit($produit);
            $pannier->setEtatachat(false);
            $entityManager->persist($pannier);
            $entityManager->flush();

            return $this->redirect("/consulteproduct/".$id);
        }
    }


    /**
     * @Route("/pannier", name="pannier")
     */
    public function Panier(Request $request,ManagerRegistry $doctrine) {
        // Récupérer le produit à ajouter au panier à partir de l'ID
        $historique=$this->getDoctrine()->getRepository(HistoriqueAchat::class)->findBy(['id_gamer'=>$this->session->get('Gamer_id'),'etatachat'=>false]);
        $prixtotal = 0;
        $etat =true;
        $pointuser=0;
        $check=true;
        foreach ($historique as $item) {
            $prixtotal += $item->getIdProduit()->getPrix();
            $pointuser=$item->getIdGamer()->getPoint();
            if($item->getIdProduit()->getQuantite() ==0){
                $check=false;
            }
        }
        if ($pointuser<$prixtotal || !$historique || !$check){
            $etat=false;
        }
        return $this->render('product/panier.html.twig', [
            'produits' => $historique,
            'prixtotal'=>$prixtotal,
            'conversion'=>round($prixtotal/230),
            'etat'=>$etat
        ]);
    }
    /**
     * @Route("/sharepannier", name="sharepannier")
     */
    public function Paniershare() {

        return $this->render('product/shareproduct.html.twig');
    }
    /* *****************************  supprimer du panier mel page produit    **************************** */
    #[Route('/supprimerDuPanier/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $em =$doctrine->getManager();
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $produit = $doctrine->getRepository(Produit::class)->find($id);
        $panier =$em->getRepository(HistoriqueAchat::class)->findOneBy(['id_gamer' => $gamer, 'idproduit' => $produit,'etatachat'=>false]);

        if (!$produit || !$panier) {
            throw $this->createNotFoundException('Product not found');
        }else
        {
            if(!$panier->isEtatachat()){
                $em->remove($panier);
                $em->flush();
                return $this->redirect("/consulteproduct/".$id);
            }else
            {
                return $this->redirect("/consulteproduct/".$id);
            }
        }
    }
    /* *****************************  supprimer prod mel page pannier (achat)   **************************** */
    #[Route('/supprimerDuPanierB/{id}', name: 'supprimer_du_panierB')]
    public function supprimerDuPanierB(ManagerRegistry $doctrine,Request $request, int $id)
    {
        $em =$doctrine->getManager();
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $produit = $doctrine->getRepository(Produit::class)->find($id);
        $panier =$em->getRepository(HistoriqueAchat::class)->findOneBy(['id_gamer' => $gamer, 'idproduit' => $produit,'etatachat'=>false]);

        if (!$produit) {
            throw $this->createNotFoundException('Product not found');
        }else
        {
            if(!$panier->isEtatachat()){
                $em->remove($panier);
                $em->flush();
                return $this->redirectToRoute('pannier');
            }else
            {
                return $this->redirectToRoute('pannier');
            }
        }
    }










    /********************************************************/
    #[Route('/acheterproduit', name: 'acheterproduit')]
    public function acheterproduit(ManagerRegistry $doctrine,Request $request)
    {

        $em =$doctrine->getManager();
        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));

        $panier =$em->getRepository(HistoriqueAchat::class)->findBy(['id_gamer' => $gamer,'etatachat'=>false]);

        if (!$panier) {
            throw $this->createNotFoundException('Product not found');
        }else
        {
            $prixtotal = 0;
            $pointuser=0;

            foreach ($panier as $item) {
                $prixtotal += $item->getIdProduit()->getPrix();
                $pointuser=$item->getIdGamer()->getPoint();
                $quantite=$item->getIdProduit()->getQuantite();
                if($quantite>=1)
                {
                $item->getIdProduit()->setQuantite( $quantite-1);
                }
                $item->setEtatachat(true);
                $item->setDateDachat(new \DateTime());
                $prefix = uniqid(); // unique timestamp-based prefix
                $randomBytes = random_bytes(1);
                $randomString = bin2hex($randomBytes);
                $uniqueString = $prefix . $randomString;
                $item->setReference($uniqueString);

                $em->persist($item);
                if ($item->getIdProduit()->getIdCategorie()->getType()=='Recharge')
                {
                    $em2 = $this->managerRegistry->getManagerForClass(RechargeCode::class);
                    $code=new RechargeCode();
                    $code->setCode($item->getReference());
                    if ($item->getIdProduit()->getPrix()<=10){
                        $code->setpoint(1000);
                    }
                    elseif ($item->getIdProduit()->getPrix()<=70 && $item->getIdProduit()->getPrix()>40 )
                    {
                        $code->setpoint(10000);
                    }
                    else
                    {
                        $code->setpoint(30000);
                    }
                    $code->setPrice(0);
                    $em2->persist($code);
                    $em2->flush();
                }


            }
            if ($pointuser>=$prixtotal and $quantite>=1 ){
                $gamer->setPoint($gamer->getPoint()-$prixtotal);

                $request->getSession()->set('Solde',$gamer->getPoint());
                $em->persist($gamer);
                $em->flush();




            }

            return $this->redirectToRoute('pannier');

        }
    }



    /********************************************************/
    #[Route('/historique_achat', name: 'historique_achat')]
    public function historique_achat(ManagerRegistry $doctrine,Request $request, HistoriqueAchatRepository $produitRepository)
    {

        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $historiqueAchat = $produitRepository->findachatByGamerIdEtat($gamer->getId());
        $tag=$gamer->getNom()." ".$gamer->getPrenom();
        $prixtotal = 0;
        foreach ($historiqueAchat as $item) {
            $prixtotal += $item->getIdProduit()->getPrix();
        }
        return $this->render('product/historiqueAchatGamer.html.twig', [
            'historiqueAchat' => $historiqueAchat,
            'tag'=>$tag,
            'prixtotal'=>$prixtotal,
            'prixtotaldnt'=>round($prixtotal/230)

        ]);

}
    #[Route('/Afficher_produit_Json', name: 'AfficherproduitJson')]
    public function Afficher_produit_Json(ProduitRepository $produitRepository,NormalizerInterface $normalizer)
    {
        $produit=$produitRepository->findAll();
        $produitNormalises = $normalizer->normalize($produit,'json',['groups'=>"produit"]);
        $json = json_encode($produitNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);

    }




    #[Route('/pdf', name: 'pdf')]
    public function pdf(ManagerRegistry $doctrine,Request $request, HistoriqueAchatRepository $produitRepository)
    {

        $gamer= $this->managerRegistry->getRepository(Gamer::class)->findOneBy((['id' => $request->getSession()->get('Gamer_id')]));
        $historiqueAchat = $produitRepository->findachatByGamerIdEtat($gamer->getId());
        $tag=$gamer->getNom()." ".$gamer->getPrenom();
        $prixtotal = 0;

        $prefix = uniqid(); // unique timestamp-based prefix
        $randomBytes = random_bytes(2);
        $randomString = bin2hex($randomBytes);
        $uniqueString = $prefix . $randomString;


        foreach ($historiqueAchat as $item) {
            $prixtotal += $item->getIdProduit()->getPrix();
        }
        return $this->render('product/pdfpage.html.twig', [
            'historiqueAchat' => $historiqueAchat,
            'tag'=>$tag,
            'prixtotal'=>$prixtotal,
            'prixtotaldnt'=>round($prixtotal/230),
            'uniqueString' => $uniqueString

        ]);
    }

    /******************************************************************************  mobile  ************************************************************************************/

    

    #[Route('/Rechercher_produit_Json/{id}', name: 'RechercherproduitJson')]
    public function Rechercher_produit_Json(ProduitRepository $produitRepository,NormalizerInterface $normalizer,$id)
    {
        $produit=$produitRepository->find($id);
        $produitNormalises = $normalizer->normalize($produit,'json',['groups'=>"jeux"]);
        $json = json_encode($produitNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

    #[Route('/Ajouter_produit_Json', name: 'AjouterproduitJson')]
    public function Ajouter_produit_Json(Request $req,NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $produit= new Produit();
        $produit->setNom($req->get('nom'));
        $produit->setPrix($req->get('prix'));
        $produit->setDescription($req->get('description'));
        $produit->setImage($req->get('image'));

        $em->persist($produit);
        $em->flush();
        $jsonContent = $normalizer->normalize($produit,'json',['groups'=>'produit']);
        return new  Response(json_encode($jsonContent));
    }

    #[Route('/modifierproduit/{id}', name: 'modifierproduitJson')]
    public function modifierproduit_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $produit= $em->getRepository(Produit::class)->find($id);
        $produit->setNomGame($req->get('nomGame'));
        $produit->setMaxPlayers($req->get('maxPlayers'));
        $produit->setDescription($req->get('description'));
        $produit->setDateAddGame(new \DateTime());
        $em->flush();
        $jsonContent = $normalizer->normalize($produit,'json',['groups'=>'produit']);
        return new  Response("produit updated",json_encode($jsonContent));
    }

    #[Route('/supprimerproduit/{id}', name: 'supprimerproduitJson')]
    public function supprimerproduit_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $produit= $em->getRepository(Produit::class)->find($id);
        $em->remove($produit);
        $em->flush();
        $jsonContent = $normalizer->normalize($produit,'json',['groups'=>'produit']);
        return new  Response("produit deleted",json_encode($jsonContent));
    }

    #[Route('/consultecateggJson/{id}', name: 'affichecateggJson')]
    public function consultcategJson(ManagerRegistry $doctrine,Request $request,int $id,NormalizerInterface $normalizer): Response
    {

        $categorie= $doctrine->getRepository(Categorie::class)->find($id);
        $produit=$categorie->getProduits();

        $produitNormalises = $normalizer->normalize($produit,'json',['groups'=>"produit"]);
        $json = json_encode($produitNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

/***************************************categorie**********************/

    #[Route('/Afficher_categorie_Json', name: 'AffichercategorieJson')]
    public function Afficher_categorie_Json(CategorieRepository $categorieRepository,NormalizerInterface $normalizer)
    {
        $categorie=$categorieRepository->findAll();
        $categorieNormalises = $normalizer->normalize($categorie,'json',['groups'=>"categorie"]);
        $json = json_encode($categorieNormalises, JSON_PRETTY_PRINT);
        return new  Response($json);
    }

    #[Route('/Ajouter_categorie_Json', name: 'AjoutercategorieJson')]
    public function Ajouter_categorie_Json(Request $req,NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie= new Categorie();
        $categorie->settype($req->get('type'));
        $categorie->setNom($req->get('nom'));

        $em->persist($categorie);
        $em->flush();
        $jsonContent = $normalizer->normalize($categorie,'json',['groups'=>'categorie']);
        return new  Response(json_encode($jsonContent));
    }


    #[Route('/supprimercategorie/{id}', name: 'supprimercategorieJson')]
    public function supprimercategorie_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie= $em->getRepository(Categorie::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        $jsonContent = $normalizer->normalize($categorie,'json',['groups'=>'categorie']);
        return new  Response("categorie deleted",json_encode($jsonContent));
    }

    #[Route('/modifiercategorie/{id}', name: 'modifiercategorieJson')]
    public function modifiercategorie_Json(Request $req,NormalizerInterface $normalizer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $categorie= $em->getRepository(Categorie::class)->find($id);
        $categorie->setNomGame($req->get('type'));
        $categorie->setNomGame($req->get('nom'));

        $em->flush();
        $jsonContent = $normalizer->normalize($categorie,'json',['groups'=>'categorie']);
        return new  Response("categorie updated",json_encode($jsonContent));
    }




}