<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            //va générer la liste déroulante avec toutes les catégories
            ->add('category', EntityType::class,
                    //permet de dire sur quelle entité on va se baser
                    array('class' => Category::class,
                        //quelle propriété de l'entité doit être affichée dans la liste déroulante
                            'choice_label' => 'libelle'
                        )
                    )
            ->add('image', FileType::class, 
                array('label' => 'ajoutez une image svp',
                        'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //on va pouvoir hériter de ce formulaire
            'inherit_data' => true,
        ]);
    }
}
