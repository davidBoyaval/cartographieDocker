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
     * @Route("/site", name="site")
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
            dump($site);
        }
        dump($form->getErrors());
        return $this->render('site/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/site/geolocalisation", name="geolocalisation", methods="GET")
     */
    public function geolocalisation(Request $request, SiteRepository $siteRepository)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $siteListe = $siteRepository->findAll();
        foreach ($siteListe as $site) {
            $features[] = array('type' => 'Feature', 'geometry' => array('type' => 'Point', 'coordinates' => [$site->getLongitude(), $site->getLatitude()]));
        }
        $geoJsonObject = array('type' => 'FeatureCollection', 'crs' => array('type' => 'name', 'properties' => array('name' => 'EPSG:3857')), 'features' => $features);
        $json = $serializer->serialize($geoJsonObject, 'json');
        dump($json);

        dump($geoJsonObject);
        $response = JsonResponse::fromJsonString($json, Response::HTTP_OK, ['Access-Control-Allow-Origin' => '*']);
        return $response;
    }
}


// geojsonObject = {
//     //   'type': 'FeatureCollection',
//     //   'crs': {
//     //     'type': 'name',S
//     //     'properties': {
//     //       'name': 'EPSG:3857'
//     //     }
//     //   },
//     //   'features': [{
//     //     'type': 'Feature',
//     //     'geometry': {
//     //       'type': 'Point',
//     //       'coordinates': nws
//     //     }
//     //   }, {
//     //     'type': 'Feature',
//     //     'geometry': {
//     //       'type': 'Point',
//     //       'coordinates': copeaux
//     //     }
//     //   }, {
//     //     'type': 'Feature',
//     //     'geometry': {
//     //       'type': 'Point',
//     //       'coordinates': isdflaubert
//     //     }
//     //   }]
//     // };
