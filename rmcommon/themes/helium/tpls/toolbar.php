<!-- rmcommon toolbar -->
<?php if(RMTemplate::get()->get_toolbar()): ?>
    <nav id="he-toolbar" role="navigation">

        <div class="he-toolbar-icons">
            <ul class="tools">
                <?php foreach(RMTemplate::get()->get_toolbar() as $menu): ?>
                    <li<?php echo array_key_exists('location', $menu) && $menu['location']==RMCSUBLOCATION ? ' class = "active"' : ''; ?>>
                        <?php if(empty($menu['options'])): ?>
                            <a href="<?php echo $menu['link']; ?>" <?php echo $xoFunc->render_attributes( $menu['attributes'] ); ?>>
                                <?php echo $cuIcons->getIcon( $menu['icon'] ); ?>
                                <?php echo $menu['title']; ?>
                            </a>
                        <?php else: ?>
                            <div class="btn-group">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $menu['link']; ?>" <?php echo $xoFunc->render_attributes( $menu['attributes'] ); ?>>
                                    <?php echo $xoFunc->getIcon( $menu, false, 'tool-icon' ); ?>
                                    <?php echo $menu['title']; ?>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php foreach($menu['options'] as $sub): ?>
                                        <li>
                                            <a href="<?php echo $sub['url']; ?>" <?php echo $xoFunc->render_attributes( $menu['attributes'] ); ?>>
                                                <?php echo $xoFunc->getIcon( $sub ); ?> <?php echo $sub['caption']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
<?php endif; ?>
<!-- End rmcommon toolbar //-->