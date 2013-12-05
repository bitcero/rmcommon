<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema Base
@author     Red México
@author     http://www.redmexico.com.mx
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/

class RmcommonErpPreload
{
    /**
     * Devuelve la lista de permisos disponibles para el módulo
     *
     * @param array $permissions Permisos existentes
     * @return array
     */
    public function eventErpLoadPermissions($permissions){

        $the_permissions = array(
            'admin-modules' => __('Administrar módulos (Instalar, desintalar y actualizar)', 'rmcommon'),
            'admin-images' => __('Administrar imágenes', 'rmcommon'),
            'admin-comments' => __('Administrar comentarios', 'rmcommon'),
            'admin-plugins' => __('Administrar Plugins', 'rmcommon'),
            'admin-groups' => __('Crear, editar, eliminar grupos', 'rmcommon'),
            'configure-system' => __('Permitir modificar la configuración del sistema y los módulos', 'rmcommon'),
            'view-alerts' => __('Ver alertas del sistema', 'rmcommon'),
        );

        $permissions[] = (object) array(
            'name' => __('Control ERP', 'rmcommon'),
            'element' => 'rmcommon',
            'type' => 'module',
            'permissions' => $the_permissions
        );

        return $permissions;

    }
}