<?php

namespace NikhilPandey\Esewa;

class Esewa
{
    /**
     * The id for the order.
     *
     * @var int
     */
    public $order_id;

    /**
     * The total amount for the order.
     *
     * @var int
     */
    public $amount;

    /**
     * The reference id provided by esewa.
     *
     * @var string
     */
    public $reference_id;

    /**
     * Esewa's URL to check the payment.
     *
     * @var string
     */
    protected $url;

    /**
     * The merchant code.
     *
     * @var string
     */
    protected $merchant_code;

    /**
     * Create a new Esewa Instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->url = config('esewa.debug') ? 'https://dev.esewa.com.np/epay/transrec' : 'https://esewa.com.np/epay/transrec';
        $this->merchant_code = config('esewa.merchant_code');
    }

    /**
     * Creates the new static instance with given values and returns it.
     * 
     * @param  array  $fields
     * @return static
     */
    public static function with($fields = [])
    {
        $esewa = new static;
        $esewa->order_id = array_get($fields, array_has($fields, 'id') ? 'id' : 'order_id');
        $esewa->reference_id = array_get($fields, 'reference_id');
        $esewa->amount = array_get($fields, 'amount');

        return $esewa;
    }

    /**
     * Checks with the esewa server if the payment is valid.
     *
     * @return bool
     */
    public function isPaid()
    {
        // The fields to be sent
        $post_fields = http_build_query([
            'pid' => $this->order_id,
            'rid' => $this->reference_id,
            'scd' => $this->merchant_code,
            'amount' => $this->amount,
        ]);

        $curl_connection = curl_init($this->url);
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        // Since Esewa uses self signed certificates
        // We need to disable peer verification
        // Else the request will not be sent
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_fields);
        $result = curl_exec($curl_connection);
        curl_close($curl_connection);

        // Check if the response contains success
        return str_contains($result, 'Success');
    }
}
