<?php
class categoryshowcaseproAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('action') == 'getMorePage') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $module = Module::getInstanceByName('categoryshowcasepro');

            $products = $module->getProducts($data['page'], $data['category']);

            header('Content-Type: application/json');
            echo json_encode($products);
        }

        exit;
    }
}
