<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use App\Repository\ChoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account_index', methods: ['GET'])]
    public function index(AccountRepository $accountRepository): Response
    {
        return $this->render('account/index.html.twig', [
            'accounts' => $accountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AccountRepository $accountRepository): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account, ['create' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountRepository->save($account, true);

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/onflyupdate', name: 'account_onflyupdate', methods: ['POST'], options: ['expose' => true])]
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
    
    #[Route('/onflyupdate2', name: 'account_onflyupdate2', methods: ['POST'], options: ['expose' => true])]
    public function onflyupdate2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $accountId = $request->request->get('account');
        $choiceId = $request->request->get('choice');
        $checked = $request->request->get('checked');
    
        $db = $entityManager->getConnection();
    
        if ($checked) {
            $query = '
                INSERT INTO choice_account (choice_id, account_id) VALUES (:choice_id, :account_id)
            ';
        } else {
            $query = '
                DELETE FROM choice_account WHERE choice_id = :choice_id AND account_id = :account_id
            ';
        }
        
        $stmt = $db->prepare($query);
        $stmt->executeQuery(['choice_id' => $choiceId, 'account_id' => $accountId]);
        
        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}', name: 'app_account_show', methods: ['GET'])]
    public function show(Account $account): Response
    {
        return $this->render('account/show.html.twig', [
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit/{section}', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(int $section, Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        //récupère le account qui est en DB
        $form = $this->createForm(AccountType::class, $account, ['section' => $section]);
        //remove account de tous les account/choices
        //si je ne le fais pas maintenant après ce n'est plus possible
        //car handlerequest retire les choices qui ont besoin de prendre 
        //un remove account sans leur mettre de remove account ... pfff
        foreach($account->getChoices() as $oldChoice)
        { 
            if($oldChoice->getCriterion()->getSection() == $section)
            {
                $oldChoice->removeAccount($account);
            }
        }
 
        //met à jour le account avec les datas du form 
        // /!\ sans passer par add/removeChoice ... pfff
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($account->getChoices() as $choice){
                $choice->addAccount($account);
            }

            $entityManager->persist($account);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_account_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
            'account_id' => $account->getId(),
            'section' => $section,
        ]);
    }

    #[Route('/{id}', name: 'app_account_delete', methods: ['POST'])]
    public function delete(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->request->get('_token'))) {
            $accountRepository->remove($account, true);
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
