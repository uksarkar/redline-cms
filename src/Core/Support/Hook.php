<?php

namespace RedlineCms\Core\Support;

use Exception;

class Hook
{
    const DEFINITIONS = [
        "describe" => "array",
        "install" => "void",
        "version" => "string",
    ];

    /**
     * Call the specified hook, ensuring its type matches the defined type.
     *
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public static function call(string $name)
    {
        if (!array_key_exists($name, static::DEFINITIONS)) {
            throw new Exception("Unrecognized hook: '$name'. Check available hook names in DEFINITIONS.");
        }

        $hookCallbacks = ThemeManager::getHook($name);

        if (empty($hookCallbacks)) {
            throw new Exception("No callbacks registered for hook: '$name'.");
        }

        $results = [];

        foreach ($hookCallbacks as $callback) {
            $result = self::invokeCallback($callback);

            if (!self::validateType($result, self::DEFINITIONS[$name])) {
                throw new Exception("Hook '$name' returned data of invalid type. Expected type: " . self::DEFINITIONS[$name]);
            }

            // If the return type is 'void', skip adding to results
            if (self::DEFINITIONS[$name] !== 'void') {
                $results[] = $result;
            }
        }

        return self::consolidateResults($results, $name);
    }

    /**
     * Invoke a callback, resolving dependencies if callable.
     *
     * @param callable $callback
     * @return mixed
     */
    protected static function invokeCallback(callable $callback)
    {
        return App::getContainer()->call($callback);
    }

    /**
     * Validates the hook return type against the expected type.
     *
     * @param mixed $value
     * @param string $expectedType
     * @return bool
     */
    protected static function validateType($value, string $expectedType): bool
    {
        switch ($expectedType) {
            case 'array':
                return is_array($value);
            case 'string':
                return is_string($value);
            case 'int':
                return is_int($value);
            case 'bool':
                return is_bool($value);
            case 'void':
                return $value === null;
            default:
                // Handle object class types
                if (class_exists($expectedType)) {
                    return $value instanceof $expectedType;
                }
                return gettype($value) === $expectedType;
        }
    }

    /**
     * Consolidate multiple hook results into a single return value, if necessary.
     *
     * @param array $results
     * @param string $hookName
     * @return mixed
     */
    protected static function consolidateResults(array $results, string $hookName)
    {
        // Return consolidated results based on type
        $expectedType = self::DEFINITIONS[$hookName];

        if ($expectedType === 'array') {
            return array_merge(...$results);
        } elseif ($expectedType === 'string' || $expectedType === 'int') {
            return end($results);  // Return the last result for single-value types
        }

        return $results;
    }
}
