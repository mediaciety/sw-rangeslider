<?php

/**
 * Created by PhpStorm.
 * User: haged
 * Date: 15.06.2017
 * Time: 16:41
 */

namespace McRangeslider\SearchBundleDBAL;

use Doctrine\DBAL\Connection;
use Enlight_Controller_Request_RequestHttp as Request;
use McRangeslider\SearchBundleDBAL\Condition\RangeCondition;
use McRangeslider\SearchBundleDBAL\Facet\RangeFacet;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use EasySlugger\Slugger;

use Shopware\Models\Property;

class CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    private $connection;

    public function __construct(Connection $conn)
    {
        $this->connection = $conn;
    }

    public function handleRequest(
        Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    )
    {
        $props = $this->getRangeProperties();

        foreach($props as $prop){
            $sprop = Slugger::slugify($prop['name']);

            if($request->has($sprop.'_min') || $request->has($sprop.'_max')){
                $limits = [
                    'min' => $request->get($sprop.'_min'),
                    'max' => $request->get($sprop.'_max')
                    ];
                $criteria->addCondition(new RangeCondition($prop['name'], $limits['min'], $limits['max']));
            }
            $criteria->addFacet(new RangeFacet($prop['name']));
        }
    }


    private function getRangeProperties(){

        $sql = "SELECT fo.name FROM s_filter_options AS fo 
                JOIN s_filter_options_attributes AS foa ON  fo.id = foa.optionID
                WHERE foa.israngefilter IS TRUE";

        $properties = $this->connection->fetchAll($sql);

        return $properties;

    }
}