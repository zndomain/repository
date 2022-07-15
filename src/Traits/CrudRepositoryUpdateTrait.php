<?php

namespace ZnDomain\Repository\Traits;

use ZnDomain\Domain\Enums\EventEnum;
use ZnDomain\Entity\Interfaces\EntityIdInterface;
use ZnDomain\Validator\Helpers\ValidationHelper;

trait CrudRepositoryUpdateTrait
{

    abstract protected function updateQuery($id, array $data): void;

    public function update(EntityIdInterface $entity)
    {
        ValidationHelper::validateEntity($entity);
        $this->findOneById($entity->getId());
        $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_UPDATE_ENTITY);
        $data = $this->mapperEncodeEntity($entity);
        $this->updateQuery($entity->getId(), $data);
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_UPDATE_ENTITY);
    }
}
