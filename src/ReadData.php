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
    public static function read(string $path): ?array
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
    private static function jsonToArray(string $data): ?array
    {
        $data = json_decode($data, true);

        if (!is_array($data) && (json_last_error() == JSON_ERROR_NONE)) {
            throw new Exception('Error read data file. Data not be JSON');
        }

        return $data;
    }
}
