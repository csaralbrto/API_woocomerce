<?php

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

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

    // print_r($woocommerce->get('products/221/variations'));die;
    // print_r($woocommerce->get('products/attributes'));die;
    $orders = $woocommerce->get('orders');

    $array_items_order = [];
    foreach ($orders as $key => $order) {
        $date = date('Y-m-d');
    
        $NumeroPedido = $order->number;
        $CedulaCliente = isset($order->billing->company) ? $order->billing->company : 0;
        $NombreCliente = $order->billing->first_name.' '.$order->billing->last_name;
        $Valor = $order->total;
        $FechaPedido = $date;
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
        // var_dump(json_encode($array_items_order));die;
    
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

        $data = [
            'action' => 'setpedido',
            'Token' => $auth_token,
            'NumeroPedido' => $NumeroPedido,
            'CedulaCliente' => $CedulaCliente,
            'NombreCliente' => $NombreCliente,
            'Valor' => $Valor,
            'FechaPedido' => $FechaPedido,
            'Referencias' => $array_items_order,
        ];

        var_dump(json_encode($data));die;
    
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
    
            $response_order = curl_exec($curl);
    
        curl_close($curl);
        $json_response_products = json_decode($response_order);
        var_dump($json_response_products);die;
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
        CURLOPT_POSTFIELDS => array('action' => 'getproducto','Token' => $auth_token),
        ));

        $response_products = curl_exec($curl);

    curl_close($curl);
    $json_response_products = json_decode($response_products);
    $response_all_products = $json_response_products->response;

    $couny = 0;
    foreach ($response_all_products as $key => $products_api) {
        if($count < 1){

            $colors_products = $products_api->Color;

            $attribute_var = "";
            $price = 0;
            $attributes_array = array("");
            $attributes_color_array = array("");
            $genero = 45;

            foreach($colors_products as $index => $color_product){

                array_push($attributes_color_array, $color_product->NombreColor);
                $price = $color_product->Precio;
                switch ($color_product->Genero) {
                    case 'F':
                        $genero = 49;
                        break;
                    
                    default:
                        $genero = 45;
                        break;
                }

                /* Queda pendiente hacer la lógica de las categorías */
                $tales_products = $color_product->Talla;
                foreach($tales_products as $key_tal => $tale){
                    if(!in_array($key_tal, $attributes_array, true)){
                        array_push($attributes_array, $key_tal);
                    }
                }

            }

            $data = [
                'name' => $products_api->Referencia,
                'type' => 'variable',
                'description' => $products_api->TipoReferencia,
                'short_description' => '',
                'regular_price' => $price, 
                'attributes' => [
                    [
                        /* Envio de tallas */
                        "id" => 1,
                        "position" => 0,
                        "visible" => true,
                        "variation" => true,
                        "options" => $attributes_array
                        
                    ],
                    [
                        /* Envio de colores */
                        "id" => 2,
                        "position" => 0,
                        "visible" => true,
                        "variation" => true,
                        "options" => $attributes_color_array
                        
                    ],
                ],  
                'categories' => [
                    [
                        'id' => $genero
                    ]
                ],
            ];

            // var_dump($data);die;

            // print_r();die;

            $product = $woocommerce->post('products', $data);

            $id_producto = $product->id;

            foreach($colors_products as $index => $color_product){

                // array_push($attributes_array, $color_product->NombreColor);

                $price = $color_product->Precio;
                $tales_products = $color_product->Talla;
                foreach($tales_products as $key_tal => $tale){
                    // if(!in_array($key_tal, $attributes_color_array, true)){
                    //     array_push($attributes_color_array, $key_tal);
                    // }
                    $data_variation = [
                        'regular_price' => $price,
                        'attributes' => [
                            [
                                'id' => 1,
                                'option' => $key_tal
                            ],
                            [
                                'id' => 2,
                                'option' => $color_product->NombreColor
                            ]
                        ]
                    ];

                    // $data = [
                    //     'name' => $products_api->Referencia,
                    //     'type' => 'variable',
                    //     'description' => $products_api->TipoReferencia,
                    //     'short_description' => '',
                    //     'regular_price' => $color_product->Precio,
                    //     'attributes' => [
                    //         [
                    //             "id" => 2,
                    //             "position" => 0,
                    //             "visible" => true,
                    //             "variation" => true,
                    //             "options" => $attributes_array
                                
                    //         ],
                    //         [
                    //             "id" => 1,
                    //             "position" => 0,
                    //             "visible" => true,
                    //             "variation" => true,
                    //             "options" => $attributes_color_array
                                
                    //         ],
                    //     ],    
                    //     'categories' => [
                    //         [
                    //             'id' => $genero
                    //         ]
                    //     ],
                    // ];
                    $woocommerce->post('products/'.$id_producto.'/variations', $data_variation);

                }
                // $data_variation_color = [
                //     'regular_price' => $price,
                //     'attributes' => [
                //         [
                //             'id' => 2,
                //             'option' => $color_product->NombreColor
                //         ]
                //     ]
                // ];
                // $woocommerce->post('products/'.$id_producto.'/variations', $data_variation_color);

            }
            $count = $count + 1;
        }
    }

?>