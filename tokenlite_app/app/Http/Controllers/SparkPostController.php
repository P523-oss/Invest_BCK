<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class SparkPostController extends Controller
{
    public function sendMail($type, $user, $userMail){
        $httpClient = new GuzzleAdapter(new Client());
        $sparky = new SparkPost($httpClient, ['key' => env('SPARK_POST_KEY')]);

        $sparky->setOptions(['async' => false]);

        $username = 'Hi '.$user;
        if($type=='register'){
            $emailTitle='Register Notification';
            $message = '<html><body> <p> '.$username.' <br> Thanks for joining Tokenizer. Your continued support is appreciated. We can assure you we are working hard to build the products and offer you great opportunities in Token Sales</p></body></html>';
        }
        if($type=='selfcert'){
            $emailTitle='Self Certification Notification';
            $message = '<html><body> <p> '.$username.' <br> Thank you for comepleting self certification.</p></body></html>';
        }
        
        $results = $sparky->transmissions->post([
        'options' => [
            'sandbox' => false,
        ],
        'content' => [
            'from' => 'invest@email.tokenizer.cc',
            'subject' => $emailTitle,
            'html' => $message
        ],
        'recipients' => [
            ['address' => ['email'=>$userMail]]
        ]
        ]);
    }
}
