<?php

                $getFcmKey = "AAAAxk8-oa0:APA91bF4de1UFl5vtZVo5AHU-aYXyRoclbFAu-ybFnJTDPa9QQqb6OwhiGiT5LXErncQo_zQqr4ASpY8d2pDHoCFM6tCMhZ9fduia-tUk4s2VnuQlHJGX8YvAgkS55uPfwYjO7rZ85KM";
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = "ccmPoQyPSkaXDivpF7OH7Z:APA91bFe-_MeaVz0TaQEZerSHyaBuZacrE0pyyJzJrYm5v7Urx0z_GAecVeE_-pz1WrRCYy_8-XH4f8wx6TkDKH1cddto_6blvcboxZBfr9A7od3OGZLuemG00Yr58xqHziIKaE83nqH";
                    
                // $body = json_encode(array('message'=>"You Got a new order",'cart_id'=>'BVYB0031'));
                    $notification = [
                        'title' => "New Order",
                        'body' => "You Got a new order",
                        'android_channel_id'=>'AyesZ_Vendor_Notifications',
                        'sound' => "default",
                    ];
                    
                    $extraNotificationData = [
                        "store_id"=>'35',
                        "cart_id" => '123456',
                        'title' => "New Test Order",
                        'body' => "You Got a new order",
                        ];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'data' => $extraNotificationData,
                        'content_available' => false, //important for iOS
                        'priority' => "high",
                        // 'time_to_live' => 5000,
                        'requireInteraction'=> true,
                        'actions'=> [
                            'action'=> "accept",
                            'title'=> "Accept"
                        ],[
                            'action'=> "reject",
                            'title'=> "Reject"
                        ]
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    echo $result;
?>