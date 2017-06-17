<?php

/**
 * Created by PhpStorm.
 * User: haged
 * Date: 16.06.2017
 * Time: 06:51
 */
namespace McRangeslider\SearchBundleDBAL\Condition;


use Shopware\Bundle\SearchBundle\ConditionInterface;


class RangeCondition implements ConditionInterface
{
    private $name;
    private $min =0;
    private $max =0;

    public function __construct($name, $min, $max)
    {
        $this->name = $name;
        $this->min = $min;
        $this->max = $max;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }
    public function jsonSerialize(){
        return get_object_vars($this);
    }

}