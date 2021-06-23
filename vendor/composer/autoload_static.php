<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc790af260e9e3ac961b63475f68734bc
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'core\\Connection_Type' => __DIR__ . '/../..' . '/core/Connection_Type.php',
        'core\\DB' => __DIR__ . '/../..' . '/core/DB.php',
        'core\\DBException' => __DIR__ . '/../..' . '/core/DBException.php',
        'core\\HTML' => __DIR__ . '/../..' . '/core/HTML.php',
        'core\\HTTP_Request_Helper' => __DIR__ . '/../..' . '/core/HTTP_Request_Helper.php',
        'core\\Input' => __DIR__ . '/../..' . '/core/Input.php',
        'core\\Permissions' => __DIR__ . '/../..' . '/core/Permissions.php',
        'core\\crud\\crud_html_accordian' => __DIR__ . '/../..' . '/core/crud/crud_html_accordian.php',
        'core\\crud\\crud_html_table' => __DIR__ . '/../..' . '/core/crud/crud_html_table.php',
        'core\\crud\\crud_module' => __DIR__ . '/../..' . '/core/crud/crud_module.php',
        'core\\crud\\crud_module_manager' => __DIR__ . '/../..' . '/core/crud/crud_module_manager.php',
        'core\\crud\\crud_modules\\crud_module_dance_by_name' => __DIR__ . '/../..' . '/core/crud/crud_modules/crud_module_dance_by_name.php',
        'core\\crud\\crud_types\\crud_type' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type.php',
        'core\\crud\\crud_types\\crud_type_bool' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_bool.php',
        'core\\crud\\crud_types\\crud_type_foot' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_foot.php',
        'core\\crud\\crud_types\\crud_type_hide' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_hide.php',
        'core\\crud\\crud_types\\crud_type_link' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_link.php',
        'core\\crud\\crud_types\\crud_type_string' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_string.php',
        'core\\crud\\crud_types\\crud_type_youtube' => __DIR__ . '/../..' . '/core/crud/crud_types/crud_type_youtube.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitc790af260e9e3ac961b63475f68734bc::$classMap;

        }, null, ClassLoader::class);
    }
}
