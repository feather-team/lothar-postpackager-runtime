<?php
function blade_plugin_pagelet($expression){
    $expression = __blade_stripParentheses__($expression);
    $variable = __blade_variable__();

    return "
<?php 
{$variable} = array_merge(array_except(get_defined_vars(), array('__data', '__path')), __blade_info__('pagelet', {$expression}));
if(array_key_exists('__pid', {$variable})){
    echo '<textarea style=\"display: none;\" id=\"' . {$variable}['__pid'] . '\">';
    echo \$__env->make({$variable}['__id'], {$variable})->render();
    echo '</textarea>';
}else{
    echo \$__env->make({$variable}['__id'], {$variable})->render();
}
?>
    ";
}