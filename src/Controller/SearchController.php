<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Matching;

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/search')]
#[IsGranted('ROLE_USER')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search')]
    public function search(Matching $matching): Response
    {
        $user_account_id = $this->getUser()->getAccount()->getId();
        $matches = $matching->getMatches($user_account_id);


        return $this->render('search/index.html.twig', [
            'matches' => $matches,
        ]);
    }
}
