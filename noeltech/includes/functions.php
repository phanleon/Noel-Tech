<?php
// includes/functions.php

/**
 * Định dạng một số thành chuỗi tiền tệ VNĐ.
 * Ví dụ: 1500000 => "1.500.000₫"
 *
 * @param float $number Số tiền cần định dạng.
 * @return string Chuỗi tiền tệ đã được định dạng.
 */
function format_vnd($number) {
    // number_format(số, số chữ số thập phân, dấu ngăn cách thập phân, dấu ngăn cách hàng nghìn)
    return number_format($number, 0, ',', '.') . '₫';
}
?>