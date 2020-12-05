<?php
// $Id: blocks.php 952 2012-05-06 23:23:46Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require  dirname(dirname(__DIR__)) . '/include/cp_header.php';
$common->location = 'about';

RMTemplate::getInstance()->add_style('about.min.css', 'rmcommon', ['id' => 'about-css']);

RMTemplate::get()->header(); ?>

<div class="cu-box welcome-box">
    <div class="panel-body">
        <div class="media">
            <div class="media-left">
                <?php echo $cuIcons->getIcon('svg-rmcommon-rmcommon', ['class' => 'media-object rmcommon-logo']); ?>
            </div>
            <div class="media-body">
                <h1 class="media-heading">
                    Welcome to Common Utilities
                </h1>
                <p class="lead">
                    Thanks for using <strong><?php echo RMModules::get_module_version('rmcommon'); ?></strong> for
                    <a href="http://xoops.org/modules/news/article.php?storyid=6762" target="_blank">XOOPS 2.5.8</a>.
                    This version has a lot of improvements and new features to make your work more productive and pleasing.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="color-bar">
    <ul>
        <li class="bg-red"></li>
        <li class="bg-pink"></li>
        <li class="bg-purple"></li>
        <li class="bg-deep-purple"></li>
        <li class="bg-indigo"></li>
        <li class="bg-blue"></li>
        <li class="bg-light-blue"></li>
        <li class="bg-cyan"></li>
        <li class="bg-teal"></li>
        <li class="bg-green"></li>
        <li class="bg-light-green"></li>
        <li class="bg-lime"></li>
        <li class="bg-yellow"></li>
        <li class="bg-amber"></li>
        <li class="bg-orange"></li>
        <li class="bg-deep-orange"></li>
        <li class="bg-brown"></li>
        <li class="bg-grey"></li>
        <li class="bg-blue-grey"></li>
        <li class="bg-midnight"></li>
        <li class="bg-primary"></li>
        <li class="bg-success"></li>
        <li class="bg-info"></li>
        <li class="bg-warning"></li>
        <li class="bg-danger"></li>
    </ul>
</div>

<div class="page-header text-center">
    <h2>Introducing Helium: the new GUI for Common Utilities</h2>
    <p>More friendly, more impressive and colorful</p>
</div>

<div id="introduce-helium">
    <div class="info">
        <h3>A fully new design with great improvements and additions.</h3>
        <p>
            Never was so easy to use and take advantage of <a href="https://www.xoops.org" target="_blank">XOOPS</a> and
            <a href="http://www.rmcommon.com" target="_blank">Common Utilities.</a> All the power, modularity and features of both are available for you with
            the best appearance ever.
        </p>
        <p>
            Doesn't mind if you are a programmer,a developer or a final user; Helium will provide you the best user experience and a bunch of cohesive graphical
            elements to use in your modules: <em>fully responsivity</em>, <em>easy to use widgets and GUI elements</em> and <em>a well designed environment</em>.
        </p>
    </div>
    <div class="screenshot">
        &nbsp;
    </div>
</div>
<div id="helium-features" class="row">
    <div class="col-sm-6 col-md-3">
        <?php echo $cuIcons->getIcon('svg-rmcommon-responsive', ['class' => 'text-amber']); ?>
        <h4 class="text-amber">Fully Responsive</h4>
        <p>
            Helium offers a fully responsive environment, easey to integrate with your modules.
        </p>
    </div>
    <div class="col-sm-6 col-md-3">
        <?php echo $cuIcons->getIcon('svg-rmcommon-blocks', ['class' => 'text-light-green']); ?>
        <h4 class="text-light-green">Admin Widgets</h4>
        <p>
            Provide more information with a good looking widgets resuable in all your modules.
        </p>
    </div>
    <div class="clearfix visible-sm"></div>
    <div class="col-sm-6 col-md-3">
        <?php echo $cuIcons->getIcon('svg-rmcommon-heart', ['class' => 'text-pink']); ?>
        <h4 class="text-pink">More Friendly</h4>
        <p>
            Find the right element in Helium is easier than ever: try the new sidebar and the quick modules search.
        </p>
    </div>
    <div class="col-sm-6 col-md-3">
        <?php echo $cuIcons->getIcon('svg-rmcommon-vector', ['class' => 'text-light-blue']); ?>
        <h4 class="text-light-blue">SVG Icons</h4>
        <p>
            Helium integrate the use of SVG as icons: infinite resizing without loss in quality.
        </p>
    </div>
</div>

<div class="panel">
    <div class="panel-body">

        <div class="row feature-info">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe width="420" height="315" src="https://www.youtube.com/embed/BvRQqXJs7Zk?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen class="embed-responsive-item"></iframe>
                </div>
            </div>
        </div>

        <div class="row feature-info">
            <div class="screenshot">
                <img src="https://raw.githubusercontent.com/bitcero/rmcommon/gh-pages/images/md-editor.png" alt="Markdown Editor">
            </div>
            <div class="info">
                <h3>
                    Multiple editors available
                    <small>including the new <strong>Markdown Editor</strong></small>
                </h3>
                <p>
                    Common Utilities includes awesome text editors that will fit your needs and preferences. You can choose between visual, markdown, HTML or plain
                    text editors.
                </p>
            </div>
        </div>

        <div class="row feature-info">
            <div class="screenshot">
                <img src="https://raw.githubusercontent.com/bitcero/rmcommon/gh-pages/images/svg-icons.svg" alt="SVG icons">
            </div>
            <div class="info">
                <h3>
                    SVG Icons
                    <small>icons that not loose resolution</small>
                </h3>
                <p>
                    Now, with svg icons support, you can resize and use the icons in any desired size without worry about reolution. Vectorial icons can be scaled without limits.
                </p>
            </div>
        </div>

        <div class="row feature-info">
            <div class="screenshot">
                <img src="https://raw.githubusercontent.com/bitcero/rmcommon/gh-pages/images/inline-help.png" alt="Inline Help">
            </div>
            <div class="info">
                <h3>
                    Inline Help
                    <small>Contextual help easy to be included</small>
                </h3>
                <p>
                    Common Utilities offers an inline help system that can be easily integrated with any component. You only need to provide a valid URL (local or remote)
                    and the help will be available.
                </p>
            </div>
        </div>

        <div class="row feature-info">
            <div class="screenshot">
                <img src="https://raw.githubusercontent.com/bitcero/rmcommon/gh-pages/images/updates.png" alt="Automatic Updates">
            </div>
            <div class="info">
                <h3>
                    Automatic Updates
                    <small>Maintain your system updated with a single click</small>
                </h3>
                <p>
                    Common Utilities and their modules can be updated easily and with a single click. Updates are automatic and delivered directly on the dashboard.
                </p>
            </div>
        </div>

    </div>
</div>

<div class="panel">
    <div class="panel-body">
        <div class="media bug-report">
            <div class="media-left">
                <?php echo $cuIcons->getIcon('svg-rmcommon-bug', ['class' => 'media-object text-danger']); ?>
            </div>
            <div class="media-body">
                <h3 class="media-heading">
                    Do you found a bug?
                </h3>
                <p>
                    Report any bug directly on the <a href="https://github.com/bitcero/rmcommon/">Common Utilities project page</a>. Remember to include all details about the bug, including system details and
                    versions of PHP, MySQL, etc.
                </p>
                <a href="https://github.com/bitcero/rmcommon/issues" target="_blank" class="btn btn-danger">Report Now!</a>
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
