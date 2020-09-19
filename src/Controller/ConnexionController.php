<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTManagement;
use Doctrine\ORM\EntityManagerInterface;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/connexion", name="")
 *
 * Les routes présentes dans ce fichier doivent être sous la forme
 * route : nomDuService
 * name : nomDuService
 *
 * Class ConnexionController
 */
class ConnexionController extends AbstractController
{
    /**
     * @Route("/cas", name="cas")
     *
     * @return JsonResponse
     */
    public function cas(EntityManagerInterface $entityManager, JWTManagement $JWTManagement)
    {
        phpCAS::client(
            CAS_VERSION_2_0,
            'cas.utt.fr',
            443,
            '/cas'
        );
        phpCAS::setNoCasServerValidation(); //TODO: changer et rajouter le certificat
        phpCAS::forceAuthentication();
        if (phpCAS::getUser()) {
            if (!$entityManager->getRepository(User::class)->findOneBy(['casUid' => phpCAS::getUser()])) {
                $user = new User();
                $user->setCasUid(phpCAS::getUser());
                $entityManager->persist($user);
                $entityManager->flush();
            } else {
                $user = $entityManager->getRepository(User::class)->findOneBy(['casUid' => phpCAS::getUser()]);
            }

            $data = [
                'jwt' => $JWTManagement->getJWTFromUser($user),
            ];

            //TODO : return on front with parameter ?
            return new JsonResponse($data, 200);
        }
    }
}
