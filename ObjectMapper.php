<?php

namespace futuretek\shared;

class ObjectMapper
{
    /**
     * Configures an object with the initial property values.
     *
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @param array $recursive Definition of recursive properties.
     * Use key as property name and value as property object. For list of objects use [], eg. Subjekt[]
     * @return object|mixed the object itself
     * @throws \ReflectionException
     */
    public static function configureRecursive($object, $properties, array $recursive = [], $strict = false)
    {
        if (!is_object($object)) {
            $object = new $object();
        }

        $ref = new \ReflectionClass($object);

        foreach ($properties as $name => $value) {
            if (!property_exists($object, $name)) {
                if ($strict) {
                    throw new \InvalidArgumentException(sprintf('Property with name %s not found.', $name));
                }
                continue;
            }

            if (array_key_exists($name, $recursive)) {
                //Recursive object
                if (false !== strpos($recursive[$name], '[]')) {
                    //Array
                    $object->$name = [];
                    if (is_array($value) && 0 !== count($value)) {
                        $className = substr($recursive[$name], 0, -2);
                        foreach ($value as $idx => $subVal) {
                            $subObj = new $className();
                            static::configureRecursive($subObj, $subVal, $recursive, $strict);
                            $object->$name[$idx] = $subObj;
                        }
                    }
                } else {
                    //Single object
                    $className = $recursive[$name];
                    $object->$name = $value;
                    if (is_array($value)) {
                        $object->$name = new $className();
                        static::configureRecursive($object->$name, $value, $recursive, $strict);
                    }
                }
            } else {
                $propType = $ref->getProperty($name)->getType();
                $type = $propType instanceof \ReflectionType ? $propType->getName() : null;
                switch ($type) {
                    case Date::class:
                        $object->$name = $value instanceof Date || $value === null ? $value : new Date($value);
                        break;
                    case \DateTime::class:
                        $object->$name = $value instanceof \DateTime || $value === null ? $value : new \DateTime($value);
                        break;
                    default:
                        $object->$name = $value;
                }
            }
        }

        return $object;
    }
}
