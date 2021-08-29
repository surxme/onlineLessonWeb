<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 12:35
 */
namespace app\admin\view;

class LayuiDragFileUpload extends \LayuiHtmlBuilderInterface
{
    private $_file_type = 'file'; //默认所有格式
    public function __construct($file_type = 'file')
    {
        $this->_file_type = $file_type;
    }

    /**
     * 返回传递给试图的数据数组
     * @return array
     */
    public function getData()
    {
        return [
            'file_type' => $this->_file_type,
        ];
    }
}