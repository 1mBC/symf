<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Criterion;
use App\Form\ProfileType;
use App\Repository\AccountRepository;
use App\Form\CriterionType;
use App\Repository\CriterionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Repository\ChoiceRepository;


#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
 

    #[Route('/', name: 'app_profile_default', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_profile_edit', ['section' => 1], Response::HTTP_SEE_OTHER);
    }





    #[Route('/cheat', name: 'app_profile_cheat', methods: ['GET'])]
    public function cheat(ChoiceRepository $choiceRepository, EntityManagerInterface $entityManager): Response
    {
        $allChoices = $choiceRepository->findAll();
        $account = $this->getUser()->getAccount();
        foreach($allChoices as $choice){
            $account->addChoice($choice);
        }

        $entityManager->persist($account);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile_edit', ['section' => 1], Response::HTTP_SEE_OTHER);        

    }




    #[Route('/{section}', name: 'app_profile_edit', methods: ['GET'])]
    public function edit(int $section, AccountRepository $accountRepository, CriterionRepository $criterionRepository): Response
    {
        $account = $this->getUser()->getAccount();
        $criteria = $criterionRepository->findBy(['section' => $section]);
        $choices = $account->getChoices();

        $form = $this->createForm(ProfileType::class, $account, ['user_account_id' => $account->getId(), 'criteria' => $criteria, 'user_choices' => $choices]);


        return $this->render('profile/index.html.twig', [
            'account' => $account,
            'form' => $form,
            'section' => $section,
        ]);

    }


    #[Route('/criterion/{id}', name: 'app_profile_criterion', methods: ['GET'])]
    public function criterion(Criterion $criterion): Response
    {

       $form = $this->createForm(CriterionType::class, $criterion, ['account_id' => 6]);


        return $this->render('profile/index.html.twig', [
            'form' => $form,
            'section' => 1,
        ]);

    }



}
