<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Region;
use App\Repository\RegionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RoomType extends AbstractType
{
    private $regionRepository;

    public function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $regions = $this->regionRepository->findAll();
        $builder
            ->add('summary', TextType::class)
            ->add('description', TextareaType::class)
            ->add('capacity', TextType::class)
            ->add('superficy', TextType::class)
            ->add('price', TextType::class)
            ->add('address', TextType::class)
            ->add('imageFile', VichImageType::class, ['required' => false, 'mapped' => false])
            ->add('submit', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
