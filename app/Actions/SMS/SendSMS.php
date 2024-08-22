<?php

namespace App\Actions\SMS;

class SendSMS
{

    public static function sendSMS($phone, $msg)
    {
        try {
            $encodedMsg = urlencode($msg);

            $url = 'https://apps.mnotify.net/smsapi?key=HdtmmlE3XEu1XUJocrtfLWMi5&to=' . $phone . '&msg=' . $encodedMsg . '&sender_id=ERMS';

            return self::fireSMS($url);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to deliver sms to customer ' . $ex->getMessage(),
            ], 422);
        }
    }

    public static function fireSMS($url)
    {
        $ch = curl_init();
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        curl_close($ch);

        //Log::info($result);
        return $result;
    }
}
