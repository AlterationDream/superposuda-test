<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function submit(Request $request) {
        $name = ($request->name) ? $request->name : '';
        $comment = ($request->comment) ? $request->comment : '';
        $article = ($request->article) ? $request->article : '';
        $manufacturer = ($request->manufacturer) ? $request->manufacturer : '';

        $productID = $this->findProduct($article, $manufacturer);
        if (!$productID) return view('welcome', ['error' => 'Товар не найден!']);
        $name = explode( ' ', trim($name));
        $postData = http_build_query(array(
            'order' => json_encode(array(
                'status' => 'trouble',
                'orderType' => 'fizik',
                'site' => 'test',
                'orderMethod' => 'test',
                'number' => '2121998',
                'lastName' => ($name[0]) ? $name[0] : '',
                'firstName' => ($name[1]) ? $name[1] : '',
                'patronymic' => ($name[2]) ? $name[2] : '',
                'customerComment' => $comment,
                'items' => [
                    'offers' => [
                        'id' => $productID
                    ]
                ]
        )),
            'apiKey' => 'QlnRWTTWw9lv3kjxy1A8byjUmBQedYqb'
        ));
        $url = "https://superposuda.retailcrm.ru/api/v5/orders/create";
        $options = [
            'http' => [
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => $postData
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return view('welcome', ['error' => 'Успешно!']);
    }

    public function findProduct($article, $manufacturer) {
        $url = "https://superposuda.retailcrm.ru/api/v5/store/products";
        $params = [
            'filter[name]' => $article,
            'filter[manufacturer]' => $manufacturer,
            'apiKey' => 'QlnRWTTWw9lv3kjxy1A8byjUmBQedYqb'
        ];
        $result = json_decode(
            file_get_contents($url . '?' . http_build_query($params)),
            true
        );
        if (count($result['products']) < 1) return false;
        return $result['products'][0]['offers'][0]['id'];
    }

    /*
    public function getOrders() {
        $url = "https://superposuda.retailcrm.ru/api/v5/orders";
        $params = [
            'apiKey' => 'QlnRWTTWw9lv3kjxy1A8byjUmBQedYqb'
        ];
        $result = json_decode(
            file_get_contents($url . '?' . http_build_query($params)),
            true
        );
        \Log::info(print_r($result, true));
    }*/
}
