<?php
function blade_plugin_widget($expression){
    $expression = __blade_stripParentheses__($expression);
    return "<?php __blade_make__('widget', \$__env, {$expression}, array_except(get_defined_vars(), array('__data', '__path')));?>";
}