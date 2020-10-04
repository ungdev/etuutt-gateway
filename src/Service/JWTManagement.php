<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Jose\Component\Core\JWK;
use Jose\Easy\Build;
use Jose\Easy\Load;

class JWTManagement
{
    //TODO : use jti ?
    //TODO : check issuer and audience
    //TODO : check best algorithm

    private $jwk;

    private $userRepository;

    private $frontBaseUrl;

    public function __construct(string $jwkAsJson, UserRepository $userRepository, string $frontBaseUrl)
    {
        $this->jwk = JWK::createFromJson($jwkAsJson);
        $this->userRepository = $userRepository;
        $this->frontBaseUrl = $frontBaseUrl;
    }

    public function getJWTFromUser(User $user): string
    {
        $time = time();

        return Build::jws()
            ->exp($time + 3600) // The "exp" claim
            ->iat($time) // The "iat" claim
            ->nbf($time) // The "nbf" claim
            //->jti('0123456789', true) // The "jti" claim.
            // The second argument indicate this pair shall be duplicated in the header
            ->alg('RS512') // The signature algorithm. A string or an algorithm class.
            ->iss('etuutt-gateway') // The "iss" claim
            ->aud('etuutt-front') // Add an audience ("aud" claim)
            ->sub('user-token') // The "sub" claim
            ->claim('id', $user->getId())
            ->claim('userId', $user->getUserId())
            //->header('prefs', ['field1', 'field7'])
            ->sign($this->jwk) // Compute the token with the given JWK
            ;
    }

    public function getUserFromJWT(string $token): ?User
    {
        try {
            $jwt = Load::jws($token)
                ->algs(['RS512']) // The algorithms allowed to be used
                ->exp() // We check the "exp" claim
                ->iat(1000) // We check the "iat" claim. Leeway is 1000ms (1s)
                ->nbf() // We check the "nbf" claim
                ->aud('etuutt-front') // Allowed audience
                ->iss('etuutt-gateway') // Allowed issuer
                ->sub('user-token') // Allowed subject
                //->jti('0123456789') // Token ID
                ->key($this->jwk) // Key used to verify the signature
                ->run() // Go!
            ;
        } catch (Exception $exception) {
            return null;
        }

        return $this->userRepository->find($jwt->claims->get('id'));
    }

    public function getFrontURLFromUser(User $user, $baseUrl = '')
    {
        $data = [
            'jwt' => $this->getJWTFromUser($user),
        ];

        if ('' === $baseUrl) {
            return $this->frontBaseUrl.'?'.http_build_query($data);
        }

        return $baseUrl.'?'.http_build_query($data);
    }
}
