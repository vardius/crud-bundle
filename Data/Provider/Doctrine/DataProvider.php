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
     * @param EntityRepository $source
     * @param EntityManager $entityManager
     * @param CrudManagerInterface $crudManager
     */
    function __construct(EntityRepository $source, EntityManager $entityManager, CrudManagerInterface $crudManager = null)
    {
        $this->source = $source;
        $this->entityManager = $entityManager;
        $this->crudManager = $crudManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get(int $id = null)
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
    public function remove($data, bool $flush = true)
    {
        $entity = null;
        if (is_object($data)) {
            $entity = $data;
        } elseif (is_numeric($data)) {
            $entity = $this->source->findOneById($data);
        } else {
            throw new \InvalidArgumentException('Argument passed is not an object or it\'s not id');
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

        $this->entityManager->flush();
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

        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return $this->source->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->source->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function reload($data)
    {
        $this->entityManager->refresh($data);
    }
}
