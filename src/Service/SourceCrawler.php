<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Customers;

class SourceCrawler
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getData($page = 1, $num_results = 20, $nat='au') : array
    {
        $client = HttpClient::create();

        $response = $client->request(
            'GET',
            'https://randomuser.me/api/?page='. $page .'&results='. $num_results .'&nat=' . $nat,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        return $response->toArray();
    }

    public function syncer($page = 1, $num_results = 20, $nat='au') : string
    {
        try {

            $_success = 0;
            $_fails = 0;
            $_updated = 0;

            $_q = $this->getData($page, $num_results, $nat);

            $_list_results = $_q['results'] ?? [];

            //return json_encode($_list_results);
            
            if(empty($_list_results)){
                return json_encode([
                    'ERROR' => 'No records found!'
                ]);
            }

            foreach($_list_results as $_r){
                
                $getCustomer = $this->doctrine
                    ->getRepository(Customers::class)
                    ->findOneBy(['email' => $_r['email']]);
                
                
                $_data = [
                    'full_name' => $_r['name']['first'] . ' ' . $_r['name']['last'],
                    'email' => $_r['email'],
                    'country' => $_r['location']['country'],

                    'username' => $_r['login']['username'],
                    'password' => $_r['login']['password'],

                    'gender' => $_r['gender'],
                    'city' => $_r['location']['city'],
                    'phone' => $_r['phone'],
                ];

                if(empty($getCustomer)){
                    //Insert if customer is not exist
                    $isInserted = $this->upsert('insert', $_data);
                    if($isInserted)  $_success++; else $_fails++;
                }else{
                    //Once exist do update
                    $isUpdated = $this->upsert('update', $_data, $getCustomer->getId());
                    if($isUpdated) $_updated++;
                }
            }

            return json_encode([
                'success' => $_success,
                'failed' => $_fails,
                'updated' => $_updated,
            ]);

        } catch (\Exception $ex) {
            return json_encode([
                'ERROR' => $ex
            ]);
        }
    }

    private function hashPassword($_pw) : string
    {
        return md5($_pw);
    }

    //Update and insert data
    private function upsert($type = 'insert', $_data = [], $id=0) : int
    {
        $entityManager = $this->doctrine->getManager();
        
        if(!empty($_data)){
            if(empty($_data['email']) || empty($_data['full_name'])){
                return 0;
            }

            if($type == 'insert'){
                $customer = new Customers();
            }else{
                $customer = $entityManager->getRepository(Customers::class)->find($id);
            }

            $password = $_data['password'] ?? '';
            
            //Encrypt password using MD5
            $password = $this->hashPassword($password);

            
            $customer->setFullName($_data['full_name'] ?? '');
            $customer->setEmail($_data['email'] ?? '');
            $customer->setCountry($_data['country'] ?? '');

            $customer->setUsername($_data['username'] ?? '');
            $customer->setPassword($password);

            $customer->setGender($_data['gender'] ?? '');
            $customer->setCity($_data['city'] ?? '');
            $customer->setPhone($_data['phone'] ?? '');

            if($type == 'insert'){
                $entityManager->persist($customer);
            }

            $entityManager->flush();

            return $customer->getId();
        }
    }
}