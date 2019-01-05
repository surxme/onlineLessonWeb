<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/3
 * Time: 10:54
 */
namespace app\admin\view;
class LayuiHtmlBuilder
{
    public static function build($builders){
        foreach($builders as $builder){
            /**
             * @var HtmlBuilderInterface $builder
             */
            $new_data = [];
            foreach($builder->getData() as $k => $v){
                $new_data[$k] = $v;
            }

            extract($new_data);
            unset($new_data);

            $view = $builder->getClassName();

            $view_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$view.'.php';

            include "{$view_file}";
        }
    }

}