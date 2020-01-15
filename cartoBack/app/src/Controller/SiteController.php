<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\DBAL\Schema\View;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;



class SiteController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     */
    public function index(SiteRepository $siteRepository)
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'Liste des sites',
            'sites' => $siteRepository->findAll()
        ]);
    }
    /**
     * @Route("/newsite", name="newsite")
     */
    public function newsite(Request $request, EntityManagerInterface $entityManager)
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site = $form->getData();

            $entityManager->persist($site);
            $entityManager->flush();
        }
        return $this->render('site/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/delete/{id}", name="delete_site")
     */
    public function delete(Site $site,Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository)
    {
            $entityManager->remove($site);
            $entityManager->flush();
            return $this->render('site/index.html.twig', [
                'controller_name' => 'Liste des sites',
                'sites' => $siteRepository->findAll()
            ]);
    }
    /**
     * @Route("/update/{id}", name="update_site")
     */
    public function update(Site $site,Request $request, EntityManagerInterface $entityManager,SiteRepository $siteRepository)
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site = $form->getData();
            $entityManager->persist($site);
            $entityManager->flush();
        }
        return $this->render('site/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/site/geolocalisation", name="geolocalisation", methods="GET")
     */
    public function geolocalisation(Request $request, SiteRepository $siteRepository)
    {
        $featuresType='Feature';
        $featureType='Point';
        $espg = 'EPSG:3857';
        $crsType = 'name';
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $siteListe = $siteRepository->findAll();
        foreach ($siteListe as $site) {
            $adresse = $site->getNumero(). " " .$site->getRue(). " " .$site->getCp()." ".$site->getVille();
            $features[] = array('type' => $featuresType,
                                'geometry' => array('type' => $featureType,
                                                    'coordinates' => [$site->getLongitude(), $site->getLatitude()]),
                                'properties'=>array('adresse'=>$adresse,
                                                    'libelle'=>$site->getLibelle(),
                                                    'poste'=>$site->getPoste(),
                                                    'sujet'=>$site->getSujet(),
                                                    'filiere'=>$site->getFiliere(),
                                                    'date'=>$site->getDate(),
                                                    'code_APE'=>$site->getCodeApe(),
                                                    'CA'=>$site->getCa() 
                                ));
        }
        $geoJsonObject = array('type' => 'FeatureCollection', 'crs' => array('type' => $crsType, 'properties' => array('name' =>  $espg)), 'features' => $features);
        $json = $serializer->serialize($geoJsonObject, 'json');
        $response = JsonResponse::fromJsonString($json, Response::HTTP_OK, ['Access-Control-Allow-Origin' => '*']);
        return $response;
    }
}