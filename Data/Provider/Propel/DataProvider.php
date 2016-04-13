<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Data\Provider\Propel;

use Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface;
use Vardius\Bundle\CrudBundle\Data\DataProviderInterface;

/**
 * DataProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /** @var string */
    protected $class;
    /** @var \ModelCriteria */
    protected $source;
    /** @var CrudManagerInterface */
    protected $crudManager;

    /**
     * @param string $class
     * @param CrudManagerInterface $crudManager
     */
    function __construct($class, CrudManagerInterface $crudManager = null)
    {
        $this->class = $class;
        $this->source = \PropelQuery::from($class);
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
     * @param integer $id
     * @return mixed
     */
    public function get($id = null)
    {
        if ($id !== null) {

            if ($this->crudManager instanceof CrudManagerInterface) {

                return $this->crudManager->get($id);
            } else {
                $this->source->clear();

                return $this->source->findPk(1);
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return new $this->class();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data)
    {
        $entity = null;
        if (is_object($data)) {
            $entity = $data;
        } elseif (is_numeric($data)) {
            $this->source->clear();
            $entity = $this->source->findPk($data);
        } else {
            throw new \InvalidArgumentException('Argument passed is not an object or it\'s not id');
        }

        if (!$entity instanceof \Persistent) {
            throw new \InvalidArgumentException('The $entity instance is not supported by the Propel implementation');
        }

        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->remove($entity);
        } else {
            $entity->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        if (!$data instanceof \Persistent) {
            throw new \InvalidArgumentException('The $data instance is not supported by the Propel implementation');
        }

        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->add($data);
        } else {
            $data->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($data)
    {
        if (!$data instanceof \Persistent) {
            throw new \InvalidArgumentException('The $data instance is not supported by the Propel implementation');
        }

        if ($this->crudManager instanceof CrudManagerInterface) {
            $this->crudManager->add($data);
        } else {
            $data->save();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        $this->source->clear();

        foreach ($criteria as $field => $value) {
            $method = 'filterBy' . ucfirst($field);
            $this->source->$method($value);
        }

        return $this->source->findOne();
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        $this->source->clear();
        return $this->source->find();
    }

    /**
     * {@inheritDoc}
     */
    public function reload($data)
    {
        if (!$data instanceof \Persistent) {
            throw new \InvalidArgumentException('The $data instance is not supported by the Propel implementation');
        }

        $data->reload();
    }
}
