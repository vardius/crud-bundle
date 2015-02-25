<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Data\Provider\Doctrine;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;

/**
 * DataProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var EntityRepository */
    protected $source;

    /**
     * @param EntityRepository $repository
     * @param EntityManager $entityManager
     */
    function __construct(EntityRepository $repository, EntityManager $entityManager)
    {
        $this->source = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function get($id = null)
    {
        if ($id !== null) {

            return $this->source->findOneById($id);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $className = $this->source->getClassName();

        return new $className();
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id, $flush = true)
    {
        $this->entityManager->remove($this->source->findById($id));

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush($data);
    }
}
