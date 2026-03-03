<?php

namespace App\Form;

use App\Entity\Mouvementstock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MouvementstockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Entrée' => Mouvementstock::ENTREE,
                    'Sortie' => Mouvementstock::SORTIE,
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ],
                'attr' => [
                    'min' => 1
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mouvementstock::class,
        ]);
    }
}
