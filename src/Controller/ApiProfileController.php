<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\AccountRepository;
use App\Repository\ChoiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ApiProfileController extends AbstractController
{
    #[Route('/onflyupdate', name: 'profile_onflyupdate', methods: ['POST'], options: ['expose' => true])]
    public function onflyupdate(Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository, ChoiceRepository $choiceRepository): Response
    {
        $accountId = $request->request->get('account');
        $choiceId = $request->request->get('choice');
        $checked = $request->request->get('checked');
    
        $account = $accountRepository->find($accountId);
        $choice = $choiceRepository->find($choiceId);
    
        if ($checked) {
            $choice->addAccount($account);
        } else {
            $choice->removeAccount($account);
        }
    
        $entityManager->persist($choice);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
    
    #[Route('/onflyupdate2', name: 'profile_onflyupdate2', methods: ['POST'], options: ['expose' => true])]
    public function onflyupdate2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $accountId = $request->request->get('account');
        $choiceId = $request->request->get('choice');
        $checked = $request->request->get('checked');
    
        $db = $entityManager->getConnection();
    
        if ($checked) {
            $query = '
                INSERT INTO account_choice (account_id, choice_id) VALUES (:account_id, :choice_id)
            ';
        } else {
            $query = '
                DELETE FROM account_choice WHERE choice_id = :choice_id AND account_id = :account_id
            ';
        }
        
        $stmt = $db->prepare($query);
        $stmt->executeQuery(['account_id' => $accountId, 'choice_id' => $choiceId]);
        
        return new JsonResponse(['success' => true]);
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


    #[Route('/cheatSQL', name: 'app_profile_cheatSQL', methods: ['GET'])]
    public function cheatSQL(ChoiceRepository $choiceRepository, EntityManagerInterface $entityManager): Response
    {
        $allChoices = $choiceRepository->findAll();
        $accountId = $this->getUser()->getAccount()->getId();

        $db = $entityManager->getConnection();

        $query = '
        INSERT INTO account_choice (account_id, choice_id) VALUES (:account_id, :choice_id)
        ';  
        foreach($allChoices as $choice)
        {
            $choiceId = $choice->getId();
            $stmt = $db->prepare($query);
            $stmt->executeQuery(['account_id' => $accountId, 'choice_id' => $choiceId]);
        }


        return $this->redirectToRoute('app_profile_edit', ['section' => 1], Response::HTTP_SEE_OTHER);        

    }

}