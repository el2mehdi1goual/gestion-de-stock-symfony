<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\FournisseurRepository;
use App\Repository\MouvementstockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        ProduitRepository $produitRepo,
        FournisseurRepository $fournisseurRepo,
        MouvementStockRepository $mouvementRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $productCount = $produitRepo->count([]);
        $supplierCount = $fournisseurRepo->count([]);
        $movementCount = $mouvementRepo->count([]);

        // Alertes = produits où stockActuel <= stockMin
        $alertCount = $produitRepo->countLowStock();

        return $this->render('home/index.html.twig', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'movementCount' => $movementCount,
            'alertCount' => $alertCount,
        ]);
    }
  
#[Route('/stock', name: 'stock', methods: ['GET'])]
public function stockRedirect(): Response
{
    return $this->redirectToRoute('stock_history');
}

}

