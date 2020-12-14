<?php
/**
 * Excel批量生成QR二维码
 * @version 1.0.0 2020-12-15 02:00
 * @author Bearki <508023077@qq.com>
 * @package Bearke
 * @link https://github.com/phpoffice/phpspreadsheet
 * @link https://github.com/endroid\qrcode;
 */

namespace Bearki;

use Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BatchQRCode
{
    // 要导入的Excel文件路径
    private $excelPath = '';

    // 二维码内容前缀
    private $qrcodePrefix = '';
    // 生成二维码的Excel表头
    private $qrcodeArr = [];
    // 二维码内容后缀
    private $qrcodeSuffix = '';

    // 二维码描述内容前缀
    private $describePrefix = '';
    // 描述二维码的Excel表头
    private $describeArr = [];
    // 二维码描述内容后缀
    private $describeSuffix = '';

    // 二维码宽高（宽==高）
    private $imgWidthHeight = 300;
    // 二维码外边距
    private $imgMargin = 10;
    // 字体大小
    private $fontSize = 14;

    // 二维码颜色
    private $qrcodeColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
    // 二维码背景色
    private $qrcodeBackground = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];

    // 二维码LOGO文件路径
    private $logoPath = '';
    // 二维码LOGO宽度
    private $logoWidth = 50;
    // 二维码LGO高度
    private $logoHeight = 50;

    // 二维码保存路径
    private $savePath = './static/qrcode/img/';

    /**
     * 构造函数
     *
     * @param string $excelPath excel文件路径
     */
    public function __construct(string $excelPath = '')
    {
      if (!empty($excelPath)) {
        $this->setExcelPath($excelPath);
      }
    }

    /**
     * 设置Excel文件所在路径
     *
     * @param string $excelPath excel文件所在路径
     * @return object
     */
    public function setExcelPath(string $excelPath)
    {
        // 截取最后一个字符，判断是x还是s
        $suffix_x_s = strtolower(substr($excelPath, -1, 1)); // 转换字符为小写
        if ($suffix_x_s === 'x') {
            // 判断文件是否是xlsx
            $suffix = strtolower(substr($excelPath, -5, 5));
            if ($suffix != ".xlsx") {
                // 抛出文件类型错误
                throw new Exception('Error: Wrong file type, only Excel files in " xls" and "xlsx" formats are supported');
            }
        } else if ($suffix_x_s === 's') {
            // 判断文件是否是xls
            $suffix = strtolower(substr($excelPath, -4, 4));
            if ($suffix != ".xls") {
                // 抛出文件类型错误
                throw new Exception('Error: Wrong file type, only Excel files in " xls" and "xlsx" formats are supported');
            }
        } else {
          // 抛出文件类型错误
          throw new Exception('Error: Wrong file type, only Excel files in " xls" and "xlsx" formats are supported');
        }
        // 判断文件是否存在
        if (file_exists($excelPath)) {
          // 设置Excel文件路径
          $this->excelPath = $excelPath;
          return $this;
        } else {
            // 抛出文件不存在
            throw new Exception('Error: file does not exist');
        }
    }

    /**
     * 设置指定范围字段为二维码内容
     *
     * @param array $fieldList 支持多字段，顺序影响拼接
     * @return object
     */
    public function setQRCodeField(array $fieldList)
    {
        $this->qrcodeArr = $fieldList;
        return $this;
    }

    /**
     * 设置二维码内容的前缀与后缀
     *
     * @param string $prefix 二维码内容前缀
     * @param string $suffix 二维码内容后缀
     * @return object
     */
    public function setQRCodePrefixSuffix(string $prefix, string $suffix)
    {
        $this->qrcodePrefix = $prefix;
        $this->qrcodeSuffix = $suffix;
        return $this;
    }

    /**
     * 设置指定范围字段为二维码下方描述内容
     *
     * @param array $fieldList 支持多字段，顺序影响拼接
     * @return object
     */
    public function setDescrField(array $fieldList)
    {
        $this->describeArr = $fieldList;
        return $this;
    }

    /**
     * 设置二维码下方描述内容的前缀与后缀
     *
     * @param string $prefix
     * @param string $suffix
     * @return object
     */
    public function setDescrPrefixSuffix(string $prefix, string $suffix)
    {
        $this->describePrefix = $prefix;
        $this->describeSuffix = $suffix;
        return $this;
    }

    /**
     * 设置二维码图片保存的路径
     *
     * @param string $path 仅支持文件夹
     * @return object
     */
    public function setSavePath(string $path)
    {
        // 判断目录尾部是否有斜杠
        if (substr($path, -1, 1) === "/") {
          $path = substr($path, 0, strlen($path) - 1);
        }
        // 判断是否是文件
        if (is_file($path)) {
            throw new Exception('Error: This path is a file, not a directory. Please pass in the correct directory path');
        }
        // // 判断目录是否存在
        if (!is_dir($path)) {
            // 创建目录
            $bool = mkdir($path, 0766, true);
            if (!$bool) {
                throw new Exception('Error: The directory passed in does not exist and the creation of the directory failed');
            }
        }
        // 配置二维码图片保存路径
        $this->savePath = $path . '/';
        return $this;
    }

    /**
     * 设置二维码图片宽高和外边距及字体大小
     *
     * @param integer $widthHeight 图片宽度
     * @param integer $fontSize 字体大小
     * @return object
     */
    public function setWidHeiMarFons(int $widthHeight, int $margin, int $fontSize)
    {
        $this->imgWidthHeight = $widthHeight;
        $this->imgMargin = $margin;
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * 设置二维码LOGO
     *
     * @param string $path LOGO路径
     * @param integer $width LOGO宽度
     * @param integer $height LOGO高度
     * @return object
     */
    public function setLogo(string $path, int $width, int $height)
    {
        $this->logoPath = $path;
        $this->logoWidth = $width;
        $this->logoHeight = $height;
        return $this;
    }

    /**
     * 设置二维码颜色及背景色
     *
     * @param array $color [0, 0, 0, 0] rgba颜色，透明度0-1，0表不透明
     * @param array $background [0, 0, 0, 0] rgba颜色，透明度0-1，0表不透明
     * @return object
     */
    public function setColorBack(array $color, array $background)
    {
        if (!empty($color)) {
            $this->qrcodeColor["r"] = $color[0];
            $this->qrcodeColor["g"] = $color[1];
            $this->qrcodeColor["b"] = $color[2];
            $this->qrcodeColor["a"] = $color[3];
        }
        if (!empty($background)) {
            $this->qrcodeBackground["r"] = $background[0];
            $this->qrcodeBackground["g"] = $background[1];
            $this->qrcodeBackground["b"] = $background[2];
            $this->qrcodeBackground["a"] = $background[3];
        }
        return $this;
    }

    /**
     * 生成二维码图片
     *
     * @param string $fileName 文件名，如：1，2，3，4·····
     * @param string $content 二维码内容，用于生成二维码
     * @param string $describe 二维码描述内容
     * @return bool|string 返回文件地址或false
     */
    public function createQRCode(string $fileName, string $content, string $describe)
    {
        try {
            // 实例化二维码生成器，并传入内容
            $qrCode = new QrCode($this->qrcodePrefix . $content . $this->qrcodeSuffix);

            // 内容区域宽高,默认为300
            $qrCode->setSize($this->imgWidthHeight);
            // 外边距大小
            $qrCode->setMargin($this->imgMargin);
            // 设置编码
            $qrCode->setEncoding('UTF-8');
            // 设置容错等级
            $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
            // 设置二维码颜色,默认为黑色
            $qrCode->setForegroundColor($this->qrcodeColor);
            // 设置二维码背景色,默认为白色
            $qrCode->setBackgroundColor($this->qrcodeBackground);

            // 判断是否需要在二维码下方加描述
            $describe = $this->describePrefix . $describe . $this->describeSuffix;
            if (!empty($describe)) {
                // 设置二维码下方的文字
                $qrCode->setLabel($describe, $this->fontSize, null, LabelAlignment::CENTER());
            }

            // 判断是否需要给二维码加LOGO
            if (!empty($this->logoPath)) {
                // 设置LGO图片路径
                $qrCode->setLogoPath($this->logoPath);
                // 设置LOGO宽高
                $qrCode->setLogoSize($this->logoWidth, $this->logoHeight);
            }
            
            // 启用内置的验证读取器(默认情况下禁用)
            $qrCode->setValidateResult(false);

            // 二维码存在本地
            $savePath = $this->savePath . $fileName . '.png';
            $qrCode->writeFile($savePath);
            // 返回文件保存地址
            return $savePath;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 读取Excel生成二维码
     *
     * @param integer $start 开始行数,0表不限制
     * @param integer $end 接收行数
     * @return bool
     */
    public function getExcel(int $start = 0, int $end = 0)
    {
        // 限制开始行数最小为2
        $start = max(2, $start);
        //根据类型创建合适的读取器对象  这里写死了
        $objread = IOFactory::createreader('Xlsx');
        // 读取Excel文件
        $objspreadsheet = $objread->load($this->excelPath);
        // 获取第一张表
        $objworksheet = $objspreadsheet->getsheet(0);
        // 将数据转换为数组
        $data = $objworksheet->toarray();
        // 取得二维码内容所在列
        $contCol = [];
        foreach ($this->qrcodeArr as $name) {
            foreach ($data[0] as $key => $val) {
                if ($name == $val) {
                    $contCol[] = $key;
                }
            }
        }
        // 取得描述内容所在列
        $descrCol = [];
        foreach ($this->describeArr as $name) {
            foreach ($data[0] as $key => $val) {
                if ($name == $val) {
                    $descrCol[] = $key;
                }
            }
        }
        // 开始生成二维码
        foreach ($data as $key => $val) {
            if ($start == $end) {
                return true;
            } else {
                $content = '';
                foreach ($contCol as $col) {
                    $content .= $val[$col];
                }
                $describe = '';
                foreach ($descrCol as $col) {
                    $describe .= $val[$col];
                }
                $this->createQRCode((string)$start, $content, $describe);
                $end++;
            }
        }
        return false;
    }
}