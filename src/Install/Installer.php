<?php

declare(strict_types=1);

namespace PrestaShop\Module\categoryshowcasepro\Install;

class Installer
{
    const HOOKS = [
        'actionFrontControllerSetMedia',
        'actionAdminControllerSetMedia',
        'displayHome'
    ];
    public function install($module)
    {
        $categorySelected = json_encode([]);
        \Configuration::updateValue('CATEGORY_SHOWCASE_NB', 10);
        \Configuration::updateValue('CATEGORY_SHOWCASE_CAT', $categorySelected);

        return $this->registerHooks($module);
    }

    /**
     * @param $module
     *
     * @return bool
     */
    public function uninstall($module)
    {
        return true;
    }
    /**
     * @param $module
     *
     * @return bool
     */
    private function registerHooks($module)
    {
        return (bool) $module->registerHook(self::HOOKS);
    }
}
