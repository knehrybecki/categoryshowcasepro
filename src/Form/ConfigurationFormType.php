<?php

namespace PrestaShop\Module\categoryshowcasepro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShop\Module\categoryshowcasepro\Service\CategoryChoicesProvider;

class ConfigurationFormType extends AbstractType
{
    private $categoryChoices;

    public function __construct(CategoryChoicesProvider $categoryChoices)
    {
        $this->categoryChoices = $categoryChoices;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $categorySelected = \Configuration::get('CATEGORY_SHOWCASE_CAT');
        $categorySelected = json_decode($categorySelected);
        $categoryLimit = \Configuration::get('CATEGORY_SHOWCASE_NB');

        $builder
            ->add('categories', CategoryChoiceTreeType::class, [
                'label' => 'Wybierz kategorie',
                'multiple' => true,
                'action' => true,
                'disabled_values' => $this->categoryChoices->getAllDisabledCategories(),
                'data' => $categorySelected,

            ])
            ->add('product_limit', IntegerType::class, [
                'label' => 'Limit produktów w kategorii',
                'attr' => ['class' => 'fixed-width-xs'],
                'data' => $categoryLimit,
                'required' => true
            ]);
    }
}
