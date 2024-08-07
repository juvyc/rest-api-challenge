<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Customers;
use App\Service\SourceCrawler;


class CustomersController extends AbstractController
{
    //Get all customers data
    #[Route('/customers', name: 'customers_index')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $customers = $doctrine
            ->getRepository(Customers::class)
            ->findAll();
   
        $data = [];
   
        foreach ($customers as $customer) {
           $data[] = [
               'full_name' => $customer->getFullName(),
               'email' => $customer->getEmail(),
               'country' => $customer->getCountry(),
           ];
        }
   
        return $this->json($data);
    }

    //Show customer individual data
    #[Route('/customers/{id}', name: 'customers_view', methods: ['get'])]
    public function view(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $customer = $doctrine
            ->getRepository(Customers::class)
            ->find($id);
        
        if (!$customer) {
            return $this->json('No customer found for id ' . $id, 404);
        }
   
        $data = [
            'full_name' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'username' => $customer->getUsername(),
            'gender' => $customer->getGender(),
            'country' => $customer->getCountry(),
            'city' => $customer->getCity(),
            'phone' => $customer->getPhone(),
        ];

        return $this->json($data);
    }
}
