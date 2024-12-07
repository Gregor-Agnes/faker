<?php

namespace GeorgRinger\Faker\Property;

use Faker\Generator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Relation implements PropertyInterface
{
    static public function getSettings(array $configuration = [])
    {
        return [
            'type' => self::class,
            'table' => $configuration['table'] ?? null,
            'pid' => isset($configuration['pid']) ? $configuration['pid'] : 'current',
            'min' => $configuration['min'] ?? 0,
            'max' => $configuration['max'] ?? 99,
            'uid' => $configuration['uid'] ?? null,
        ];
    }

    public function generate(Generator $faker, array $configuration = [])
    {
        return $value = $this->getRelationUids($configuration);
    }


    protected function getRelationUids(array $configuration)
    {
        $table = $configuration['table'];
        
        if ( isset($configuration['uid']) ) {
            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder$queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryBuilder->select('uid')->from($table)->where(
                $queryBuilder->expr()->eq('uid', (int)$configuration['uid'])
            );
            $rows = $queryBuilder->executeQuery();
            
            foreach ($rows->fetchAllAssociative() as $row) {
                $list[] = $row['uid'];
            }
            
            return $list;
            
        } else {
            
            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder$queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryBuilder->select('uid')->from($table)->where(
                $queryBuilder->expr()->eq('pid', (int)$configuration['pid'])
            );
            $rows= $queryBuilder->executeQuery();
            
            $list = [];
            foreach ($rows->fetchAllAssociative() as $row) {
                $list[] = $row['uid'];
            }
            $randList = $this->array_random($list, rand($configuration['min'], $configuration['max']));
            if (is_array($randList)) {
                return implode(',', $randList);
            }
            return '';
        }
        
    }

    /**
     * @param array $arr
     * @param int $num
     *
     * @return array Returns an array with random $num elements from original array
     */
    protected function array_random($arr, $num = 1)
    {
        if ($num === 0) {
            return [];
        }
        shuffle($arr);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            if (isset($arr[$i])) {
                $r[] = $arr[$i];
            }
        }
        return $r;
    }
}
