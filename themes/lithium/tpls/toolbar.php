<!-- rmcommon toolbar -->
<?php if (RMTemplate::get()->get_toolbar()): ?>
    <nav id="he-toolbar" role="navigation">

        <div class="he-toolbar-icons">
            <ul class="tools">
                <?php foreach (RMTemplate::getInstance()->get_toolbar() as $menu): ?><li<?php echo array_key_exists('location', $menu) && RMCSUBLOCATION == $menu['location'] ? ' class = "active"' : ''; ?>>
                            <a href="<?php echo $menu['link']; ?>" <?php echo array_key_exists('attributes', $menu) ? $xoFunc->render_attributes($menu['attributes']) : ''; ?>>
                                <?php echo $cuIcons->getIcon($menu['icon']); ?>
                                <?php echo $menu['title']; ?>
                            </a>
                    </li><?php endforeach; ?>
            </ul>
        </div>
    </nav>
<?php endif; ?>
<!-- End rmcommon toolbar //-->