<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AppType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfig("Date de début", "", [
                'widget' => 'single_text'
            ]))
            ->add('endDate', DateType::class, $this->getConfig("Date de fin", "", [
                'widget' => 'single_text'
            ]))
            ->add('comment', TextAreaType::class, $this->getConfig("Commentaire", ""))
            ->add('booker', EntityType::class, $this->getConfig("Liste des Utilisateurs", "", [
                'class' => User::class,
                'choice_label' => function($user) {
                    /** @var User $user */
                    return $user->getFirstName() . " " . strtoupper($user->getLastName());
                }
            ]))
            ->add('ad', EntityType::class, $this->getConfig("Liste des Annonces", "", [
                'class' => Ad::class,
                'choice_label' => 'title'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
