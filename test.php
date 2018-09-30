<?php
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/***
 * @param 将二维数组格式化树形结构 数组元素格式[id, pid, ...]
 * @return array (保留原数组的索引)
 */
function array_to_tree($data, $p_field='pid', $s_field='id', $child='children') {
    $tree = [];
    foreach($data as $k=>$v) {
        $fils[$k] = $v[$s_field];       //把s_filed提取出来成一维数组，保持键不变（为了不需要在下面的循环中再次遍历）
    }
    $fils = array_flip($fils);          //键值互换 (这样就可以直接根据s_field的值获取到数组的键)
    foreach($data as $k => $v) {
        if ($v[$p_field] > 0) {
            $key = $fils[$v[$p_field]];             //提取p_field和s_field相同时的二维数组的键
            $data[$key][$child][$k] = &$data[$k];
        } else {
            !isset($data[$k][$child]) && ($data[$k][$child] = []);
            $tree[$k] = &$data[$k];
        }
    }
    return $tree;
}

$aa = array(
    '3' => array('id'=>2, 'pid'=>0, 'name'=>'数据库'),
    '5' => array('id'=>3, 'pid'=>0, 'name'=>'编程'),
    '4' => array('id'=>6, 'pid'=>3, 'name'=>'php'),
    '7' => array('id'=>7, 'pid'=>3, 'name'=>'java'),
    '9' => array('id'=>8, 'pid'=>'6', 'name'=>'后台'),
    '56' => array('id'=>9, 'pid'=>'8', 'name'=>'函数库'),
    '54' => array('id'=>10, 'pid'=>'9', 'name'=>'数组'),
    '70' => array('id'=>11, 'pid'=>'10', 'name'=>'排序'),
    '85' => array('id'=>12, 'pid'=>'11', 'name'=>'多维'),

);

$b = array_to_tree($aa, 'pid', 'id', 'children');
dump($b);
