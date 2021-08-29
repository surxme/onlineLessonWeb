<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/3
 * Time: 10:59
 */

abstract class LayuiHtmlBuilderInterface
{

    /**
     * 返回传递给视图的数据数组
     * @return array
     */
    public abstract function getData();

    public function getClassName(){
        return lcfirst(get_called_class());
    }

}