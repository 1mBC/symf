<?php

namespace App\DataFixtures\Providers;
 
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 
class EncodePasswordProvider
{
    private $encoder;
 
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
 
    public function encodePassword(string $plainPassword): string
    {
        return $this->encoder->encodePassword(new User(), $plainPassword);
    }
}