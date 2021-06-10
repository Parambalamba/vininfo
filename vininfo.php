<?php
/**
 * Plugin Name: Vin Info
 * Description: Plugin for test work
 * Author:      Sergei Konovalov
 * Version:     1.0
 *
*/
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
include(plugin_dir_path(__FILE__) . 'inc/vininfo_shortcode.php');

/**
 *  Класс плагина
 */
class vininfo
{

    protected $slug = 'vin_info';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_vininfo_page'));
        add_action('admin_init', array($this, 'plugin_settings'));
        add_filter('plugin_action_links', array($this, 'vininfo_links'), 10, 2);

        //register_activation_hook(__FILE__, array($this,'plugin_activate')); //активирует хук
        //register_deactivation_hook(__FILE__, array($this,'plugin_deactivate')); //отключает хук
    }

    /**
     * Возвращаем значение переменной плагина
     */
    public function get_slug() {
        return $this->slug;
    }

    /**
     * Создаем страницу настроек плагина
     */

    public function add_vininfo_page() {
        add_options_page( 'Настройки VinInfo', 'Vin Info', 'manage_options', 'vin_info', array($this, 'vin_info_options_page_output') );
    }

    public function vin_info_options_page_output() {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>

            <form action="options.php" method="POST">
                <?php
                    settings_fields( 'vin_info_api_key' );     // скрытые защитные поля
                    do_settings_sections( 'vin_info' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Регистрируем настройки.
     * Настройки будут храниться в массиве, а не одна настройка = одна опция.
     */

    public function plugin_settings(){
        // параметры: $option_group, $option_name, $sanitize_callback
        register_setting( 'vin_info_api_key', 'rapid_api_key' );

        // параметры: $id, $title, $callback, $page
        add_settings_section( 'vin_info_section', 'Основные настройки', '', $this->get_slug() );

        // параметры: $id, $title, $callback, $page, $section, $args
        add_settings_field('vin_info_api_key', 'Api Key', array($this, 'fill_api_key'), $this->get_slug(), 'vin_info_section' );
        //add_settings_field('primer_field2', 'Другая опция', 'fill_primer_field2', 'primer_page', 'section_id' );
    }

    /**
     *  Заполняем поле
     */
    public function fill_api_key(){
        $val = get_option('rapid_api_key');
        //$val = $val ? $val['input'] : null;
        ?>
        <input type="text" name="rapid_api_key" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    /**
      * Добавляем ссылку на настройки в список плагинов
      */
    public function vininfo_links($links, $file) {

        //проверка - наш это плагин или нет
        if ( $file != plugin_basename(__FILE__) ){
            return $links;
        }

        // создаем ссылку
        $settings_link = sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=vin_info'), 'Настройки');

        array_unshift( $links, $settings_link );
        return $links;
    }
}

new vininfo();
?>