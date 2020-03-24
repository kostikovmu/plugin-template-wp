<?
/**
 * Plugin Name: Калькулятор AirPods
 * Description: Расчет стоимости, отображение фото, отправка на почту
 * Author URI:  https://kostikovmu.ru
 * Author:      kostikovmu
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     false
 * Version:     1.0.0
 */

defined ('ABSPATH') || exit();

define ( 'AIRPODS_DIR', get_template_directory() );
define ( 'AIRPODS_URL', get_template_directory_uri() );

require_once AIRPODS_DIR . '/inc/helpers.php';

class AirPods_Plugin {

  public function __construct() {
    register_activation_hook(__FILE__, [ $this, 'activation' ] );
    register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );


    add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );
    add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    add_action('wp_ajax_airPods', [ $this, 'send_form' ] );
    add_action('wp_ajax_nopriv_airPods', [ $this, 'send_form' ] );


    add_shortcode( 'air_pods_calc', [ $this, 'short_code' ] );
  }

  public function activation() {
    $this->add_data();
  }
  private function add_data() {
    add_option('airPods_options', [

    ]);
  }

  public function deactivation() {
    $this->remove_data();
  }
  private function remove_data() {

  }

  public function assets() {
    wp_register_style(
      'airPods_font_style',
      'https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap&subset=cyrillic',
      []
    );
    wp_register_style(
      'airPods_libs_style',
      AIRPODS_URL . '/assets/css/libs.min.css',
      [],
      filemtime( AIRPODS_DIR . '/assets/css/libs.min.css' )
    );
    wp_register_style(
      'airPods_main_style',
      AIRPODS_URL . '/assets/css/main.min.css',
      [ 'airPods_libs_style' ],
      filemtime( AIRPODS_DIR . '/assets/css/main.min.css' )
    );
    wp_register_script(
      'airPods_libs_script',
      AIRPODS_URL . '/assets/js/libs.min.js',
      [ 'jquery' ],
      filemtime( AIRPODS_DIR . '/assets/js/libs.min.js' )
    );
    wp_register_script(
      'airPods_main_script',
      AIRPODS_URL . '/assets/js/main.min.js',
      [ 'airPods_libs_script' ],
      filemtime( AIRPODS_DIR . '/assets/js/main.min.js' )
    );
  }

  public function admin_menu() {

    add_menu_page(
      '',
      '',
      'manage_options',
      '',
      [ $this, 'render_admin_sub_menu_1' ],
      AIRPODS_URL . '/inc/images/calculator.svg',
      105
    );

    $sub_menu_1 = add_submenu_page(
      '',
      '',
      '',
      'manage_options',
      '',
      [ $this, 'render_admin_sub_menu_1' ],
      1
    );

    add_action( 'load-' . $sub_menu_1, [ $this, 'admin_assets' ] );
  }
  public function render_admin_sub_menu_1() {
    echo $this->get_template('sub_menu_1');
  }
  public function admin_assets() {

  }
  public function short_code() {
    wp_enqueue_style('airPods_font_style' );
    wp_enqueue_style('airPods_libs_style' );
    wp_enqueue_style('airPods_main_style' );

    wp_enqueue_script('airPods_libs_script' );
    wp_enqueue_script('airPods_main_script' );

    return $this->get_template( 'short_code' );
  }


  function send_form() {
    check_ajax_referer('airPods_action','nonce');

    require_once ABSPATH . '/wp-includes/class-phpmailer.php';

    $to_mail = get_theme_mod('vip_print_form_email');
    $site = $_SERVER['SERVER_NAME'];

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->isHTML(true);

    $mail->Host = 'smtp.mail.ru';
    $mail->SMTPAuth = true;
    $mail->Username = 'vipprint24@mail.ru';
    $mail->Password = 'OuUNpPtoa62(';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = '465';

    $mail->CharSet = 'UTF-8';
    $mail->From = 'vipprint24@mail.ru';
    $mail->FromName = 'vipprint24';
    $mail->Subject = 'Заявка с сайта';

    $field_1 = $_POST['name'];

    $label_1 = $_POST['label1'];

    $body  = "<html><body style='font-family:Arial,sans-serif;'>";
    $body .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Заявка с сайта $site</h2>\r\n";
    $body .= "<p><strong>$label_1: </strong>$field_1</p>\r\n";
    $body .= "</body></html>";

    $mail->Body = $body;


    if( isset( $_FILES['file-1'] ) ) {
      $mail->addAttachment($_FILES['file-1']['tmp_name'], $_FILES['file-1']['name'] );
    }


    $mail->addAddress($to_mail);
    $send = $mail->send();
    $mail->clearAllRecipients();

    if ( $send ) {
      echo 1;
    }
    else {
      echo 0;
    }
    wp_die();

  }

  private function get_template($template_name) {
    ob_start();
    do_action('travel_insurance_before_' . $template_name );
    require 'templates/' . $template_name . '.php';
    do_action('travel_insurance_after_' . $template_name );
    $html = ob_get_clean();
    return $html;
  }

}

new AirPods_Plugin();
