<?php

define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
define('DIST_DIR', ROOT . DS . 'src/data');
define('EXCEL_FILES_DIR_NAME', 'excel_files');
define('EXCEL_FILES_DIR', ROOT . DS . EXCEL_FILES_DIR_NAME);

require 'vendor/autoload.php';

function slug($string)
{
    static $slugify;

    if (!$slugify) {
        $slugify = new \Cocur\Slugify\Slugify();
    }

    return $slugify->slugify($string);
}

function isStartWith($str, $start_with)
{
    return $start_with === substr($str, 0, strlen($start_with));
}

function readExcelFile($file)
{
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    $fields = ['ten_tinh_tp_va_cap', 'ma_tinh_tp', 'ten_quan_huyen_va_cap', 'ma_qh', 'ten_phuong_xa_va_cap', 'ma_px', 'cap_px', 'ten_tieng_anh_px'];

    $data = array();

    $tp_label = 'thành phố';
    $tp_label_len = strlen($tp_label);

    $tinh_label = 'Tỉnh';
    $tinh_name_len = strlen($tinh_label);

    $quan_label = 'Quận';
    $quan_label_len = strlen($quan_label);


    $huyen_label = 'Huyện';
    $huyen_label_len = strlen($huyen_label);

    $thi_xa_label = 'thị xã';
    $thi_xa_label_len = strlen($thi_xa_label);

    $phuong_label = 'Phường';
    $phuong_label_len = strlen($phuong_label);

    $xa_label = 'Xã';
    $xa_label_len = strlen($xa_label);

    $thi_tran_label = 'Thị trấn';
    $thi_tran_label_len = strlen($thi_tran_label);


    for ($row = 2; $row <= $highestRow; ++$row) {
        $row_data = [];

        for ($col = 0; $col < $highestColumnIndex; ++$col) {
            $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            $row_data[$fields[$col]] = $value;
        }


        $row_data['la_tp'] = isStartWith(mb_strtolower($row_data['ten_tinh_tp_va_cap']), mb_strtolower($tp_label));
        $row_data['loai_tinh_tp'] = $row_data['la_tp'] ? 'thanh-pho' : 'tinh';
        $row_data['ten_tinh_tp'] = trim(substr($row_data['ten_tinh_tp_va_cap'], $row_data['la_tp'] ? $tp_label_len : $tinh_name_len));
        $row_data['ten_tinh_tp_slug'] = slug($row_data['ten_tinh_tp']);

        $row_data['qh_la_tp'] = isStartWith(mb_strtolower($row_data['ten_quan_huyen_va_cap']), mb_strtolower($tp_label));
        $row_data['qh_la_thi_xa'] = !$row_data['qh_la_tp'] && isStartWith(mb_strtolower($row_data['ten_quan_huyen_va_cap']), mb_strtolower($thi_xa_label));
        $row_data['qh_la_quan'] = !$row_data['qh_la_thi_xa'] && !$row_data['qh_la_thi_xa'] && isStartWith(mb_strtolower($row_data['ten_quan_huyen_va_cap']), mb_strtolower($quan_label));
        $row_data['qh_la_huyen'] = !($row_data['qh_la_thi_xa'] || $row_data['qh_la_thi_xa'] || $row_data['qh_la_quan']);

        switch (true) {
            case $row_data['qh_la_tp']:
                $row_data['ten_qh'] = trim(substr($row_data['ten_quan_huyen_va_cap'], $tp_label_len));
                $row_data['loai_qh'] = 'thanh-pho';
                break;
            case $row_data['qh_la_thi_xa']:
                $row_data['ten_qh'] = trim(substr($row_data['ten_quan_huyen_va_cap'], $thi_xa_label_len));
                $row_data['loai_qh'] = 'thi-xa';
                break;
            case $row_data['qh_la_quan']:
                $row_data['ten_qh'] = trim(substr($row_data['ten_quan_huyen_va_cap'], $quan_label_len));
                $row_data['loai_qh'] = 'quan';
                break;
            case $row_data['qh_la_huyen']:
                $row_data['ten_qh'] = trim(substr($row_data['ten_quan_huyen_va_cap'], $huyen_label_len));
                $row_data['loai_qh'] = 'huyen';
                break;
        }

        //Fix bug một vài chỗ dùng "Thị Xã" thay vì "Thị xã"
        if ($row_data['qh_la_thi_xa']) {
            $row_data['ten_quan_huyen_va_cap'] = 'Thị xã ' . $row_data['ten_qh'];
        }

        $row_data['ten_qh_slug'] = slug($row_data['ten_qh']);
        $row_data['dia_chi_qh'] = implode(', ', [$row_data['ten_qh'], $row_data['ten_tinh_tp']]);
        $row_data['dia_chi_qh_full'] = implode(', ', [$row_data['ten_quan_huyen_va_cap'], $row_data['ten_tinh_tp_va_cap']]);

        $row_data['px_la_phuong'] = $row_data['cap_px'] === 'Phường';
        $row_data['px_la_thi_tran'] = !$row_data['px_la_phuong'] && $row_data['cap_px'] === $thi_tran_label;
        $row_data['px_la_xa'] = !($row_data['px_la_phuong'] || $row_data['px_la_thi_tran']);

        switch (true) {
            case $row_data['px_la_phuong']:
                $row_data['ten_px'] = trim(substr($row_data['ten_phuong_xa_va_cap'], $phuong_label_len));
                $row_data['loai_px'] = 'phuong';
                break;
            case $row_data['px_la_thi_tran']:
                $row_data['ten_px'] = trim(substr($row_data['ten_phuong_xa_va_cap'], $thi_tran_label_len));
                $row_data['loai_px'] = 'thi-tran';
                break;
            case $row_data['px_la_xa']:
                $row_data['ten_px'] = trim(substr($row_data['ten_phuong_xa_va_cap'], $xa_label_len));
                $row_data['loai_px'] = 'xa';
                break;
        }

        $row_data['ten_px_slug'] = slug($row_data['ten_px']);
        $row_data['dia_chi_px'] = implode(', ', [$row_data['ten_px'], $row_data['dia_chi_qh']]);
        $row_data['dia_chi_px_full'] = implode(', ', [$row_data['ten_phuong_xa_va_cap'], $row_data['dia_chi_qh_full']]);

        $data[] = $row_data;
    }

    return $data;
}

$tinh_tp = [];
$quan_huyen = [];
$xa_phuong = [];
$tree = [];

$current_quan_huyen_code = null;

$files = glob(EXCEL_FILES_DIR . '/*.xls', GLOB_NOSORT);
$files_count = count($files);
$file_index = 1;

foreach ($files as $file) {
    echo implode(' ', ['Import', $file_index++ . '/' . $files_count . ': ', $file]) . "\n";
    $data = readExcelFile($file);

    $tinh_tp_data = array(
        'name' => $data[0]['ten_tinh_tp'],
        'slug' => $data[0]['ten_tinh_tp_slug'],
        'type' => $data[0]['la_tp'] ? 'thanh-pho' : 'tinh',
        'name_with_type' => $data[0]['ten_tinh_tp_va_cap'],
        'code' => $data[0]['ma_tinh_tp']
    );
    $tinh_tp[$tinh_tp_data['code']] = $tinh_tp_data;
    $tree[$tinh_tp_data['code']] = $tinh_tp_data;
    $tree[$tinh_tp_data['code']]['districts'] = [];

    $current_quan_huyen = [];
    $current_xa_phuong_by_quan = [];

    foreach ($data as $row) {
        if ($current_quan_huyen_code !== $row['ma_qh']) {
            $current_quan_huyen_code = $row['ma_qh'];

            $quan_huyen_data = array(
                'name' => $row['ten_qh'],
                'type' => $row['loai_qh'],
                'slug' => $row['ten_qh_slug'],
                'name_with_type' => $row['ten_quan_huyen_va_cap'],
                'path' => $row['dia_chi_qh'],
                'path_with_type' => $row['dia_chi_qh_full'],
                'code' => $row['ma_qh'],
                'parent_code' => $tinh_tp_data['code']
            );
            $quan_huyen[$row['ma_qh']] = $quan_huyen_data;
            $current_quan_huyen[$row['ma_qh']] = $quan_huyen_data;
            $current_xa_phuong_by_quan[$current_quan_huyen_code] = [];

            $tree[$tinh_tp_data['code']]['districts'][$row['ma_qh']] = $quan_huyen_data;
            $tree[$tinh_tp_data['code']]['districts'][$row['ma_qh']]['wards'] = [];
        }

        $xa_phuong_data = array(
            'name' => $row['ten_px'],
            'type' => $row['loai_px'],
            'slug' => $row['ten_px_slug'],
            'name_with_type' => $row['ten_phuong_xa_va_cap'],
            'path' => $row['dia_chi_px'],
            'path_with_type' => $row['dia_chi_px_full'],
            'code' => $row['ma_px'],
            'parent_code' => $current_quan_huyen_code
        );
        $xa_phuong[$xa_phuong_data['code']] = $xa_phuong_data;
        $current_xa_phuong_by_quan[$current_quan_huyen_code][$xa_phuong_data['code']] = $xa_phuong_data;

        $tree[$tinh_tp_data['code']]['districts'][$current_quan_huyen_code]['wards'][$xa_phuong_data['code']] = $xa_phuong_data;
    }

    file_put_contents(DIST_DIR . DS . 'districts' . DS . $tinh_tp_data['code'] . '.json', json_encode($current_quan_huyen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    foreach (array_keys($current_quan_huyen) as $temp_qh_id) {
        file_put_contents(DIST_DIR . DS . 'wards' . DS . $temp_qh_id . '.json', json_encode($current_xa_phuong_by_quan[$temp_qh_id], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

file_put_contents(DIST_DIR . DS . 'province.json', json_encode($tinh_tp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents(DIST_DIR . DS . 'district.json', json_encode($quan_huyen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));