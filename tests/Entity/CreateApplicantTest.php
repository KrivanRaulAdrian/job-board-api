<?php

namespace App\Tests\Entity;

use App\Entity\Applicant;
use PHPUnit\Framework\TestCase;

class CreateApplicantTest extends TestCase
{
    public function testCreateApplicantShouldWork(): void
    {
        $name = 'Applicant name';
        $contactInformation = 'Applicant contact information';
        $jobPreferences = 'Applicant job preferences';

        $applicant = new Applicant();
        $applicant->setName($name);
        $applicant->setContactInformation($contactInformation);
        $applicant->setJobPreferences($jobPreferences);

        self::assertEquals($name, $applicant->getName());
        self::assertEquals($contactInformation, $applicant->getContactInformation());
        self::assertEquals($jobPreferences, $applicant->getJobPreferences());
    }
}
