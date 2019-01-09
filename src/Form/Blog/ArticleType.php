<?php

namespace App\Form\Blog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    const BLOCK_PREFIX = 'app_add_article';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'label' => 'Upload Image'
        ]);
        $builder->add('title', TextType::class, [
            'label' => 'Title'
        ]);
        $builder->add('author', TextType::class, [
            'label' => 'Author'
        ]);
        $builder->add('blog', TextareaType::class, [
            'label' => 'Article'
        ]);
        $builder->add('tags', TextType::class, [
            'label' => 'Tags'
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Blog'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_add_article';
    }
}