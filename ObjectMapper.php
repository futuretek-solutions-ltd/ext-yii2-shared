<?php

namespace futuretek\shared;

use ArrayAccess;

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
    public static function toObject($object, $properties, array $recursive = [], $strict = false)
    {
        return static::configureRecursive($object, $properties, $recursive, $strict);
    }

    /**
     * Configures an object with the initial property values.
     *
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @param array $recursive Definition of recursive properties.
     * Use key as property name and value as property object. For list of objects use [], eg. Subjekt[]
     * @return object|mixed the object itself
     * @throws \ReflectionException
     * @deprecated Use ObjectMapper::toObject() instead.
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
                            static::toObject($subObj, $subVal, self::stripModelFromRelatedKeys($recursive, $name), $strict);
                            $object->$name[$idx] = $subObj;
                        }
                    }
                } else {
                    //Single object
                    $className = $recursive[$name];
                    if (is_array($value)) {
                        $object->$name = new $className();
                        static::toObject($object->$name, $value, self::stripModelFromRelatedKeys($recursive, $name), $strict);
                    } else {
                        $object->$name = $value;
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

    /**
     * Converts an object or an array of objects into an array.
     * @param object|array|string $object the object to be converted into an array
     * @param array $properties a mapping from object class names to the properties that need to put into the resulting arrays.
     * @param bool $recursive whether to recursively converts properties which are objects into arrays.
     * @return array|mixed the array representation of the object
     * @throws \Exception
     */
    public static function toArray($object, array $properties = [], bool $recursive = true)
    {
        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::toArray($value, $properties);
                    }
                }
            }

            return $object;
        }

        if ($object instanceof Date) {
            return $object->format('Y-m-d');
        }

        if ($object instanceof \DateTimeInterface) {
            return $object->format('Y-m-d\TH:i:s');
        }

        if (is_object($object)) {
            if (!empty($properties)) {
                $className = get_class($object);
                if (!empty($properties[$className])) {
                    $result = [];
                    foreach ($properties[$className] as $key => $name) {
                        if (is_int($key)) {
                            $result[$name] = $object->$name;
                        } else {
                            $result[$key] = static::_getValue($object, $name);
                        }
                    }

                    return $recursive ? static::toArray($result, $properties) : $result;
                }
            }

            $result = [];
            foreach ($object as $key => $value) {
                $result[$key] = $value;
            }

            return $recursive ? static::toArray($result, $properties) : $result;
        }

        return [$object];
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array, the default value will be returned instead.
     * Not used when getting value from an object.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
     * like `['x', 'y', 'z']`.
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure|array $key key name of the array element, an array of keys or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * The possibility to pass an array of keys is available since version 2.0.4.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     */
    private static function _getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::_getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_object($array) && property_exists($array, $key)) {
            return $array->$key;
        }

        if (static::_keyExists($key, $array)) {
            return $array[$key];
        }

        if ($key && ($pos = strrpos($key, '.')) !== false) {
            $array = static::_getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (static::_keyExists($key, $array)) {
            return $array[$key];
        }
        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            try {
                return $array->$key;
            } catch (\Exception $e) {
                if ($array instanceof ArrayAccess) {
                    return $default;
                }
                throw $e;
            }
        }

        return $default;
    }

    private static function _keyExists($key, $array): bool
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return true;
        }

        return $array instanceof ArrayAccess && $array->offsetExists($key);
    }

    protected static function stripModelFromRelatedKeys(array $relations, string $propertyName): array
    {
        $result = [];
        foreach ($relations as $k => $v) {
            if (strpos($k, $propertyName . '.') === 0) {
                $result[substr($k, strlen($propertyName) + 1)] = $v;
            }
        }

        return $result;
    }
}
