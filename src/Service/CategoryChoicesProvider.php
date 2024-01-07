<?php

namespace PrestaShop\Module\categoryshowcasepro\Service;

use PrestaShop\Module\categoryshowcasepro\Repository\CategoryRepository;

class CategoryChoicesProvider
{
    private $categoryRepository;
    private $context;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->context = \Context::getContext();
    }
    public function getAllDisabledCategories()
    {
        return $this->categoryRepository->getAllDisabledCategories($this->context->language->id);
    }
}
