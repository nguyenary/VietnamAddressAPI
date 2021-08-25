<?php

namespace NguyenAry\VietnamAddressAPI;

class Address
{
    private static $schema = [];

    /**
     * @param array $province_ids [Option] Danh sách province_id cần lấy. (Mặc định: Lấy tất cả)
     * @description Lấy danh sách tỉnh, thành phố của Việt Nam (tùy chọn với danh sách $province_id)
     * @return array
     */
    public static function getProvinces(?array $province_ids = []): array
    {
        $provinces = ReadData::read(Constant::PATH_PROVINCES);

        if ($province_ids) {
            $provinces = array_filter(
                $provinces,
                function ($key) use ($province_ids) {
                    return in_array(strval($key), $province_ids);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return static::outputs($provinces);
    }

    /**
     * @param string $province_id
     * @description Lấy thông tin tỉnh thành phố bằng province_id
     * @return array
     */
    public static function getProvince(string $province_id): array
    {
        return static::getProvinces([$province_id])[$province_id] ?? [];
    }

    /**
     * @param string $province_id
     * @description Lấy danh sách quận huyện của một tỉnh thành phố bằng province_id
     * @return array
     */
    public static function getDistrictsByProvinceId(string $province_id): array
    {
        $district_path = Constant::PATH_DISTRICTS_FOLDER . "/$province_id.json";
        $districts = ReadData::read($district_path);

        return static::outputs($districts);
    }

    /**
     * @param string $district_id
     * @description Lấy thông tin một quận huyện bằng district_id
     * @return array
     */
    public static function getDistrict(string $district_id): array
    {
        $districts = ReadData::read(Constant::PATH_DISTRICTS);
        $district = $districts[$district_id] ?? [];
        
        if (!$district) {
            return [];
        }
        
        return static::output($district);
    }

    /**
     * @param string $district_id
     * @description Lấy danh sách xã phường của một quận huyện bằng district_id
     * @return array
     */
    public static function getWardsByDistrictId(string $district_id): array
    {
        $ward_path = Constant::PATH_WARDS_FOLDER . "/$district_id.json";
        $wards = ReadData::read($ward_path);

        return static::outputs($wards);;
    }

    /**
     * @param string $district_id
     * @param string $ward_id
     * @description Lấy thông tin của một xã phường trong một quận huyện bằng district_id và ward_id
     * @return array
     */
    public static function getWard(string $district_id, string $ward_id): array
    {
        $wards = static::getWardsByDistrictId($district_id);

        return $wards[$ward_id] ?? [];
    }

    /**
     * @param array $schema
     */
    public static function setSchema(array $schema = []): void
    {
        static::$schema = $schema;
    }

    /**
     * @return array
     */
    private static function getSchema(): array
    {
        return static::$schema;
    }

    /**
     * @param array $data
     * @return array
     */
    private static function applySchema(array $data): array
    {
        if (!static::getSchema()) {
            return $data;
        }

        $province_new = [];

        foreach ($data as $key => $item) {
            if (in_array($key, static::getSchema())) {
                $province_new[$key] = $item;
            }
        }

        return $province_new;
    }

    /**
     * @param array $data
     * @return array
     */
    private static function outputs(array $data): array
    {
        $result = array_map('static::applySchema', $data);
        static::setSchema([]);

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private static function output(array $data): array
    {
        $result = static::applySchema($data);
        static::setSchema([]);

        return $result;
    }
}
