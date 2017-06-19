<?php
/**
 * Created by PhpStorm.
 * User: haged
 * Date: 16.06.2017
 * Time: 07:03
 */

namespace McRangeslider\SearchBundleDBAL\Condition;


use Doctrine\DBAL\Connection;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class RangeConditionHandler implements ConditionHandlerInterface
{
        public function supportsCondition(ConditionInterface $condition)
        {
            return $condition instanceof RangeCondition;
        }
        public function generateCondition(
            ConditionInterface $condition,
            QueryBuilder $query,
            ShopContextInterface $context
        )
        {
            if(!$query->hasState($condition->getName())){
                $query->addState($condition->getName());
                $this->limitRange($query, $condition);
            }

        }

        private function limitRange(QueryBuilder $query, ConditionInterface $condition){
            try{
                $conn = $query->getConnection();

                $min = 0;
                $max = 50000;

                if($condition->getMin()){
                    $min = $condition->getMin();;
                }
                if($condition->getMax()){
                    $max = $condition->getMax();;
                }

                $sql = "SELECT distinct fa.articleID FROM s_filter_options as fo 
                    JOIN s_filter_values as fv ON fo.id = fv.optionID 
                    JOIN s_filter_articles as fa ON fv.id = fa.valueID
                    WHERE CAST(fv.value AS DECIMAL(10,2)) BETWEEN ".$min." AND ".$max."
                    AND fo.`name` LIKE '".$condition->getName()."'";
                $valids = $conn->fetchAll($sql);


                $queryCriteria = $query;
                $queryCriteria->andWhere('product.id IN(:validArticleNumbers)');


                $ids = [];
                foreach($valids as $valid){
                    $ids[] = $valid['articleID'];
                }

                $queryCriteria->setParameter('validArticleNumbers', $ids, Connection::PARAM_STR_ARRAY);
                //die(var_dump($queryCriteria->getMaxResults()));

            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
}