<?php
// $Id: blocks.php 952 2012-05-06 23:23:46Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','about');
include '../../include/cp_header.php';

RMTemplate::get()->header(); ?>

<div class="cu-box box-solid box-red">
    <div class="panel-body">
        <div class="media">
            <div class="media-left">
                <img src="<?php echo RMCURL; ?>/images/cu-gear.png" class="media-object" style="width: 80px;">
            </div>
            <div class="media-body">
                <h1 class="media-heading">
                    Welcome to Common Utilities
                </h1>
                <p class="lead">
                    Thanks for using <strong><?php echo RMModules::get_module_version('rmcommon'); ?></strong>.
                    This version has a lot of improvements and new features to make your work more productive and pleasing.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="cu-box">
            <div class="box-content">
                <h2>Improved GUI</h2>
                <img src="//www.xoopsmexico.net/about/rmcommon/schemes/all.jpg" class="img-responsive" style="margin-bottom: 10px;" alt="Improved GUI">
                <p>The default theme of Common Utilities (Two·P6) has been improved and we have gived to it a better, modern and clean
                    appearance.</p>
                <hr>
                <h3 class="text-info">Choose a Color Scheme</h3>
                <p class="help-block">Six new color schemes has been included in this version of Two·Six, and you can
                    <a href="./?twop6=about">create your own schemes</a>.</p>
                <div class="row">
                    <div class="col-xs-4  col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" data-file="theme-default.css" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/default.png');" title="Default Color Sheme" data-rel="tooltip">
                            <span>Default</span>
                        </a>
                        <strong>Default</strong>
                    </div>
                    <div class="clearfix visible-sm"></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/cardio.png');" data-file="theme-cardio.css">
                            <span>Cardio</span>
                        </a>
                        <strong>Cardio</strong>
                    </div>
                    <div class="clearfix visible-sm visible-md visible-lg"></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/neutral.png');" data-file="theme-neutral.css">
                            <span>Neutral Flavor</span>
                        </a>
                        <strong>Neutral Flavor</strong>
                    </div>
                    <div class="clearfix visible-xs visible-sm"></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/veritas.png');" data-file="theme-veritas.css">
                            <span>Veritas</span>
                        </a>
                        <strong>Veritas</strong>
                    </div>
                    <div class="clearfix visible-sm visible-md visible-lg"></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/voyboy.png');" data-file="theme-voyboy.css">
                            <span>Voy-Boy</span>
                        </a>
                        <strong>Voy-Boy</strong>
                    </div>
                    <div class="clearfix visible-sm visible-md visible-sm"></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('//www.xoopsmexico.net/about/rmcommon/schemes/thewinner.png');" data-file="theme-winner.css">
                            <span>Voy-Boy</span>
                        </a>
                        <strong>Voy-Boy</strong>
                    </div>
                </div>

                <hr>

            </div>
        </div>

    </div>

    <div class="col-sm-8">

        <h2 class="text-success text-center">Better and modern modules</h2>
        <p class="lead text-center">
            Common Utilities provide the neccessary tools to make better modules with modern features.
            As if this were not enough, it's integrated MVC support allows you to create modules faster and easily as ever.
        </p>
        <hr>

        <h3>New Components</h3>
        <div class="row">

            <div class="col-sm-4">
                <h4 class="text-info">Modules Manager</h4>
                <p class="help-block">
                    The new <strong>Modules Manager</strong> of Common Utilities allows a more effective control of
                    modules.
                </p>
            </div>
            <div class="col-sm-4">
                <h4 class="text-info">Blocks Manager</h4>
                <p class="help-block">
                    The Common Utilities block manager provide you a full freedom to manage your blocks: create new
                    blocks positions, add blocks and a super comfortable interface.
                </p>
            </div>
            <div class="col-sm-4">
                <h4 class="text-info">Images Manager</h4>
                <p class="help-block">
                    Upload and manage images never has been more easy.
                    Organize them by categories and assign different sizes for each one. Common Utilities make the hard work
                    for you.
                </p>
            </div>

        </div>
        <hr>

        <div class="panel panel-default">
            <div class="panel-body">
                <h3 class="text-center" style="margin-bottom: 20px;">Markdown editor and full screen edition</h3>
                <div class="row">
                    <div class="col-sm-5">
                        <a href="settings.php"><img src="//www.xoopsmexico.net/about/rmcommon/md.png" alt="Nuevo editor Markdown" class="img-responsive img-thumbnail"></a>
                    </div>
                    <div class="col-sm-7">
                        <p>Common Utilities now includes a <strong>Markdown editor</strong> (Github Flavored), with highlight syntax,
                            available to use in any module or component.</p>
                        <hr>
                        <p>
                            Additionally, all editors (Visual, HTMl, Simple, XoopsCode and Markdown) now includes a great feature:
                            <strong>Full screen edition</strong>. Work without distractions and be more productive.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h2 class="text-primary text-center">More Powerful Configuration</h2><br>
        <div class="row">
            <div class="col-sm-6">
                <p>El nuevo sistema de confgiuración de Common Utilities (<em>tambien disponible para módulos nativos de XOOPS</em>), permite
                    una configuración mas poderosa de los módulos al proporcionar nuevos tipos de campos y la modificación de los
                    valores en línea.</p>
                <p>
                    Con el soporte para categorías de opciones de configuración y un diseño mucho mas limpio, ajustar el comportamiento
                    de los módulos para adaptarlos a tus necesidades es mucho mas fácil.
                </p>
            </div>
            <div class="col-sm-6">
                <a href="settings.php"><img src="//www.xoopsmexico.net/about/rmcommon/settings.png" alt="Nuevo sistema de confgiuración" class="img-responsive img-thumbnail"></a>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3>1-Click Updates</h3>
                        <img src="//www.xoopsmexico.net/about/rmcommon/updates.png" alt="Actualizaciones automáticas" class="img-responsive img-thumbnail">
                        <p class="help-block">
                            ¡Se acabaron los sufrimientos para mantener actualizado el sistema! Con el sistema de actualizaciones de
                            Common Utilities, las actualizaciones están <strong class="text-success">a un solo click de distancia</strong>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Improved Permissions</h3>
                <img src="//www.xoopsmexico.net/about/rmcommon/security.png" alt="Seguridad mejorada" class="img-responsive img-thumbnail">
                <p class="help-block">
                    Módulos con un mejor control sobre cada una de sus características mediante el nuevo sistema de seguridad
                    mejorado. Ahora es posible asignar <strong class="text-warning">permisos individuales a cada acción</strong>.
                </p>
            </div>
        </div>

    </div>

</div>

<style>
    body{
        background: #F0F1F4;
    }
</style>

<?php
RMTemplate::get()->footer();
