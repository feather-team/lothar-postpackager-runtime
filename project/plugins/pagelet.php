<?php
function blade_plugin_pagelet($expression){
    $expression = __blade_stripParentheses__($expression);
    return "<?php __blade_make__('pagelet', \$__env, {$expression}, array_except(get_defined_vars(), array('__data', '__path')));?>";
}