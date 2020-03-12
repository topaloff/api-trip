<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CompanyController extends AbstractController
{
    /**
     * @Route("/companies", name="companies", methods={"GET"})
     */
    public function index(CompanyRepository $companyRepository)
    {
        $companies = $companyRepository->findAll();
        return $this->json($companies);
    }

    /**
     * @Route("/company", name="company", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = new Company($data['name'],$data['street'],$data['zipcode'],$data['city'],$data['picture']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($company);
        $entityManager->flush();
        return $this->json($data);
    }

    /**
     * @Route("/company/{id}", name="company_delete", methods={"DELETE"})
     */
    public function delete(CompanyRepository $companyRepository, $id)
    {
        $company = $companyRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($company);
        $entityManager->flush();       
        return $this->json("Company deleted");
    }


    /**
     * @Route("/company/edit/{id}", name="company_edit", methods={"PUT"})
     */
    public function edit(CompanyRepository $companyRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $company = $companyRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$company) {
            throw $this->createNotFoundException(
                'No company found for id '.$id
            );
        }      
        $company->setName($data['name']);
        $company->setStreet($data['street']);
        $company->setZipcode($data['zipcode']);
        $company->setCity($data['city']);
        $company->setPicture($data['picture']);
        $entityManager->flush();
        return $this->json("Company updated");
    }


}
