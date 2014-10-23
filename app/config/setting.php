<?php

/*
 +----------------------------------------------------------------------------------+
 | PhalconJumpstart                                                                 |
 +----------------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 PhalconJumpstart Team (http://phalconjumpstart.com)      |
 +----------------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled               |
 | with this package in the file docs/LICENSE.txt.                                  |
 |                                                                                  |
 | If you did not receive a copy of the license and are unable to                   |
 | obtain it through the world-wide-web, please send an email                       |
 | to license@phalconjumpstart.com so we can send you a copy immediately.           |
 +----------------------------------------------------------------------------------+
*/

$setting = new \Phalcon\Config([
    'global' => [
        'cssSiteRev' => 1,
        'jsSiteRev' => 1,
        'cssAdminRev' => 1,
        'jsAdminRev' => 1,
        'defaultLanguage' => 'en',
    ],
    'user' => [
        'viewUrl'                   =>  'public/uploads/user/',
        'imageDirectory'            =>  ROOT_PATH . '/public/uploads/user/',
        'validExtension'            =>  ['JPG', 'JPEG', 'PNG', 'GIF'],
        'validMaxFileSize'          =>  10 * 1024 * 1024, //size in byte
        'imageMaxWidth'             =>  '1200',
        'imageMaxHeight'            =>  '1200',
        'imageMediumWidth'          =>  '540',
        'imageMediumHeight'         =>  '1000',
        'imageThumbWidth'           =>  '300',
        'imageThumbHeight'          =>  '200',
        'imageThumbRatio'           =>  '3:2',
        'imageQuality'              =>  '95'
    ],
    'region' => [
        '82' => 'An Giang',
        '102' => 'Bà Rịa - Vũng Tàu',
        '106' => 'Bắc Ninh',
        '103' => 'Bắc Giang',
        '104' => 'Bắc Kạn',
        '105' => 'Bạc Liêu',
        '107' => 'Bến Tre',
        '109' => 'Bình Dương',
        '108' => 'Bình Định',
        '110' => 'Bình Phước',
        '111' => 'Bình Thuận',
        '161' => 'Bình Trị Thiên',
        '81' => 'Cà Mau',
        '7' => 'Cần Thơ',
        '112' => 'Cao Bằng',
        '162' => 'Cửu Long',
        '9' => 'Đà Nẵng',
        '6' => 'Đắc Lắk',
        '113' => 'Đắc Nông',
        '114' => 'Điện Biên',
        '8' => 'Đồng Nai',
        '115' => 'Đồng Tháp',
        '116' => 'Gia Lai',
        '201' => 'Hà Bắc',
        '121' => 'Hải Dương',
        '117' => 'Hà Giang',
        '118' => 'Hà Nam',
        '159' => 'Hà Nam Ninh',
        '5' => 'Hà Nội',
        '119' => 'Hà Tây',
        '120' => 'Hà Tĩnh',
        '158' => 'Hải Hưng',
        '122' => 'Hậu Giang',
        '101' => 'Hải Phòng',
        '123' => 'Hoà Bình',
        '124' => 'Hưng Yên',
        '125' => 'Khánh Hoà',
        '126' => 'Kiên Giang',
        '157' => 'Khác',
        '127' => 'Kon Tum',
        '128' => 'Lai Châu',
        '130' => 'Lạng Sơn',
        '129' => 'Lâm Đồng',
        '131' => 'Lào Cai',
        '132' => 'Long An',
        '133' => 'Nam Định',
        '134' => 'Nghệ An',
        '160' => 'Nghĩa Bình',
        '135' => 'Ninh Bình',
        '136' => 'Ninh Thuận',
        '137' => 'Phú Thọ',
        '138' => 'Phú Yên',
        '139' => 'Quảng Bình',
        '140' => 'Quảng Nam',
        '141' => 'Quảng Ngãi',
        '142' => 'Quảng Ninh',
        '143' => 'Quảng Trị',
        '144' => 'Sóc Trăng',
        '145' => 'Sơn La',
        '146' => 'Tây Ninh',
        '147' => 'Thái Bình',
        '148' => 'Thái Nguyên',
        '149' => 'Thanh Hoá',
        '150' => 'Thừa Thiên Huế',
        '151' => 'Tiền Giang',
        '3' => 'TP.Hồ Chí Minh',
        '152' => 'Trà Vinh',
        '153' => 'Tuyên Quang',
        '154' => 'Vĩnh Long',
        '155' => 'Vĩnh Phúc',
        '156' => 'Yên Bái'
    ],
]);
