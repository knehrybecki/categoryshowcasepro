<?php

namespace PrestaShop\Module\categoryshowcasepro\Controller\Admin;

use ModuleAdminControllerCore;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\Module\categoryshowcasepro\Form\ConfigurationFormType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class AdminConfigController extends FrameworkBundleAdminController
{

    public function index(Request $request)
    {
        $datas = [
            'WishlistPageName' => 'asd',
            'WishlistDefaultTitle' => 'as',
            'CreateButtonLabel' => 'asd',
        ];
        $form = $this->createForm(ConfigurationFormType::class, $datas);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $categorySelected = json_encode($formData['categories']);

                \Configuration::updateValue('CATEGORY_SHOWCASE_NB', $formData['product_limit']);
                \Configuration::updateValue('CATEGORY_SHOWCASE_CAT', $categorySelected);

                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('categoryshowcasepro_configuration');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->render(
            '@Modules/categoryshowcasepro/views/templates/admin/configure.html.twig',
            [
                'form' => $form->createView(),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminConfigController', 'asd'),
                'layoutTitle' => $this->trans('Konfiguracja', 'Modules.Mimodulo.Admin'),

            ],

        );
    }
}
