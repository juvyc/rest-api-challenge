<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Customers;

final class SourceCrawlerTest extends TestCase
{

    private $testData = '{"results":[{"gender":"male","name":{"title":"Mr","first":"Bryan","last":"Morrison"},"location":{"street":{"number":4424,"name":"Groveland Terrace"},"city":"Sunshine Coast","state":"South Australia","country":"Australia","postcode":5449,"coordinates":{"latitude":"-76.4046","longitude":"-64.4075"},"timezone":{"offset":"-4:00","description":"Atlantic Time (Canada), Caracas, La Paz"}},"email":"bryan.morrison@example.com","login":{"uuid":"2a475c88-6bf7-4af6-b38f-e85a2b6cb189","username":"orangewolf923","password":"cloud9","salt":"zR9EQ9y9","md5":"a253e860769abd284fbe9d7b020c72df","sha1":"ff498fbef60f0bd3cf04563bf9eabe36127d98f4","sha256":"cf177ee6b7d3df98ea1f90083cbcb9e0a9de9344e0e36888cf229c4a466ffefc"},"dob":{"date":"1974-01-23T12:49:23.862Z","age":50},"registered":{"date":"2008-07-10T23:13:03.951Z","age":16},"phone":"01-7477-0562","cell":"0499-803-665","id":{"name":"TFN","value":"937528544"},"picture":{"large":"https://randomuser.me/api/portraits/men/67.jpg","medium":"https://randomuser.me/api/portraits/med/men/67.jpg","thumbnail":"https://randomuser.me/api/portraits/thumb/men/67.jpg"},"nat":"AU"}],"info":{"seed":"e7f4d42a0ec735cd","results":1,"page":1,"version":"1.4"}}';

    public function testInsertingCustomer() : void
    {
        $_r = $this->getData();

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

        $customer = new Customers();

        $password = $this->hashPassword($_data['password']);

        $customer->setFullName($_data['full_name'] ?? '');
        $customer->setEmail($_data['email'] ?? '');
        $customer->setCountry($_data['country'] ?? '');

        $customer->setUsername($_data['username'] ?? '');
        $customer->setPassword($password);

        $customer->setGender($_data['gender'] ?? '');
        $customer->setCity($_data['city'] ?? '');
        $customer->setPhone($_data['phone'] ?? '');


        $this->assertSame('Bryan Morrison', $customer->getFullName());
        $this->assertSame('test@testing.com', $customer->getEmail());
        $this->assertSame('Australia', $customer->getCountry());
        $this->assertSame('Male', $customer->getGender());
        $this->assertSame('Sunshine Coast', $customer->getCity());
    }

    private function getData()
    {
        $_d = json_decode($this->testData, true);
        return (!empty($_d['results'])) ? $_d['results'][0] : [];
    }

    private function hashPassword($_pw)
    {
        return md5($_pw);
    }
}