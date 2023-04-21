<?php

namespace App\Tests\Entity;

use App\Entity\Job;
use PHPUnit\Framework\TestCase;

class CreateJobTest extends TestCase
{
    public function testCreateJobShouldWork(): void
    {
        $title = 'Job title';
        $description = 'Job description';
        $requiredSkills = 'Job required skills';
        $experience = 'Job experience';

        $job = new Job();
        $job->setTitle($title);
        $job->setDescription($description);
        $job->setRequiredSkills($requiredSkills);
        $job->setExperience($experience);

        self::assertEquals($title, $job->getTitle());
        self::assertEquals($description, $job->getDescription());
        self::assertEquals($requiredSkills, $job->getRequiredSkills());
        self::assertEquals($experience, $job->getExperience());
    }
}
