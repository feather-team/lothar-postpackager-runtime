<?php
function __blade_make__($type, $__env, $id, $data = array()){
    list($namespace, $id) = explode(':', $id);
    $id = $namespace . '/' . $type . '/' . $id;

    $temp = explode('#', $id);
    $pid = null;

    if(count($temp) > 1 && $type == 'pagelet'){
        $id = $temp[0];
        $pid = $temp[1];
    }

    if($type == 'pagelet'){
        //做个hack，blade的extends，并非真正的继承，section的内容会提前执行，导致引入pagelet时，pagelet中的静态资源会被输出
        $data['FEATHER_PAGELET_INCLUDE'] = true;   
    }

    $data['__isRef'] = true;
    
    $content = $__env->make($id, $data, array_except(get_defined_vars(), array('__data', '__path')))->render();
    
    if($pid){
        $content = '<textarea style="display: none;" id="' . $pid . '">' . $content . '</textarea>';
    }

    echo $content;
}

function __blade_stripParentheses__($expression){
    if($expression[0] == '('){
        $expression = substr($expression, 1, -1);
    }

    return $expression;
}