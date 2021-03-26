<?php

                $getFcmKey = "AAAAxk8-oa0:APA91bF4de1UFl5vtZVo5AHU-aYXyRoclbFAu-ybFnJTDPa9QQqb6OwhiGiT5LXErncQo_zQqr4ASpY8d2pDHoCFM6tCMhZ9fduia-tUk4s2VnuQlHJGX8YvAgkS55uPfwYjO7rZ85KM";
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = "f741qZ0GSFKHSHe--cE9SK:APA91bGRlFnihwj3WARjZhQehhxpjsqKUIfGEiG55gjcwgmaD1XrQKnnXPf8lXV1WIaCtdjV0FtDGn416Z0lTmin9WX5-jiDwajU5JgiDfGux9FrObjBkWeWl-znCi54U5MW93N5J00i";
                    
                    $extraNotificationData = [
                        "dboy_id"=>'19',
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