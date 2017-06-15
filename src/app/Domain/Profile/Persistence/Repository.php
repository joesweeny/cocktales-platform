<?php

namespace Cocktales\Domain\Profile\Persistence;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Inserts a new profile record into the database
     *
     * @param Profile $profile
     * @return Profile
     */
    public function createProfile(Profile $profile): Profile;

    /**
     * Retrieve a profile from the database by associated User ID
     *
     * @param Uuid $id
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getProfileByUserId(Uuid $id): Profile;

    /**
     * Retrieves a user by their username
     *
     * @param string $username
     * @return Profile
     * @throws NotFoundException
     */
    public function getProfileByUsername(string $username): Profile;

    /**
     * Update a profile record in the database
     *
     * @param Profile $profile
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function updateProfile(Profile $profile): Profile;
}
