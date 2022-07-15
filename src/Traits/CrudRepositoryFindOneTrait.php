<?php

namespace ZnDomain\Repository\Traits;

use ZnCore\Code\Helpers\PropertyHelper;
use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Text\Helpers\Inflector;
use ZnDomain\Domain\Enums\EventEnum;
use ZnDomain\Entity\Exceptions\AlreadyExistsException;
use ZnDomain\Entity\Exceptions\NotFoundException;
use ZnDomain\Entity\Interfaces\EntityIdInterface;
use ZnDomain\Entity\Interfaces\UniqueInterface;
use ZnDomain\Query\Entities\Query;
use ZnLib\I18Next\Facades\I18Next;

trait CrudRepositoryFindOneTrait
{

    protected $primaryKey = ['id'];

    public function findOneById($id, Query $query = null): EntityIdInterface
    {
        if (empty($id)) {
            throw (new InvalidMethodParameterException('Empty ID'))
                ->setParameterName('id');
        }
        $query = $this->forgeQuery($query);
        $query->where($this->primaryKey[0], $id);
        $entity = $this->findOne($query);
        return $entity;
    }

    public function findOne(Query $query = null): object
    {
        $query->limit(1);
        $collection = $this->findAll($query);
        if ($collection->count() < 1) {
            throw new NotFoundException('Not found entity!');
        }
        $entity = $collection->first();
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_READ_ENTITY);
        return $entity;
    }

    public function checkExists(EntityIdInterface $entity): void
    {
        try {
            $existedEntity = $this->findOneByUnique($entity);
            if ($existedEntity) {
                $message = I18Next::t('core', 'domain.message.entity_already_exist');
                $e = new AlreadyExistsException($message);
                $e->setEntity($existedEntity);
                throw $e;
            }
        } catch (NotFoundException $e) {
        }
    }

    public function findOneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        $unique = $entity->unique();
        if (!empty($unique)) {
            foreach ($unique as $uniqueConfig) {
                $oneEntity = $this->findOneByUniqueGroup($entity, $uniqueConfig);
                if ($oneEntity) {
                    return $oneEntity;
                }
            }
        }
        throw new NotFoundException();
    }

    private function findOneByUniqueGroup(UniqueInterface $entity, $uniqueConfig): ?EntityIdInterface
    {
        $query = new Query();
        foreach ($uniqueConfig as $uniqueName) {
            $value = PropertyHelper::getValue($entity, $uniqueName);
            if ($value === null) {
                return null;
            }
            $query->where(Inflector::underscore($uniqueName), $value);
        }
        $all = $this->findAll($query);
        if ($all->count() > 0) {
            return $all->first();
        }
        return null;
    }
}
