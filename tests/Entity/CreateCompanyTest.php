<?php

namespace App\Tests\Entity;

use App\Entity\Company;
use PHPUnit\Framework\TestCase;

class CreateCompanyTest extends TestCase
{
    public function testCreateCompanyShouldWork(): void
    {
        $name = 'Company name';
        $location = 'Company location';
        $contactInformation = 'Company contact information';

        $company = new Company();
        $company->setName($name);
        $company->setLocation($location);
        $company->setContactInformation($contactInformation);

        self::assertEquals($name, $company->getName());
        self::assertEquals($location, $company->getLocation());
        self::assertEquals($contactInformation, $company->getContactInformation());
    }
}
