<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\FournisseurRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/products', name: 'products', methods: ['GET'])]
    public function index(
        Request $request,
        ProduitRepository $repo,
        FournisseurRepository $fournisseurRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        
        $mode = $request->query->get('mode');
        $stockMode = ($mode === 'stock');

        
        $q = trim((string) $request->query->get('q', ''));
        $supplierId = $request->query->get('supplier');
        $low = $request->query->get('low') === '1';

        
        $qb = $repo->createQueryBuilder('p')
            ->leftJoin('p.fournisseur', 'f')
            ->addSelect('f')
            ->orderBy('p.id', 'DESC');

        if ($q !== '') {
            $qb->andWhere('p.nom LIKE :q OR p.description LIKE :q OR f.nom LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if (!empty($supplierId)) {
            $qb->andWhere('f.id = :sid')
               ->setParameter('sid', (int) $supplierId);
        }

        if ($low) {
            $qb->andWhere('p.stockActuel <= p.stockMin');
        }

        $products = $qb->getQuery()->getResult();
        $suppliers = $fournisseurRepo->findBy([], ['nom' => 'ASC']);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'suppliers' => $suppliers,

            // filtres
            'q' => $q,
            'supplierId' => $supplierId ? (int) $supplierId : null,
            'low' => $low,

            // mode stock
            'stockMode' => $stockMode,
        ]);
    }

    #[Route('/products/new', name: 'products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Produit();
        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès ✅');
            return $this->redirectToRoute('products_new');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products/{id}/edit', name: 'products_edit', methods: ['GET', 'POST'])]
    public function edit(Produit $product, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ProduitType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Produit modifié avec succès ✅');
            return $this->redirectToRoute('products');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/products/{id}', name: 'products_delete', methods: ['POST'])]
    public function delete(Produit $product, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete_product_' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé ✅');
        }

        return $this->redirectToRoute('products');
    }
}
    

