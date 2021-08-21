# Đơn Vị Hành Chính Việt Nam
> Một thư viện được viết bằng PHP để lấy thông tin các đơn vị hành chính Việt Nam (Tỉnh, Thành Phố, Quận, Huyện, Xã, Phường, Thị Trấn...)
## Installation
Install vào dự án bằng `composer`
```properties
composer require nguyenary/vietnam-address-api
```
## Usage
Sử dụng trong dự án bằng cách:
```php
require 'vendor/autoload.php';

use NguyenAry\VietnamAddressAPI\Address;
```

Xem ví dụ cụ thể tham khảo file `example.php`

#### ***`Address::getProvince()`***
> Lấy tất cả Tỉnh / Thành Phố của Việt Nam
- Option: 
    - `province_ids` (array) : Mảng chứa danh sách province_id muốn lấy
---
#### ***`Address::getProvince()`***
> Lấy thông tin của một Tỉnh, Thành Phố theo `province_id`
- Require:
    - `province_id` (string) : `province_id` của `Tỉnh / Thành Phố` muốn lấy thông tin
---
#### ***`Address::getDistrictsByProvinceId()`***
> Lấy danh sách Quận / Huyện của một Tỉnh / Thành Phố bằng `province_id`
- Require:
    - `province_id` (string) : `province_id` của _Tỉnh / Thành Phố_ để lấy danh sách các _Quận / Huyện_ của nó
---
#### ***`Address::getDistrict()`***
> Lấy thông tin của Quận / Huyện bằng `district_id`
- Require:
    - `district_id` (string) : `district_id` của _Quận / Huyện_ cần lấy thông tin
---
#### ***`Address::getWardsByDistrictId()`***
> Lấy danh sách Xã / Phường / Thị Trấn của một Quận / Huyện bằng `district_id`
- Require:
    - `district_id` (string) : `district_id` của _Quận / Huyện_ để lấy danh sách _Xã / Phường / Thị Trấn_ của nó
---
#### ***`Address::getWard()`***
> Lấy thông tin Xã / Phường / Thị Trấn bằng `distric_id` và `ward_id`
- Require:
    - `district_id` (string) : `district_id` của _Quận / Huyện_ chứa _Xã / Phường / Thị Trấn_ cần lấy thông tin
    - `ward_id` (string) : `ward_id` của _Xã / Phường / Thị Trấn_ cần lấy thông tin
---
#### ***`Address::setSchema()`***
> Định dạng lại các trường sẽ trả về. Gọi hàm này trước khi gọi các phương thức `get` ở trên (Mặc định sẽ trả về tất cả)
- Option:
    - `schema` (array) : Mảng chứa danh sách các trường cần lấy (Vd: `name`, `type`, `code`,...v..v)
---
##### Nguồn dữ liệu hành chính Việt Nam: [madnh/hanhchinhvn](https://github.com/madnh/hanhchinhvn)