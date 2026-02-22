<?php

namespace App\Controller;

use App\Entity\Mouvementstock;
use App\Entity\Produit;
use App\Form\MouvementstockType;
use App\Repository\MouvementstockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MouvementstockController extends AbstractController
{
    #[Route('/stock/{id}', name: 'stock_move', methods: ['GET', 'POST'])]
    public function move(
        ?Produit $produit,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $m = new Mouvementstock();
        $form = $this->createForm(MouvementstockType::class, $m);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

          
            $m->setProduit($produit);
            $m->setUser($this->getUser());

            $qte = (int) $m->getQuantite();

            if ($m->getType() === Mouvementstock::SORTIE) {

                
                if ($produit->getStockActuel() < $qte) {
                    $this->addFlash('error', 'Stock insuffisant ❌');
                    return $this->redirectToRoute('stock_move', ['id' => $produit->getId()]);
                }

                $produit->setStockActuel($produit->getStockActuel() - $qte);
            }

            if ($m->getType() === Mouvementstock::ENTREE) {
                $produit->setStockActuel($produit->getStockActuel() + $qte);
            }

            $em->persist($m);
            $em->flush();

            $this->addFlash('success', 'Mouvement enregistré ✅');
            return $this->redirectToRoute('products');
        }

        return $this->render('stock/move.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/stock/history/{id}', name: 'stock_history_product', methods: ['GET'])]
    public function historyByProduct(
        Produit $produit,
        MouvementstockRepository $repo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $mouvements = $repo->findBy(['produit' => $produit], ['date' => 'DESC']);

        return $this->render('stock/history_product.html.twig', [
            'produit' => $produit,
            'mouvements' => $mouvements,
        ]);
    }

    #[Route('/stock/history', name: 'stock_history', methods: ['GET'])]
    public function historyAll(MouvementstockRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $mouvements = $repo->findBy([], ['date' => 'DESC']);

        return $this->render('stock/history.html.twig', [
            'mouvements' => $mouvements,
        ]);
    }
}
