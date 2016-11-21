<?php
function blade_plugin_widget($expression){
    $expression = __blade_stripParentheses__($expression);
    $variable = __blade_variable__();

    return "
<?php 
{$variable} = array_merge(array_except(get_defined_vars(), array('__data', '__path')), __blade_info__('widget', {$expression}));
echo \$__env->make({$variable}['__id'], {$variable})->render();
?>
    ";
}