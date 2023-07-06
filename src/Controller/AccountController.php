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
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/account')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account_default')]
    public function index(AccountRepository $accountRepository): Response
    {
        return $this->redirectToRoute('app_account_infos', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/infos', name: 'app_account_infos')]
    public function access(Request $request, AccountRepository $accountRepository): Response
    {
        $account = $this->getUser()->getAccount();
        $choices = $account->getChoices();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountRepository->save($account, true);

            return $this->redirectToRoute('app_account_infos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/infos.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }



    #[Route('/delete/{id}', name: 'app_account_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->request->get('_token'))) {
            $accountRepository->remove($account, true);
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
