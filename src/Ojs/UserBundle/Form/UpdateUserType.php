<?php

namespace Ojs\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateUserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('firstName')
            ->add('lastName')

            ->add('subjects', 'entity', array(
                'class' => 'Ojs\JournalBundle\Entity\Subject',
                'property' => 'subject',
                'multiple' => true,
                'expanded' => false,
                'attr' => array('class' => 'select2-element', 'style' => 'width:100%'),
                'required'=>false
            ))
            ->add('avatar','hidden')
            ->add('header','hidden')
            ->add('privacy','checkbox',[
                'label'=>'Hide my account',
                'required'=>false
            ])
//                ->add('journals', 'entity', array(
//                    'class' => 'Ojs\JournalBundle\Entity\Journal',
//                    'property' => 'title',
//                    'multiple' => true,
//                    'expanded' => false,
//                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ojs\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ojs_userbundle_updateuser';
    }

}
