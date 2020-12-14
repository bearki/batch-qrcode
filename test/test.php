<?php
/**
 * 
 * 扩展库测试单元
 * 
 */

// 引入自动加载
require '../vendor/autoload.php';

// 实例化二维码批量生成类
// $batQR = new Bearki\BatchQRCode();

// 实例化时可传入Excel文件路径
$batQR = new Bearki\BatchQRCode('./static/excel/test.xlsx');


/**
 * #####类方法描述#####
 * 
 * setExcelPath('./static/excel/test.xlsx');                // 设置需要用于生成二维码的Excel文件路径
 * 
 * setQRCodeField(['name', 'age']);                         // 选择Excel表内的字段,用于生成二维码,支持多字段，拼接顺序与传入顺序有关
 * 
 * setQRCodePrefixSuffix('二维码内容前缀', '二维码内容后缀'); // 二维码内容的前缀与后缀，最后会与传入的Excel字段名对应的值拼接在一起
 * 
 * setDescrField(['msg1', 'msg2']);                         // 选择Excel表内的字段,用于描述二维码,支持多字段，拼接顺序与传入顺序有关
 * 
 * setDescrPrefixSuffix('描述内容前缀', '描述内容后缀');      // 二维码描述内容的前缀与后缀，最后会与传入的Excel字段名对应的值拼接在一起
 * 
 * setSavePath('./static/qrcode/');                         // 设置生成的二维码的保存路径,强烈建议调用它，会给你减少很多麻烦
 * 
 * setWidHeiMarFons(300, 10, 14);                           // 设置二维码宽高，外边距，底部描述内容字体大小
 * 
 * setLogo('./static/img/logo.png', 50, 50);                // 设置二维码中间的LOGO的路径，宽度，高度
 * 
 * setColorBack([0,0,0,0], [255,255,255,0]);                // 设置二维码的颜色与背景色，rgba颜色模式
 * 
 * createQRCode('001', "https://www.baidu.com", "百度一下"); // 生成单个二维码，文件名、二维码内容、二维码描述内容
 * 
 * getExcel(2, 0);                                           // 读取Excel表批量生成二维码,开始行数最小值为2，结束行数为0时表示不限制
 * 
 * 
 * 重点：支持连贯操作->->->->,所有函数在getExcel()或CreateQRCode()之前调用即可，连贯操作时这两个只能二选一·········
 */


// 设置二维码保存路径(默认'./static/qrcode/img/')
$batQR->setSavePath('./static/qrcode/img/')->setQRCodeField(["防伪码"])->setDescrField(["防伪验证码"])->getExcel(2, 0);

// 生成单个二维码
// $batQR->createQRCode('001', "https://www.baidu.com", "百度一下");
