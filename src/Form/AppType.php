<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;


class AppType extends AbstractType
{
    /**
     * getConfig : Configuration label et attr
     * @param $label
     * @param $placeholder
     * @return array
     */
    protected function getConfig($label, $placeholder)
    {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ];
    }

}
