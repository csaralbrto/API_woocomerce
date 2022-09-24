<?php
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */

	/*
	* Include files by Woocomerce Cliente
	* To be able to obtain all the methods within Woocomerce 
	*/

	require __DIR__ . '/vendor/autoload.php';

	use Automattic\WooCommerce\Client;



if ( ! function_exists( 'twentytwentytwo_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'twentytwentytwo_support' );

if ( ! function_exists( 'twentytwentytwo_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'twentytwentytwo-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );


/* Hook fire when the order is complete */

add_action( 'woocommerce_payment_complete_order_status_completed', 'rudr_complete_for_status' );

function rudr_complete_for_status( $order_id ){

	$woocommerce = new Client(
		'https://deploy.calzadocaprino.com/', // Your store URL
		'ck_d1c2d0b0a56bd0355329832102097ab6d118a2bf', // Your consumer key
		'cs_fc512f8f3347845ddf0a0d226a8ae572e297aaf7', // Your consumer secret
		[
			'wp_api' => true, // Enable the WP REST API integration
			'version' => 'wc/v3', // WooCommerce WP REST API version
			'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
		]
	);

	$order = $woocommerce->get('orders/'.$order_id);
	$date = date('Y-m-d');

	$NumeroPedido = $order->number;
	$CedulaCliente = isset($order->billing->company) ? $order->billing->company : 0;
	$NombreCliente = $order->billing->first_name.' '.$order->billing->last_name;
	$Valor = $order->total;
	$FechaPedido = $date;


    $array_items_order = [];
	// var_dump($order->line_items);die;
	$items = $order->line_items;
	foreach ($items as $key_item => $item) {
		$array_by_item_orde = [];
		$cantidad = isset($item->quantity) ? $item->quantity : null;
		$nombre   = isset($item->parent_name) ? $item->parent_name : null;
		foreach ($item->meta_data as $key => $meta_data) {
			if($meta_data->display_key == 'Talla'){
				$talla = $meta_data->display_value;
			}else{
				$color = $meta_data->display_value;
			}
		}

		if(isset($cantidad) && isset($nombre) && isset($talla) && isset($color)){
			$array_by_item_orde = [
				"Referencia" => $nombre,
				"Talla" => $talla,
				"Color" => $color,
				"Cantidad" => $cantidad,
			];
		}else{
			$cantidad = isset($item->quantity) ? $item->quantity : null;
			$nombre   = isset($item->name) ? $item->name : null;
			$talla    =  null;
			$color    = null;

			$array_by_item_orde = [
				"Referencia" => $nombre,
				"Talla" => $talla,
				"Color" => $color,
				"Cantidad" => $cantidad,
			];
		}

		json_encode($array_by_item_orde);
		array_push($array_items_order, $array_by_item_orde);
	}

    $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.almacenescaprino.com/api/caprino.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('action' => 'gettoken','Usuario' => 'webcaprino','Clave' => 'C4pr1n0web##'),
        ));

        $response = curl_exec($curl);

    curl_close($curl);
    $json_response = json_decode($response);
    $response_token = $json_response->response;
    $auth_token = $response_token[0]->Token;

	$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://www.almacenescaprino.com/api/caprino.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array(
			'action' => 'setpedido',
			'Token' => $auth_token,
			'NumeroPedido' => $NumeroPedido,
			'CedulaCliente' => $CedulaCliente,
			'NombreCliente' => $NombreCliente,
			'Valor' => $Valor,
			'FechaPedido' => $FechaPedido,
			'Referencias' => $array_items_order,
			// 'Bonos' => '[
			// 	{
			// 		"IDBonoFidelizacion":"20766",
			// 		"Valor":"120000"
			// 	},
			// 	{
			// 		"IDBonoFidelizacion":"20767",
			// 		"Valor":"110000"
			// 	}
			// ]'
		),
		));

		$response = curl_exec($curl);

	curl_close($curl);
	
}

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';
