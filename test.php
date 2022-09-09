<?php
$id_product = $product_saved_response->id;


foreach($colors_products as $index => $color_product){

    // array_push($attributes_array, $color_product->NombreColor);

    $price = $color_product->Precio;
    $tales_products = $color_product->Talla;
    foreach($tales_products as $key_tal => $tale){
        $data_atribute = [
            'regular_price' => $color_product->Precio,
            'attributes' => [
                [
                    "id" => 2,
                    "position" => 0,
                    "visible" => true,
                    "variation" => true,
                    "options" => [$color_product->NombreColor],
                    
                ],
                // [
                //     "id" => 1,
                //     "position" => 0,
                //     "visible" => true,
                //     "variation" => true,
                //     "options" => [$key_tal]
                    
                // ],
            ]
        ];

        // var_dump($id_product);die;

        print_r($woocommerce->post('products/'.$id_product.'/variations', $data_atribute));
    }

}
$data = [
    'name' => $products_api->Referencia,
    'type' => 'simple',
    'regular_price' => $price,
    'description' => $products_api->TipoReferencia,
    'short_description' => '',
    'attributes' => [
        [
            "id" => 2,
            "position" => 0,
            "visible" => true,
            "variation" => true,
            "options" => $attributes_array
            
        ],
    ]
];


$productos_woocomerce = $woocommerce->get('products');
// $json_response_products = json_decode($productos_woocomerce);
foreach ($productos_woocomerce as $product) {
    print_r($product);die;
}

/* prueba conectar a woocomerce */

$store_url = 'https://deploy.calzadocaprino.com/';
$endpoint = '/wc-auth/v1/authorize';
$params = [
    'app_name' => 'API Caprino',
    'scope' => 'read_write',
    'user_id' => 2,
    'return_url' => 'http://app.com',
    'callback_url' => 'https://app.com'
];
$query_string = http_build_query( $params );

echo $store_url . $endpoint . '?' . $query_string;

?>