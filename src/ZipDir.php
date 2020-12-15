<?php
namespace Bearki;

use ZipArchive;

class ZipDir
{
    /**
     * 总接口
     * @param $dir_path 需要压缩的目录地址（绝对路径）
     * @param $zipName 需要生成的zip文件名（绝对路径）
     */
    public function zip($dir_path, $zipName){
        $relationArr = [$dir_path => [
            'originName' => $dir_path,
            'is_dir'     => true,
            'children'   => []
        ]];
        $this->modifiyFileName($dir_path, $relationArr[$dir_path]['children']);
        $zip = new ZipArchive();
        $zip->open($zipName,ZipArchive::CREATE);
        $this->zipDir(array_keys($relationArr)[0],'',$zip,array_values($relationArr)[0]['children']);
        $zip->close();
        $this->restoreFileName(array_keys($relationArr)[0],array_values($relationArr)[0]['children']);
    }
    
    /**
     * 递归添加文件进入zip
     * @param $real_path 在需要压缩的本地的目录
     * @param $zip_path zip里面的相对目录
     * @param $zip ZipArchive对象
     * @param $relationArr 目录的命名关系
     */
    public function zipDir($real_path, $zip_path, &$zip, $relationArr){
        $sub_zip_path = empty($zip_path) ? '' : $zip_path .'/';
        if (is_dir($real_path)) {
            foreach ($relationArr as $k => $v) {
                if ($v['is_dir']) { //是文件夹
                    $zip->addEmptyDir($sub_zip_path.$v['originName']);
                    $this->zipDir($real_path.'/'.$k, $sub_zip_path.$v['originName'], $zip, $v['children']);
                } else { //不是文件夹
                    $zip->addFile($real_path.'/'.$k, $sub_zip_path.$k);
                    $zip->deleteName($sub_zip_path.$v['originName']);
                    $zip->renameName($sub_zip_path.$k, $sub_zip_path.$v['originName']);
                }
            }
        }
    }
    
    /**
     * 递归将目录的文件名更改为随机不重复编号，然后保存原名和编号关系
     * @param $path 本地目录地址
     * @param $relationArr 关系数组
     * @return bool
     */
    public function modifiyFileName($path, &$relationArr){
        if(!is_dir($path) || !is_array($relationArr)) {
            return false;
        }
        if ($dh = opendir($path)) {
            $count = 0;
            while (($file = readdir($dh)) !== false) {
                if (in_array($file, ['.', '..', null])) {
                    continue; //无效文件，重来
                }
                if (is_dir($path.'/'.$file)) {
                    $newName = md5(rand(0,99999).rand(0,99999).rand(0,99999).microtime().'dir'.$count);
                    $relationArr[$newName] = [
                        'originName' => iconv('GBK','UTF-8',$file),
                        'is_dir' => true,
                        'children' => []
                    ];
                    rename($path.'/'.$file, $path.'/'.$newName);
                    $this->modifiyFileName($path.'/'.$newName,$relationArr[$newName]['children']);
                    $count++;
                } else {
                    $extension = strchr($file,'.');
                    $newName = md5(rand(0,99999).rand(0,99999).rand(0,99999).microtime().'file'.$count);
                    $relationArr[$newName.$extension] = [
                        'originName' => iconv('GBK','UTF-8',$file),
                        'is_dir' => false,
                        'children' => []
                    ];
                    rename($path.'/'.$file, $path.'/'.$newName.$extension);
                    $count++;
                }
            }
        }
    }
    
    /**
     * 根据关系数组，将本地目录的文件名称还原成原文件名
     * @param $path 本地目录地址
     * @param $relationArr 关系数组
     */
    public function restoreFileName($path, $relationArr){
        foreach ($relationArr as $k=>$v) {
            if (!empty($v['children'])) {
                $this->restoreFileName($path.'/'.$k,$v['children']);
                rename($path.'/'.$k,iconv('UTF-8','GBK', $path.'/'.$v['originName']));
            } else {
                rename($path.'/'.$k,iconv('UTF-8','GBK', $path.'/'.$v['originName']));
            }
        }
    }

    /**
     * 删除文件夹及其文件
     *
     * @param string $dir 文件夹地址
     * @return bool
     */
    public function deldir(string $dir)
    {
        // 打开文件夹
        $dh = opendir($dir);
        //先删除目录下的文件：
        while ($file = readdir($dh)) {
            if($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    // 递归该方法
                    return $this->deldir($fullpath);
                }
            }
        }
        // 关闭打开的文件夹
        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
           return true;
        } else {
           return false;
        }
     }
}