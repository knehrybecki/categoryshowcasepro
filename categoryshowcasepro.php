<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\Module\categoryshowcasepro\Install\Installer;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class CategoryShowcasePro extends Module
{
    public function __construct()
    {
        $this->name = 'categoryshowcasepro';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Kamil Nehrybecki';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans('Category Showcase Pro', [], 'Modules.CategoryShowcasePro.Admin');
        $this->description = $this->trans(
            'show products in category box',
            [],
            'Modules.CategoryShowcasePro.Admin'
        );
        $this->ps_versions_compliancy = ['min' => '8.0', 'max' => _PS_VERSION_];
    }


    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Installer();

        return $installer->install($this);
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $installer = new Installer();

        return $installer->uninstall($this);
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            SymfonyContainer::getInstance()->get('router')->generate('categoryshowcasepro_configuration')
        );
    }
    public function hookActionFrontControllerSetMedia()
    {
        if (Tools::getValue('controller') === 'index') {
            $this->context->controller->addCss($this->getPathUri() . 'views/css/categoryshowcaseproHome.css');

            $this->context->controller->addJs($this->getPathUri() . 'views/js/categoryshowcaseproHome.js');
            Media::addJsDef([
                'ajaxMoreProductsCategoryShowcasePro' => $this->context->link->getModuleLink('categoryshowcasepro', 'ajax', ["action" => "getMorePage"]),
            ]);
        }
    }
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') === 'AdminConfigController') {
            $this->context->controller->addJs($this->getPathUri() . 'views/js/categoryshowcaseproAdmin.js');
        }
    }
    public function hookDisplayHome($params)
    {
        $products = $this->getProducts($page = 1, $category = null);

        foreach ($products as $product) {
            $link = new Link();
            $category_link = $link->getCategoryLink($product['category']);
            $product['category']->category_link = $category_link;
        }

        $this->context->smarty->assign([
            'products' => $products,
        ]);

        return $this->fetch('module:categoryshowcasepro/views/templates/hook/categoryShowCaseProFront.tpl');
    }
    public function getProducts($page, $categoryIdShoMore)
    {
        $categorySelected = \Configuration::get('CATEGORY_SHOWCASE_CAT');
        $categorySelected = json_decode($categorySelected);
        $categoryLimit = \Configuration::get('CATEGORY_SHOWCASE_NB');
        $pageFirst = 1;
        $categoriesWithProducts = [];

        if ($categoryIdShoMore > 0) {
            $categorySelected = [$categoryIdShoMore];
        }

        foreach ($categorySelected as $categoryId) {
            $category = new Category($categoryId);
            $searchProvider = new CategoryProductSearchProvider(
                $this->context->getTranslator(),
                $category
            );
            $context = new ProductSearchContext($this->context);

            $query = new ProductSearchQuery();

            if ($categoryLimit <= 10) {
                $query->setResultsPerPage($categoryLimit)->setPage($pageFirst);
            } else {
                $resultsPerPage = min(10, $categoryLimit);

                $totalPages = ceil($categoryLimit / $resultsPerPage);

                if ($page >= 1 && $page <= $totalPages) {
                    if ($page == $totalPages) {
                        $productsOnLastPage = $categoryLimit % $resultsPerPage;
                        $resultsPerPage = $productsOnLastPage > 0 ? $productsOnLastPage : $resultsPerPage;
                    }
                    $query->setResultsPerPage($resultsPerPage)->setPage($page);
                } else {
                    return [];
                }
            }

            $result = $searchProvider->runQuery($context, $query);

            $assembler = new ProductAssembler($this->context);

            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = $presenterFactory->getPresenter();

            $productsForTemplate = [];

            foreach ($result->getProducts() as $rawProduct) {
                if ($rawProduct['quantity'] > 0) {
                    $productsForTemplate[] = $presenter->present(
                        $presentationSettings,
                        $assembler->assembleProduct($rawProduct),
                        $this->context->language
                    );
                }
            }

            if (!empty($productsForTemplate)) {
                $categoriesWithProducts[] = [
                    'category' => $category,
                    'products' => $productsForTemplate
                ];
            }
        }

        return $categoriesWithProducts;
    }
}
