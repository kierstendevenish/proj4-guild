<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller {

	function __construct()
 	{
		parent::__construct();
 	}

        function index()
        {
        }

	function consume($id = '')
	{
            $name = $this->input->post('_name');
            $domain = $this->input->post('_domain');

            if ($domain == 'rfq')
            {
                if ($name == 'delivery_ready')
                {
                    //forward delivery_ready event to top-3 rated drivers
                    $this->load->model('user');
                    $esls = $this->user->getTop3Esls();

                    $id = $this->input->post('id');
                    $deliveryAddr = $this->input->post('deliveryAddr');
                    $deliveryTime = $this->input->post('deliveryTime');
                    $pickupTime = $this->input->post('pickupTime');
                    $shopName = $this->input->post('shopName');
                    $shopEsl = $this->input->post('shopEsl');
                    $shopCoords = $this->input->post('shopCoords');

                    foreach ($esls as $e)
                    {
                        $fields_str = '_name=delivery_ready&_domain=rfq&id='.$id.'&shopName='.$shopName.'&shopCoords='.$shopCoords.'&pickupTime='.$pickupTime.'&deliveryAddr='.$deliveryAddr.'&deliveryTime='.$deliveryTime.'&shopEsl='.$shopEsl;
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $e);
                        curl_setopt($ch, CURLOPT_POST, 9);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_str);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        curl_close($ch);
                    }

                    //save the delivery
                    $this->load->model('request');
                    $this->request->saveDelivery($id);
                }
                else if ($name == 'bid_awarded')
                {
                    $driverName = $this->input->post('driverName');
                    $deliveryId = $this->input->post('deliveryId');

                    $this->load->model('request');
                    $this->request->markUser($deliveryId, $driverName);

                    $this->load->model('user');
                    $esl = $this->user->getEsl($driverName);

                    $fields_str = '_name=bid_awarded&_domain=rfq&driverName='.$driverName.'&deliveryId='.$deliveryId;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $esl);
                    curl_setopt($ch, CURLOPT_POST, 4);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_str);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
            else if ($domain == 'delivery')
            {
                if ($name == 'picked_up')
                {
                    $deliveryId = $this->input->post('deliveryId');

                    $this->load->model('request');
                    $this->request->markPickedUp($deliveryId);
                    $user = $this->request->getDriver($deliveryId);

                    $this->load->model('user');
                    $this->user->raiseRank($user);
                }
                else if ($name == 'complete')
                {
                    //update driver ranking if on time
                }
            }
	}
}

?>
