<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
              'constraints' => [
                new NotBlank(),
                new Length(['min' => 3, 'max' => 50])
              ]
            ])
            ->add('image', FileType::class, [
              'required' => false,
              'attr' => [
                'accept' => "image/jpg, image/jpeg, image/png"
              ],
              'constraints' => [
                new Image([
                  'mimeTypes' => [
                    'image/jpg',
                    'image/jpeg',
                    'image/png',
                  ],
                  'mimeTypesMessage' => 'Please upload a JPG or PNG',
                ])
              ]
            ])
            ->add('description', TextareaType::class, [
              'constraints' => [
                new NotBlank(),
                new Length(['min' => 3])
              ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
