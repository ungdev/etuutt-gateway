<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LdapUNGType;
use App\Kernel;
use App\Service\JWTManagement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpCAS;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Ldap;
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
     * @return RedirectResponse
     */
    public function cas(EntityManagerInterface $entityManager, JWTManagement $JWTManagement, Kernel $kernel)
    {
        phpCAS::client(
            CAS_VERSION_2_0,
            'cas.utt.fr',
            443,
            '/cas'
        );
        phpCAS::setCasServerCACert(
            $kernel->getProjectDir().'/config/certificates/chain-cas-utt.pem',
            true
        );
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

            return $this->redirect($JWTManagement->getFrontURLFromUser($user));
        }

        throw $this->createAccessDeniedException('Auth cas failed !');
    }

    /**
     * @Route("/ldapUNG", name="ldapUNG")
     */
    public function ldapUNG(Request $request, EntityManagerInterface $entityManager, JWTManagement $JWTManagement)
    {
        $form = $this->createForm(LdapUNGType::class);
        //$form->handleRequest($request);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $ldap = Ldap::create('ext_ldap', [
                'connection_string' => 'ldap://'.$this->getParameter('UNG_LDAP_HOST').':389',
            ]);

            try {
                $ldap->bind(
                    'uid='.$form->get('username')->getData().',cn=users,cn=accounts,dc=uttnetgroup,dc=net',
                    $form->get('password')->getData()
                );
            } catch (Exception $exception) {
                echo $exception->getMessage();
                echo 'uid='.$form->get('username')->getData().',cn=users,cn=accounts,dc=uttnetgroup,dc=net\n';
                echo $form->get('password')->getData();

                throw $this->createAccessDeniedException('Auth ldap ung failed');
            }

            if (!$entityManager->getRepository(User::class)
                ->findOneBy(['ldapUNGUid' => $form->get('username')->getData()])) {
                throw $this->createNotFoundException('User not found');
            }
            $user = $entityManager->getRepository(User::class)
                ->findOneBy(['ldapUNGUid' => $form->get('username')->getData()])
                ;

            return $this->redirect($JWTManagement->getFrontURLFromUser($user));
        }

        return new Response('No username or password submitted', 400);
    }
}
