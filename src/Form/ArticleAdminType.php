<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //je récupère les champs générés dans ArticleType
        //je n'ai pas besoin du use pour ArticleType car la classe est déclarée dans le même namespace
            ->add('article', ArticleType::class,
                        array('data_class' => Article::class)
                )
            //je rajoute ceux que je veux
            ->add('date_publi', DateTimeType::class)
            ->add('user', EntityType::class,
                        array('class' => User::class,
                                'choice_label' => 'username'
                        )
                )
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
