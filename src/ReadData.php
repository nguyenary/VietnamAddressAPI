<?php

namespace NguyenAry\VietnamAddressAPI;

use Exception;

class ReadData
{
    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    public static function read(string $path) : array
    {
        if (!file_exists($path)) {
            throw new Exception('File path not exist');
        }

        return static::jsonToArray(file_get_contents($path));
    }

    /**
     * @param string $data
     * @return array
     * @throws Exception
     */
    private static function jsonToArray(string $data) :array
    {
        if (!static::isJson($data)) {
            throw new Exception('Error read data file. Data not be JSON');
        }

        return json_decode($data, true);
    }

    /**
     * @param string $string
     * @return bool
     */
    private static function isJson(string $string): bool
    {
        return is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE);
    }
}
