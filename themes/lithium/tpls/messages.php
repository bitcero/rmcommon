<?php
/**
 * Redirect messages
 */
$lithiumMessages = [];
if (isset($_SESSION['cu_redirect_messages'])) {
    foreach ($_SESSION['cu_redirect_messages'] as $msg) {
        $lithiumMessages[] = $msg;
    }
    unset($_SESSION['cu_redirect_messages']);
}

if (isset($_SESSION['redirect_message'])) {
    $lithiumMessages[] = [
        'text' => $_SESSION['redirect_message'],
        'icon' => 'svg-rmcommon-info',
        'level' => RMMSG_INFO,
    ];
    unset($_SESSION['redirect_message']);
}

if (!empty($lithiumMessages)) {
    $template = 'cuHandler.notify({
                    title: "%s",
                    text: "%s",
                    delay: 7000,
                    type: "%s",
                    icon: "%s",
                    nonblock: {
                        nonblock: true,
                        nonblock_opactity:.2
                    }
                });';

    foreach ($lithiumMessages as $msg) {
        if (is_string($msg['level'])) {
            $type = 'alert-' . $msg['level'];
        } else {
            switch ($msg['level']) {
                case RMMSG_DANGER:
                case RMMSG_ERROR:
                    $icon = 'svg-rmcommon-error';
                    $type = 'alert-danger';
                    break;
                case RMMSG_SAVED:
                case RMMSG_SUCCESS:
                    $icon = 'svg-rmcommon-ok-circle';
                    $type = 'alert-success';
                    break;
                case RMMSG_WARN:
                    $icon = 'svg-rmcommon-warning';
                    $type = 'alert-warning';
                    break;
                case RMMSG_INFO:
                default:
                    $icon = 'svg-rmcommon-info-solid';
                    $type = 'alert-info';
                    break;
            }
        }

        $template = sprintf($template, '', html_entity_decode($msg['text']), $type, $icon);

        RMTemplate::get()->add_inline_script($template, 1);
    }
}
