<?php
function smarty_function_breadcrumb($options, $tpl)
{
    $bc = RMBreadCrumb::get();

    return $bc->render();
}
