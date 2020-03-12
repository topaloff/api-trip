<?php

namespace App\Controller;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CountryController extends AbstractController
{
    /**
     * @Route("/countries", name="countries", methods={"GET"})
     */
    public function index(CountryRepository $countryRepository)
    {
        $countries = $countryRepository->findAll();
        return $this->json($countries);
    }

    /**
     * @Route("/country", name="country", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data= json_decode($request->getContent(), true);
        $country = new Country($data['name'],$data['flag']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($country);
        $entityManager->flush();
        return $this->json($data);
    }

    /**
     * @Route("/country/{id}", name="country_delete", methods={"DELETE"})
     */
    public function delete(CountryRepository $countryRepository, $id)
    {
        $country = $countryRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($country);
        $entityManager->flush();       
        return $this->json("Country deleted");
    }


    /**
     * @Route("/country/edit/{id}", name="country_edit", methods={"PUT"})
     */
    public function edit(CountryRepository $countryRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $country = $countryRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$country) {
            throw $this->createNotFoundException(
                'No country found for id '.$id
            );
        }      
        $country->setName($data['name']);
        $country->setFlag($data['flag']);
        $entityManager->flush();
        return $this->json("Country updated");
    }


}
