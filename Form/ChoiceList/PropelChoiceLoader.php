<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Form\ChoiceList;

use BaseObject;
use ColumnMap;
use Criteria;
use ModelCriteria;
use PDO;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class PropelChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * @var ChoiceListFactoryInterface
     */
    protected $factory;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ModelCriteria
     */
    protected $query;

    /**
     * The fields of which the identifier of the underlying class consists
     *
     * This property should only be accessed through identifier.
     *
     * @var array
     */
    protected $identifier = [];

    /**
     * Whether to use the identifier for index generation.
     *
     * @var bool
     */
    protected $identifierAsIndex = false;

    /**
     * @var ChoiceListInterface
     */
    protected $choiceList;

    /**
     * PropelChoiceListLoader constructor.
     *
     * @param ChoiceListFactoryInterface $factory
     * @param string $class
     */
    public function __construct(ChoiceListFactoryInterface $factory, $class, ModelCriteria $queryObject, $useAsIdentifier = null)
    {
        $this->factory = $factory;
        $this->class   = $class;
        $this->query   = $queryObject;
        if ($useAsIdentifier) {
            $this->identifier = [$this->query->getTableMap()->getColumn($useAsIdentifier)];
        } else {
            $this->identifier = $this->query->getTableMap()->getPrimaryKeys();
        }
        if (1 === count($this->identifier) && $this->isScalar(current($this->identifier))) {
            $this->identifierAsIndex = true;
        }
    }

    /**
     * Whether this column contains scalar values (to be used as indices).
     *
     * @param ColumnMap $column
     *
     * @return bool
     */
    private function isScalar(ColumnMap $column)
    {
        return in_array($column->getPdoType(), [
            PDO::PARAM_BOOL,
            PDO::PARAM_INT,
            PDO::PARAM_STR,
        ], true);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Performance optimization
        if (empty($values)) {
            return [];
        }

        // Optimize performance in case we have a single-field identifier
        if (!$this->choiceList && $this->identifierAsIndex && current($this->identifier) instanceof ColumnMap) {
            $phpName          = current($this->identifier)->getPhpName();
            $query            = clone $this->query;
            $unorderedObjects = $query->filterBy($phpName, $values, Criteria::IN)->find();
            $objectsById      = [];
            $objects          = [];

            // Maintain order and indices from the given $values
            foreach ($unorderedObjects as $object) {
                $objectsById[(string)current($this->getIdentifierValues($object))] = $object;
            }

            foreach ($values as $i => $id) {
                if (isset($objectsById[$id])) {
                    $objects[$i] = $objectsById[$id];
                }
            }

            return $objects;
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * Returns the values of the identifier fields of a model.
     *
     * Propel must know about this model, that is, the model must already
     * be persisted or added to the idmodel map before. Otherwise an
     * exception is thrown.
     *
     * @param object $model The model for which to get the identifier
     *
     * @return array
     */
    private function getIdentifierValues($model)
    {
        if (!$model instanceof $this->class) {
            return [];
        }

        if (1 === count($this->identifier) && current($this->identifier) instanceof ColumnMap) {
            $phpName = current($this->identifier)->getPhpName();
            if (method_exists($model, 'get' . $phpName)) {
                return [$model->{'get' . $phpName}()];
            }
        }

        if ($model instanceof BaseObject) {
            return [$model->getPrimaryKey()];
        }

        return $model->getPrimaryKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if ($this->choiceList) {
            return $this->choiceList;
        }

        $models = iterator_to_array($this->query->find());

        $this->choiceList = $this->factory->createListFromChoices($models, $value);

        return $this->choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Performance optimization
        if (empty($choices)) {
            return [];
        }

        if (!$this->choiceList && $this->identifierAsIndex) {
            $values = [];

            // Maintain order and indices of the given objects
            foreach ($choices as $i => $object) {
                if ($object instanceof $this->class) {
                    // Make sure to convert to the right format
                    $values[$i] = (string)current($this->getIdentifierValues($object));
                }
            }

            return $values;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }

}
