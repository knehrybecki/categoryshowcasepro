<?php

namespace PrestaShop\Module\categoryshowcasepro\Repository;

use Doctrine\DBAL\Connection;

class CategoryRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAllDisabledCategories($id_lang)
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('c.id_category', 'cl.name')
            ->from(_DB_PREFIX_ . 'category', 'c')
            ->innerJoin('c', _DB_PREFIX_ . 'category_lang', 'cl', 'c.id_category = cl.id_category')
            ->where('cl.id_lang = :id_lang')
            ->andWhere('c.active = :active')
            ->setParameter('id_lang', $id_lang, \PDO::PARAM_INT)
            ->setParameter('active', 0, \PDO::PARAM_INT);

        $categories = $qb->execute()->fetchAll();

        $choices = [];
        foreach ($categories as $category) {
            $choices[$category['name']] = $category['id_category'];
        }

        return $choices;
    }
}
