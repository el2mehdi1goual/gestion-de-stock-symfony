<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{
    #[Route('/alerts', name: 'alerts')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $products = $produitRepository->findAlertProducts();
        
        
        return $this->render('alert/index.html.twig', [
            'products' => $products,
        ]);
    }
}
