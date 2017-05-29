<?php

namespace Perform\BaseBundle\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Perform\BaseBundle\Form\Type\DatePickerType;

/**
 * Use the ``date`` type for ``date`` doctrine fields.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class DateType extends DateTimeType
{
    public function getDefaultConfig()
    {
        return [
            'options' => [
                'format' => 'd/m/Y',
                'human' => false,
            ],
        ];
    }

    public function createContext(FormBuilderInterface $builder, $field, array $options = [])
    {
        $builder->add($field, DatePickerType::class, [
            'format' => 'dd/MM/y',
            'datepicker_format' => 'DD/MM/YYYY',
        ]);
    }
}
