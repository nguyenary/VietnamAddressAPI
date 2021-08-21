<?php

require 'vendor/autoload.php';

use NguyenAry\VietnamAddressAPI\Address;

echo "<pre>";

// Lấy tất cả thông tin tỉnh, thành phố
print_r(Address::getProvinces());

// Lấy danh sách tỉnh thành phố theo province_ids (province_id là key trong file src/data/province.json)
// Chọn những trường muốn lấy bằng setSchema() (Default: Lấy tất cả)
// Lưu ý: Với những province_id bắt đầu bằng số 0 thì phải là dạng string
// Address::setSchema(['name', 'type']);
// print_r(Address::getProvinces(['01', 87, 12]));

// Lấy thông tin của một tỉnh, thành phố bằng province_id
//print_r(Address::getProvince('01'));

// Lấy danh sách quận huyện của một tỉnh, thành phố bằng province_id
//print_r(Address::getDistrictsByProvinceId('01'));

// Lấy thông tin một quận huyện bằng district_id
//print_r(Address::getDistrict('009'));

// Lấy danh sách các xã, phường của một quận, huyện bằng district_id
//print_r(Address::getWardsByDistrictId('009'));

// Lấy thông tin một xã phường trong một quận, huyện bằng district_id và ward_id
//print_r(Address::getWard('009', '00346'));
