<?php

class Twop6RmcommonPreload
{
    public function eventRmcommonXoopsCommonEnd(){

        $w = RMHttpRequest::get( 'twop6', 'string', '' );

        if ($w == '') return;

        if ($w == 'colortest') {

            include_once XOOPS_ROOT_PATH .'/include/cp_functions.php';

            RMTemplate::get()->header();

            require RMCPATH .'/themes/twop6/include/test-color.php';

            RMTemplate::get()->footer();
            die();
        }

        if ($w == 'about') {

            include_once XOOPS_ROOT_PATH .'/include/cp_functions.php';

            RMTemplate::get()->header();

            require RMCPATH .'/themes/twop6/include/about.php';

            RMTemplate::get()->footer();
            die();

        }

    }
}
