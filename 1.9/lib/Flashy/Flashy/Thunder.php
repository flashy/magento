<?php

class Flashy_Thunder {

    /**
     * Track an event
     * @param  integer $account_id
     * @param  string $email email address of the customer
     * @param  string $event event type
     * @param  array $data
     * @param  boolean $encode_email
     * @return boolean
     */
    public function track($account_id, $email, $event, $data, $encode_email = true)
    {
		$url = "https://track.flashyapp.com/events/track";

		$email = ( $encode_email == true ) ? base64_encode($email) : $email;

		$json_data = json_encode(array(
			"event" => $event,
			"body" => array_merge(array("account_id" => $account_id, "flashy_id" => $email), $data)
		));

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$json_response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return json_decode($json_response, true);
    }
}