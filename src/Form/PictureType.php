<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AppType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, $this->getConfig("", "Url de la photo"))
            ->add('caption', TextType::class, $this->getConfig("", "Titre de la photo"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }

}
