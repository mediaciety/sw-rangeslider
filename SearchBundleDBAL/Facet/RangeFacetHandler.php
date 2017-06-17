<?php
/**
 * Created by PhpStorm.
 * User: haged
 * Date: 16.06.2017
 * Time: 06:40
 */

namespace McRangeslider\SearchBundleDBAL\Facet;


use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\RangeFacetResult;
use Shopware\Bundle\SearchBundleDBAL\FacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactory;
use Shopware\Bundle\StoreFrontBundle\Struct;
use EasySlugger\Slugger;

class RangeFacetHandler implements FacetHandlerInterface
{
    private $qbf;

    public function __construct(QueryBuilderFactory $qbf)
    {
            $this->qbf = $qbf;
    }

    public function supportsFacet(FacetInterface $facet)
    {
        return $facet instanceof RangeFacet;
    }

    public function generateFacet(
        FacetInterface $facet,
        Criteria $criteria,
        Struct\ShopContextInterface $context
    )
    {
        $sfacetName = Slugger::slugify($facet->getName());
        $limit = $this->getMinMax($facet, $criteria, $context);

        if($condition = $criteria->getCondition($facet->getName())){

            $actMin = $condition->getMin();
            $actMax = $condition->getMax();
        }

        if($limit){
            return new RangeFacetResult(
                $facet->getName(),
                $criteria->hasCondition($facet->getName()),
                $facet->getName(),
                (float) $limit['minimum'],
                (float) $limit['maximum'],
                (float) $actMin,
                (float) $actMax,
                Slugger::slugify($facet->getName()).'_min', Slugger::slugify($facet->getName()).'_max'
            );
        } else {
            return false;
        }

    }

    private function getMinMax(FacetInterface $facet, Criteria $criteria, Struct\ShopContextInterface $context){

        $qb = $this->qbf->createQueryBuilder();
        $conn = $qb->getConnection();

        $sql = "SELECT min(CAST(fv.value AS DECIMAL(10,2))) as minimum, max(CAST(fv.value AS DECIMAL(10,2))) as maximum FROM s_filter_options as fo 
                JOIN s_filter_values as fv ON fo.id = fv.optionID
                WHERE fo.`name` LIKE '".$facet->getName()."'";

        return $conn->fetchAssoc($sql);
    }
}