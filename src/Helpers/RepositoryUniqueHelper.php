<?php

namespace ZnDomain\Repository\Helpers;

use ZnCore\Entity\Helpers\EntityHelper;
use ZnCore\Entity\Interfaces\UniqueInterface;
use ZnCore\Query\Entities\Query;
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
