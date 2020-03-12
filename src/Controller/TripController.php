<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\CountryRepository;
use App\Repository\CompanyRepository;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// A Mettre pour serialiser le retour du service en json
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class TripController extends AbstractController
{
    /**
     * @Route("/trips", name="trips", methods={"GET"})
     */
    public function index(TripRepository $tripRepository): Response
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $trips = $tripRepository->findAll();

        $trips = $serializer->serialize($trips, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);        
        return new Response($trips, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/trip", name="trip", methods={"POST"})
     */
    public function add(Request $request, CountryRepository $countryRepository, CompanyRepository $companyRepository)
    {
        $data= json_decode($request->getContent(), true);
        $date = new \DateTime($data['departure_date']); 
        $country = $countryRepository->find($data['country']);
        $company = $companyRepository->find($data['company']);
        $trip = new Trip($data['title'],$date,$data['duration'],$country,$company);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trip);
        $entityManager->flush();
        return $this->json($data);
    }

    /**
     * @Route("/trip/{id}", name="trip_delete", methods={"DELETE"})
     */
    public function delete(TripRepository $tripRepository, $id)
    {
        $trip = $tripRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trip);
        $entityManager->flush();       
        return $this->json("Trip deleted");
    }


    /**
     * @Route("/trip/edit/{id}", name="trip_edit", methods={"PUT"})
     */
    public function edit(TripRepository $tripRepository, CountryRepository $countryRepository, CompanyRepository $companyRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $trip = $tripRepository->find($id);

        $date = new \DateTime($data['departure_date']); 
        $country = $countryRepository->find($data['country']);
        $company = $companyRepository->find($data['company']);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$trip) {
            throw $this->createNotFoundException(
                'No trip found for id '.$id
            );
        }      
        $trip->setTitle($data['name']);
        $trip->setDepartureDate($date);
        $trip->setDuration($data['duration']);
        $trip->setCompany($company);
        $trip->setCountry($country);
        $entityManager->flush();
        return $this->json("Trip updated");
    }


}
