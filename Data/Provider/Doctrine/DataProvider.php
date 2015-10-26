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
use Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;

/**
 * DataProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /** @var EntityRepository */
    protected $source;
    /** @var EntityManager */
    protected $entityManager;
    /** @var CrudManagerInterface */
    protected $crudManager;

    /**
     * @param EntityRepository $repository
     * @param EntityManager $entityManager
     * @param CrudManagerInterface $crudManager
     */
    function __construct(EntityRepository $repository, EntityManager $entityManager, CrudManagerInterface $crudManager = null)
    {
        $this->source = $repository;
        $this->entityManager = $entityManager;
        $this->crudManager = $crudManager;
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function get($id = null)
    {
        if ($id !== null) {

            if ($this->crudManager instanceof CrudManagerInterface) {

                return $this->crudManager->get($id);
            } else {
                $query = $this->source->createQueryBuilder('entity');

                return $query
                    ->andWhere('entity.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
            }
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
    public function remove($data, $flush = true)
    {
        $entity = null;
        if (is_object($data)) {
            $entity = $data;
        } elseif (is_numeric($data)) {
            $entity = $this->source->findOneById($data);
        } else {
            throw new \InvalidArgumentException('Argument passed is not an object or it\'s id');
        }

        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->remove($entity);
        } else {
            $this->entityManager->remove($entity);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->add($data);
        } else {
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($data)
    {
        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->update($data);
        } else {
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush($data);
    }

    /**
     * @param EntityRepository $source
     * @return DataProvider
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
}
