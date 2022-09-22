<!-- rmcommon toolbar -->
<?php if (RMTemplate::get()->get_toolbar()): ?>
    <nav id="li-toolbar" role="navigation">
        <div class="li-toolbar-icons">
            <ul class="tools d-flex justify-content-end align-items-center">
              <?php foreach (RMTemplate::getInstance()->get_toolbar() as $menu): ?>
                  <li class="list-inline-item <?php echo array_key_exists('location', $menu) && RMCSUBLOCATION == $menu['location'] ? 'active' : ''; ?>">
                      <a
                          href="<?php echo $menu['link']; ?>"
                          class="d-flex align-items-center"
                        <?php echo array_key_exists('attributes', $menu) ? $xoFunc->render_attributes($menu['attributes']) : ''; ?>
                      >
                        <?php echo $common->icons()->getIcon($menu['icon'], [], false); ?>
                          <span class="caption"><?php echo $menu['title']; ?></span>
                      </a>
                  </li>
              <?php endforeach; ?>
            </ul>
        </div>
    </nav>
<?php endif; ?>
<!-- End rmcommon toolbar //-->