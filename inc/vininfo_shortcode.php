<?php
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

class vininfo_shortcode
{

	function __construct()
	{
		add_action('init', array($this,'register_vininfo_shortcode'));
	}

	/**
	 * Регистрируем шорткод
	 */
	public function register_vininfo_shortcode() {
		add_shortcode('vin_info_shortcode', array($this,'vininfo_shortcode_output'));
	}

	/**
	 * Выводим форму нашего плагина и возвращаем результаты проверки
	 */
	public function vininfo_shortcode_output($atts, $content) {
		//global $vininfo;
		echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    			<div>
    				<label for="vincode">Vin Code <strong>*</strong></label>
    				<input type="text" name="vincode" value="">
    			</div>
    			<input type="submit" name="submit" value="Проверить"/>
    			</form>';
		return $this->check_vin_code();
	}

	/**
	 * Проверка vin'а в rapid api
	 */
	public function check_vin_code() {
		if (isset($_POST['submit'])) {
			global $vincode;
			$vincode = sanitize_text_field($_POST['vincode']);
			$url = 'https://vindecoder.p.rapidapi.com/decode_vin';
			$api_key = get_option('rapid_api_key');
			$args = array(
				'headers' => array(
					'x-rapidapi-key' => $api_key,
					'x-rapidapi-host' => 'vindecoder.p.rapidapi.com'
				),
				'body' => array(
					'vin' => $vincode
				),
			);
			$response = wp_remote_get($url, $args);
			$answer = json_decode(wp_remote_retrieve_body( $response ), true);
			$html = '';
			if ($answer['specification']) {
				$html = '<div style="display: flex;justify-content: space-between;width: 100%; flex-wrap: wrap;">';
				foreach ($answer['specification'] as $key => $value) {
					$html .= '<div style="display: flex; width: 100%;justify-content:space-between;"><span>' . $key . '</span><span>' . $value . '</span></div>';
				}
				$html .= '</div>';
			}
			echo "vincode вернул следующие данные = <br>" . $html;
		}
	}
}

$sh = new vininfo_shortcode();
?>