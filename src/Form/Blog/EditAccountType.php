<?php

namespace App\Form\Blog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EditAccountType extends AbstractType
{
    const BLOCK_PREFIX = 'app_edit_account';
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('file', FileType::class, [
            'label' => 'Upload Image'
        ]);
        $builder->add('name', TextType::class);
        $builder->add('surname', TextType::class);
        $builder->add('email', EmailType::class);
        $builder->add('password', TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }

    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
