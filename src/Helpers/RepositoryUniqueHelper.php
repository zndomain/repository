<?php

namespace ZnDomain\Repository\Helpers;

use ZnDomain\Entity\Helpers\EntityHelper;
use ZnDomain\Entity\Interfaces\UniqueInterface;
use ZnDomain\Query\Entities\Query;
use ZnCore\Text\Helpers\Inflector;

class RepositoryUniqueHelper
{

    public static function buildQuery(UniqueInterface $entity, array $uniqueConfig): Query
    {
        $query = new Query();
        foreach ($uniqueConfig as $uniqueName) {
            $value = EntityHelper::getValue($entity, $uniqueName);
            if ($value === null) {
                return null;
            }
            $query->where(Inflector::underscore($uniqueName), $value);
        }
        return $query;
    }
}
