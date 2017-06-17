<?php

/**
 * Created by PhpStorm.
 * User: haged
 * Date: 15.06.2017
 * Time: 16:43
 */
namespace McRangeslider\SearchBundleDBAL\Facet;

use Shopware\Bundle\SearchBundle\FacetInterface;

class RangeFacet implements FacetInterface
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    function jsonSerialize(){
        return get_object_vars($this);
    }
}