<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FournisseurController extends AbstractController
{
    #[Route('/suppliers', name: 'suppliers', methods: ['GET'])]
    public function index(FournisseurRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        return $this->render('supplier/index.html.twig', [
            'suppliers' => $repo->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/suppliers/new', name: 'suppliers_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $supplier = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($supplier);
            $em->flush();

            $this->addFlash('success', '✅ Fournisseur ajouté avec succès.');
            return $this->redirectToRoute('suppliers_new'); // rester sur le formulaire
        }

        return $this->render('supplier/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/suppliers/{id}/edit', name: 'suppliers_edit', methods: ['GET','POST'])]
    public function edit(Fournisseur $supplier, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FournisseurType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', '✅ Fournisseur modifié.');
            return $this->redirectToRoute('suppliers');
        }

        return $this->render('supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/suppliers/{id}', name: 'suppliers_delete', methods: ['POST'])]
    public function delete(Fournisseur $supplier, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete_supplier_'.$supplier->getId(), $request->request->get('_token'))) {
            $em->remove($supplier);
            $em->flush();
            $this->addFlash('success', '✅ Fournisseur supprimé.');
        }

        return $this->redirectToRoute('suppliers');
    }
}
