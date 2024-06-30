<?php

namespace App\Traits;

trait ImageTrait {

    public function sendEmail()
    {
        $url = 'https://api.elasticemail.com/v2/email/send';

        try{
            $post = [
                'from' => 'youremail@yourdomain.com',
                'fromName' => 'Your Company Name',
                'apikey' => '00000000-0000-0000-0000-000000000000',
                'subject' => 'Your Subject',
                'to' => 'recipient1@gmail.com;recipient2@gmail.com',
                'bodyHtml' => '<h1>Html Body</h1>',
                'bodyText' => 'Text Body',
                'isTransactional' => false
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;   
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
}