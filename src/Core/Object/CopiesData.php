<?php

namespace JTDSoft\EssentialsSdk\Core\Object;

use Carbon\Carbon;
use JTDSoft\EssentialsSdk\Core\Collection;
use JTDSoft\EssentialsSdk\Core\Object;

trait CopiesData
{
    /**
     * Copies attributes from target object
     *
     * @param Object $target
     *
     * @return $this
     */
    public function copy(Object $target)
    {
        foreach ($target as $attribute => $value) {
            $this->{$attribute} = $value;
        }

        $this->cleanDirtyAttributes();

        return $this;
    }

    /**
     * Creates object from array
     *
     * @param array $array
     *
     * @return static
     */
    public function copyFromArray(array $array)
    {
        foreach ($array as $property => $value) {
            if (isset(static::getProperties()[$property])) {
                $type = static::getProperties()[$property]['type'];
                if (strpos($type, '[]') !== false) { //array
                    $type                  = trim($type, '[]');
                    $this->data[$property] = new Collection();
                    foreach ($value as $key => $single) {
                        $this->data[$property]->put($key, static::castSingleProperty($type, $single));
                    }
                } else {
                    $this->data[$property] = static::castSingleProperty($type, $value);
                }
            } else {
                $this->data[$property] = $value;
            }
        }

        return $this;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return mixed
     */
    protected static function castSingleProperty($type, $value)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'object':
                return (object)$value;
            case '\DateTime':
                return Carbon::createFromTimestamp(strtotime($value));
            default:
                if (class_exists($type)) {
                    return new $type($value);
                }

                return $value;
        }
    }
}
