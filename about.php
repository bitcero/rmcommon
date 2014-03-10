<?php
// $Id: blocks.php 952 2012-05-06 23:23:46Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','blocks');
include '../../include/cp_header.php';

RMTemplate::get()->header(); ?>

<style type="text/css">
    .thumbnail{
        width: 100%;
        height: 100px;
        border-radius: 0;
        background-size: 95%;
        background-repeat: no-repeat;
        background-position: 50% -5%;
        margin: 10px 0;
    }
    .thumbnail span{display: none;}
    .clearfix hr{
        margin: 10px 0;
    }
    .page-header{border: 0;}
</style>

<div class="row">
    <div class="col-xs-12">
        <h1 class="page-header">
            <img src="<?php echo RMCURL; ?>/images/cu-gear.png" class="pull-left" style="width: 80px; margin-right: 10px;">
            Bienvenido a Common Utilities <?php echo RMCVERSION; ?><br>
            <small>Gracias por instalar Common Utilities <?php echo RMCVERSION; ?>. Esta versión tiene muchas novedades para tí.</small>
        </h1>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="cu-box">
            <div class="box-content">
                <h2>Aspecto visual mejorado</h2>
                <p>El tema por defecto de Common Utilities (Two·P6) ha sido mejorado y le hemos otorgado un aspecto
                    mas moderno y limpio.</p>
                <hr>
                <h3 class="text-info">Elije una Combinación</h3>
                <p class="help-block">Seis nuevos esquemas de colores han sido incluidos en esta versión de Common Utilities, junto con la posibilidad de crear
                    tus propias combinaciones.</p>
                <div class="row">
                    <div class="col-xs-4  col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" data-file="theme-default.css" style="background-image: url('<?php echo RMCURL; ?>/themes/twop6/images/schemes/default.png');" title="Default Color Sheme" data-rel="tooltip">
                            <span>Default</span>
                        </a>
                        <strong>Default</strong>
                    </div>
                    <div class="clearfix visible-sm"><hr></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('<?php echo RMCURL; ?>/themes/twop6/images/schemes/cardio.png');" data-file="theme-cardio.css">
                            <span>Cardio</span>
                        </a>
                        <strong>Cardio</strong>
                    </div>
                    <div class="clearfix visible-sm visible-md visible-lg"><hr></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('<?php echo RMCURL; ?>/themes/twop6/images/schemes/neutral.png');" data-file="theme-neutral.css">
                            <span>Neutral Flavor</span>
                        </a>
                        <strong>Neutral Flavor</strong>
                    </div>
                    <div class="clearfix visible-xs visible-sm"><hr></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('<?php echo RMCURL; ?>/themes/twop6/images/schemes/veritas.png');" data-file="theme-veritas.css">
                            <span>Veritas</span>
                        </a>
                        <strong>Veritas</strong>
                    </div>
                    <div class="clearfix visible-sm visible-md visible-lg"><hr></div>
                    <div class="col-xs-4 col-sm-12 col-md-6 text-center">
                        <a href="#" class="twop6-scheme thumbnail" style="background-image: url('<?php echo RMCURL; ?>/themes/twop6/images/schemes/voyboy.png');" data-file="theme-voyboy.css">
                            <span>Voy-Boy</span>
                        </a>
                        <strong>Voy-Boy</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-sm-8">

        <h2 class="text-success text-center">Módulos mejores y modernos</h2>
        <p class="lead text-center">
            Common Utilities ofrece las herramientas necesarias para crear mejores módulos con características modernas. Su soporte MVC
            hace la creación de módulos más sencilla que nunca.
        </p>
        <hr>

        <h3>Nuevos Componentes</h3>
        <div class="row">

            <div class="col-sm-4">
                <h4 class="text-info">Gestor de Módulos</h4>
                <p class="help-block">
                    El <strong>Gestor de Módulos</strong> integrado en Common Utilities permite una administración más efectiva
                    de los módulos, tanto nativos de XOOPS como de CU.
                </p>
            </div>
            <div class="col-sm-4">
                <h4 class="text-info">Gestor de Bloques</h4>
                <p class="help-block">
                    El nuevo gestor de bloques te permite toda la libertada que hasta ahora no tenías. Crea posiciones, copias del mismo
                    bloque y adminístralos como nunca lo habías hecho.
                </p>
            </div>
            <div class="col-sm-4">
                <h4 class="text-info">Gestor de Imágenes</h4>
                <p class="help-block">
                    Cargar y administrar imágenes nunca ha sido tan fácil. Crea categorías para organizarlas y asigna diferentes tamaños;
                    Common Utilities se encarga de todo lo demás.
                </p>
            </div>

        </div>
        <hr>

        <h2 class="text-primary">Configuración mas Poderosa</h2>
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
                Hola
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h3>Actualizaciones inmediatas</h3>
                <p class="help-block">
                    ¡Se acabaron los sufrimientos para mantener actualizado el sistema! Con el sistema de actualizaciones de
                    Common Utilities, las actualizaciones están <strong class="text-success">a un solo click de distancia</strong>.
                </p>
            </div>
            <div class="col-md-6">
                <h3>Seguridad mejorada</h3>
                <p class="help-block">
                    Módulos con un mejor control sobre cada una de sus características mediante el nuevo sistema de seguridad
                    mejorado. Ahora es posible asignar <strong class="text-warning">permisos individuales a cada acción</strong>.
                </p>
            </div>
        </div>

    </div>

</div>

<?php
RMTemplate::get()->footer();