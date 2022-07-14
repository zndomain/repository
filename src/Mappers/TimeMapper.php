<?php

namespace ZnDomain\Repository\Mappers;

use ZnDomain\Repository\Interfaces\MapperInterface;
use ZnCore\Text\Helpers\Inflector;

class TimeMapper implements MapperInterface
{

    public $format = 'Y-m-d H:i:s';
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function encode($entityAttributes)
    {
        foreach ($this->attributes as $attribute) {
//            $data[$attribute] = $time->format($this->format);
//            $data[$attribute] = json_encode($data[$attribute], JSON_UNESCAPED_UNICODE);
        }
        return $entityAttributes;
    }

    public function decode($rowAttributes)
    {
        foreach ($this->attributes as $attribute) {
            $attribute = Inflector::underscore($attribute);
            $value = $rowAttributes[$attribute];
            if ($value) {
                $rowAttributes[$attribute] = new \DateTime($value);
            }
        }
        return $rowAttributes;
    }
}
