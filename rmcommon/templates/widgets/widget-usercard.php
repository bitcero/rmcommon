<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

if ($widgetSettings->type == 'large'):
    // LARGE WIDGET
?>

    <div <?php echo $attributes; ?>>

        <?php if ('top' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

        <section class="main-section">
            <div class="media">
                <div class="media-left">
                    <?php if ('' != $widgetSettings->link): ?>
                        <a href="<?php echo $widgetSettings->link; ?>">
                            <img src="<?php echo $widgetSettings->image; ?>" class="media-object" alt="<?php echo $widgetSettings->name; ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo $widgetSettings->image; ?>" class="media-object" alt="<?php echo $widgetSettings->name; ?>">
                    <?php endif; ?>
                </div>
                <div class="media-body">
                    <h3 class="media-heading"><?php echo $widgetSettings->name; ?></h3>
                    <?php if ('' != $widgetSettings->charge): ?><small class="card-charge"><?php echo $widgetSettings->charge; ?></small><?php endif; ?>
                    <?php if (false == empty($widgetSettings->mainButton)): ?>
                        <a href="<?php echo $widgetSettings->mainButton['link']; ?>" class="btn btn-<?php echo $widgetSettings->color; ?> main-button"><?php echo $widgetSettings->mainButton['caption']; ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (false == empty($widgetSettings->counters)): ?>
                <div class="card-counters">
                    <ul>
                        <?php foreach ($widgetSettings->counters as $i => $counter): if ($i > 2) {
    continue;
} ?>

                            <li class="counter-item">
                                <span class="value"><?php echo $counter['value']; ?></span>
                                <span class="caption"><?php echo $counter['caption']; ?></span>
                            </li>

                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($widgetSettings->info != '' || false == empty($widgetSettings->social)): ?>
        <section class="secondary-section">
            <?php if ('' != $widgetSettings->info): ?>
            <div class="card-info"><?php echo $widgetSettings->info; ?></div>
            <?php endif; ?>

            <?php if (false == empty($widgetSettings->social)): ?>
            <div class="card-social">
                <ul>
                    <?php foreach ($widgetSettings->social as $social): ?>
                    <li>
                        <a href="<?php echo $social['link']; ?>" target="_blank" class="text-<?php echo $widgetSettings->color; ?>">
                            <?php echo $common->icons()->getIcon($social['icon']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
        <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

    </div>

<?php

elseif ($widgetSettings->type == 'large-header'):
    // LARGE WIDGET WITH HEADER

?>

    <div <?php echo $attributes; ?>>
        <div
                class="card-header bg-<?php echo $widgetSettings->color; ?>"
                <?php if ('' != $widgetSettings->headerBg): ?>style="background-image: url(<?php echo $widgetSettings->headerBg; ?>);"<?php endif; ?>
        >
            <?php if ($widgetSettings->headerGradient): ?><div class="header-gradient"></div><?php endif; ?>
        </div>
        <header>
            <?php if ('' != $widgetSettings->link): ?>
                <a href="<?php echo $widgetSettings->link; ?>">
                    <img src="<?php echo $widgetSettings->image; ?>" class="card-avatar" alt="<?php echo $widgetSettings->name; ?>">
                </a>
            <?php else: ?>
                <img src="<?php echo $widgetSettings->image; ?>" class="card-avatar" alt="<?php echo $widgetSettings->name; ?>">
            <?php endif; ?>
            <h3 class="card-name"><?php echo $widgetSettings->name; ?></h3>
            <?php if ('' != $widgetSettings->charge): ?><small class="card-charge"><?php echo $widgetSettings->charge; ?></small><?php endif; ?>
        </header>

        <?php if (false == empty($widgetSettings->counters)): ?>
            <section class="card-counters">
                <ul>
                    <?php foreach ($widgetSettings->counters as $i => $counter): if ($i > 2) {
    continue;
} ?>

                        <li class="counter-item">
                            <span class="value text-<?php echo $widgetSettings->color; ?>"><?php echo $counter['value']; ?></span>
                            <span class="caption"><?php echo $counter['caption']; ?></span>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (false == empty($widgetSettings->mainButton)): ?>
            <div class="card-button">
                <a href="<?php echo $widgetSettings->mainButton['link']; ?>" class="btn btn-<?php echo $widgetSettings->color; ?> main-button">
                    <?php if ('' != $widgetSettings->mainButton['icon']): ?>
                        <?php echo $common->icons()->getIcon($widgetSettings->mainButton['icon']); ?>
                    <?php endif; ?>
                    <?php echo $widgetSettings->mainButton['caption']; ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ('' != $widgetSettings->info): ?>
            <div class="card-info"><?php echo $widgetSettings->info; ?></div>
        <?php endif; ?>

        <?php if (false == empty($widgetSettings->social)): ?>
            <div class="card-social">
                <ul>
                    <?php foreach ($widgetSettings->social as $social): ?>
                        <li>
                            <a href="<?php echo $social['link']; ?>" target="_blank" class="text-<?php echo $widgetSettings->color; ?>">
                                <?php echo $common->icons()->getIcon($social['icon']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

    </div>

<?php

elseif ($widgetSettings->type == 'large-big-header'):
    // LARGE WIDGET WITH BIG HEADER

?>

    <div <?php echo $attributes; ?>>
    <header
            class="card-header bg-<?php echo $widgetSettings->color; ?><?php echo $widgetSettings->headerGradient && '' == $widgetSettings->headerBg ? ' with-gradient' : ' no-gradient'; ?>"
            <?php if ('' != $widgetSettings->headerBg): ?>style="background-image: url(<?php echo $widgetSettings->headerBg; ?>);"<?php endif; ?>
    >
        <?php if ($widgetSettings->headerGradient && '' == $widgetSettings->headerBg): ?><div class="header-gradient"><?php endif; ?>

        <?php if ('' != $widgetSettings->link): ?>
            <a href="<?php echo $widgetSettings->link; ?>">
                <img src="<?php echo $widgetSettings->image; ?>" class="card-avatar" alt="<?php echo $widgetSettings->name; ?>">
            </a>
        <?php else: ?>
            <img src="<?php echo $widgetSettings->image; ?>" class="card-avatar" alt="<?php echo $widgetSettings->name; ?>">
        <?php endif; ?>
        <h3 class="card-name"><?php echo $widgetSettings->name; ?></h3>
        <?php if ('' != $widgetSettings->charge): ?><small class="card-charge"><?php echo $widgetSettings->charge; ?></small><?php endif; ?>

        <?php if ($widgetSettings->headerGradient && '' == $widgetSettings->headerBg): ?></div><?php endif; ?>

    </header>

    <?php if (false == empty($widgetSettings->counters)): ?>
    <section class="card-counters">
        <ul>
            <?php foreach ($widgetSettings->counters as $i => $counter): if ($i > 2) {
    continue;
} ?>

                <li class="counter-item">
                    <span class="icon">
                        <?php if (array_key_exists('icon', $counter) && '' != $counter['icon']): ?>
                        <?php echo $common->icons()->getIcon($counter['icon']); ?>
                        <?php endif; ?>
                    </span>
                    <span class="value bg-<?php echo $widgetSettings->color; ?>"><?php echo $counter['value']; ?></span>
                    <span class="caption"><?php echo $counter['caption']; ?></span>
                </li>

            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

    <?php if (false == empty($widgetSettings->mainButton) || false == empty($widgetSettings->addonButton)): ?>
        <div class="card-button">

            <?php if (false == empty($widgetSettings->mainButton)): ?>

                <a href="<?php echo $widgetSettings->mainButton['link']; ?>" class="btn btn-<?php echo $widgetSettings->color; ?> main-button">
                    <?php if ('' != $widgetSettings->mainButton['icon']): ?>
                        <?php echo $common->icons()->getIcon($widgetSettings->mainButton['icon']); ?>
                    <?php endif; ?>
                    <?php echo $widgetSettings->mainButton['caption']; ?>
                </a>

            <?php endif; ?>

            <?php if (false == empty($widgetSettings->addonButton)): ?>
                <a href="<?php echo $widgetSettings->addonButton['link']; ?>" class="btn btn-<?php echo $widgetSettings->addonButton['color'] ? $widgetSettings->addonButton['color'] : $widgetSettings->color; ?> main-button">
                    <?php if ('' != $widgetSettings->addonButton['icon']): ?>
                        <?php echo $common->icons()->getIcon($widgetSettings->addonButton['icon']); ?>
                    <?php endif; ?>
                    <?php echo $widgetSettings->addonButton['caption']; ?>
                </a>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <?php if ('' != $widgetSettings->info): ?>
    <div class="card-info"><?php echo $widgetSettings->info; ?></div>
<?php endif; ?>

    <?php if (false == empty($widgetSettings->social)): ?>
    <div class="card-social">
        <ul>
            <?php foreach ($widgetSettings->social as $social): ?>
                <li>
                    <a href="<?php echo $social['link']; ?>" target="_blank" class="text-<?php echo $widgetSettings->color; ?>">
                        <?php echo $common->icons()->getIcon($social['icon']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
    <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
<?php endif; ?>

    </div>

<?php

elseif ($widgetSettings->type == 'tiny'):
    // TINY WIDGET

?>

    <div <?php echo $attributes; ?>>

        <?php if ('top' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

        <div class="card-container">

            <div class="card-avatar">
                <?php if ('' != $widgetSettings->link): ?>
                    <a href="<?php echo $widgetSettings->link; ?>">
                        <img src="<?php echo $widgetSettings->image; ?>" alt="<?php echo $widgetSettings->name; ?>">
                    </a>
                <?php else: ?>
                    <img src="<?php echo $widgetSettings->image; ?>" alt="<?php echo $widgetSettings->name; ?>">
                <?php endif; ?>
            </div>
            <div class="card-content">
                <h3 class="card-name"><?php echo $widgetSettings->name; ?></h3>
                <?php if ('' != $widgetSettings->info): ?>
                    <div class="card-line"><?php echo $widgetSettings->info; ?></div>
                <?php endif; ?>
            </div>

        </div>

        <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

    </div>

<?php

elseif ($widgetSettings->type == 'small'):
    // SMALL WIDGET

?>
    <div <?php echo $attributes; ?>>
        <?php if ('top' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

        <div class="card-container">

            <div class="card-avatar">
                <?php if ('' != $widgetSettings->link): ?>
                    <a href="<?php echo $widgetSettings->link; ?>">
                        <img src="<?php echo $widgetSettings->image; ?>" alt="<?php echo $widgetSettings->name; ?>">
                    </a>
                <?php else: ?>
                    <img src="<?php echo $widgetSettings->image; ?>" alt="<?php echo $widgetSettings->name; ?>">
                <?php endif; ?>
            </div>

            <h3 class="card-name"><?php echo $widgetSettings->name; ?></h3>
            <?php if ('' != $widgetSettings->info): ?>
                <div class="card-line"><?php echo $widgetSettings->info; ?></div>
            <?php endif; ?>

            <?php if (false == empty($widgetSettings->mainButton)): ?>
                <div class="card-button">
                    <a href="<?php echo $widgetSettings->mainButton['link']; ?>" class="btn btn-<?php echo $widgetSettings->color; ?> btn-sm main-button">
                        <?php if ('' != $widgetSettings->mainButton['icon']): ?>
                            <?php echo $common->icons()->getIcon($widgetSettings->mainButton['icon']); ?>
                        <?php endif; ?>
                        <?php echo $widgetSettings->mainButton['caption']; ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>

        <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>
    </div>
<?php

elseif ($widgetSettings->type == 'medium'):
    // MEDIUM WIDGET

?>
    <div <?php echo $attributes; ?>>
        <?php if ('top' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>

        <div class="card-container">

            <div class="media">
                <div class="media-left">
                    <?php if ('' != $widgetSettings->link): ?>
                        <a href="<?php echo $widgetSettings->link; ?>">
                            <img src="<?php echo $widgetSettings->image; ?>" class="media-object" alt="<?php echo $widgetSettings->name; ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo $widgetSettings->image; ?>" class="media-object" alt="<?php echo $widgetSettings->name; ?>">
                    <?php endif; ?>
                </div>
                <div class="media-body">
                    <h3 class="media-heading"><?php echo $widgetSettings->name; ?></h3>
                    <?php if ('' != $widgetSettings->charge): ?><small class="card-charge"><?php echo $widgetSettings->charge; ?></small><?php endif; ?>
                    <?php if (false == empty($widgetSettings->lines)): ?>
                        <ul class="card-lines">
                            <?php foreach ($widgetSettings->lines as $line): ?>
                            <li><?php echo $line; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <?php if ('bottom' == $widgetSettings->highlight || 'both' == $widgetSettings->highlight): ?>
            <div class="card-highlight bg-<?php echo $widgetSettings->color; ?>"></div>
        <?php endif; ?>
    </div>
<?php
endif;
