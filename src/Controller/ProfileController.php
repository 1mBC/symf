<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\ProfileType;
use App\Repository\AccountRepository;
use App\Repository\ChoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
 

    #[Route('/', name: 'app_profile_default', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_profile_edit', ['secction' => 1], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{section}', name: 'app_profile_edit', methods: ['GET'])]
    public function edit(int $section = 1, Request $request, AccountRepository $accountRepository): Response
    {
        $account_id = $this->getUser()->getAccount()->getId();
        $account = $accountRepository->find($account_id);
        $form = $this->createForm(ProfileType::class, $account, ['account_id' => $account_id, 'section' => $section]);


        return $this->render('profile/index.html.twig', [
            'account' => $account,
            'form' => $form,
            'section' => $section,
        ]);

    }



}
