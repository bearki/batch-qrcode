<?php
/**
 * Excel批量生成QR二维码
 * @version 1.0.0
 * @author Bearke <508023077@qq.com>
 * @package Bearke
 * @link https://github.com/phpoffice/phpspreadsheet
 * @link https://github.com/endroid\qrcode;
 */

namespace Bearke;

use Exception;

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
    // 二维码内容前缀
    private $describePrefix = '';
    // 描述二维码的Excel表头
    private $describeArr = [];
    // 二维码内容前缀
    private $describeSuffix = '';
    // 图片整体宽度
    private $imgWidth = 40;
    // 图片整体高度
    private $imgHeight = 40;
    // 字体大小
    private $fontSize = 14;
    // 二维码保存路径
    private $savePath = './qrcode/img/';

    /**
     * 构造函数
     *
     * @param string $excelPath excel文件路径
     */
    public function __construct(string $excelPath = '')
    {
      if (!empty($excelPath)) {
        $this->indExcelPath($excelPath);
      }
    }

    /**
     * 设置Excel文件所在路径
     *
     * @param string $excelPath excel文件所在路径
     * @return bool 文件是否引入成功
     */
    public function indExcelPath(string $excelPath)
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
          return true;
        }
        return false;
    }

    /**
     * 设置指定范围字段为二维码内容
     *
     * @param array $fieldList 支持多字段，顺序影响拼接
     * @return void
     */
    public function selectFieldCodeContent(array $fieldList)
    {
        $this->qrcodeArr = $fieldList;
        return;
    }

    /**
     * 设置二维码内容的前缀与后缀
     *
     * @param string $prefix 二维码内容前缀
     * @param string $suffix 二维码内容后缀
     * @return void
     */
    public function setContentPrefixAndSuffix(string $prefix, string $suffix)
    {
        $this->qrcodePrefix = $prefix;
        $this->qrcodeSuffix = $suffix;
        return;
    }

    /**
     * 设置指定范围字段为二维码下方描述内容
     *
     * @param array $fieldList 支持多字段，顺序影响拼接
     * @return void
     */
    public function selectFieldCodeDescribe(array $fieldList)
    {
        $this->describeArr = $fieldList;
        return;
    }

    /**
     * 设置二维码下方描述内容的前缀与后缀
     *
     * @param string $prefix
     * @param string $suffix
     * @return void
     */
    public function setDescribePrefixAndSuffix(string $prefix, string $suffix)
    {
        $this->describePrefix = $prefix;
        $this->describeSuffix = $suffix;
        return;
    }

    /**
     * 设置二维码图片保存的文件夹
     *
     * @param string $path 仅支持文件夹
     * @return void
     */
    public function setQRCodeImgSavePath(string $path)
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
        return;
    }

    /**
     * 设置二维码图片宽高及字体大小
     *
     * @param float $width 图片宽度
     * @param float $height 图片高度
     * @param integer $fontSize 字体大小
     * @return void
     */
    public function setCodeImgeFontSize(float $width, float $height, int $fontSize)
    {

    }

    /**
     * 生成二维码图片
     *
     * @param string $fileName 文件名，如：1，2，3，4·····
     * @param string $content 二维码内容，用于生成二维码
     * @param string $describe 二维码描述内容
     * @return bool
     */
    public function createQRCodeImg(string $fileName, string $content, string $describe)
    {

    }

    /**
     * 读取Excel生成二维码
     *
     * @param integer $start 开始行数,0表不限制
     * @param integer $end 接收行数
     * @return bool
     */
    public function getExcelCreateQRCode(int $start = 0, int $end = 0)
    {

    }
}