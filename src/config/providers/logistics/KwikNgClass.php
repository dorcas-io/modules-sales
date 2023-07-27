<?php

namespace Dorcas\ModulesSales\config\providers\logistics;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use mysql_xdevapi\Exception;

class KwikNgClass
{
     private $baseUrl;
     private $username;
     private $userPassword ;
     private $apiLogin;
     private $domainName;
     private $accessToken = null;

     public function __construct()
     {
         $this->baseUrl = env('CREDENTIAL_ECOMMERCE_PROVIDER_URL', 'provider.com');
         $this->domainName = env('CREDENTIAL_ECOMMERCE_PROVIDER_DOMAIN', 'provider.com');
         $this->username = env('CREDENTIAL_ECOMMERCE_PROVIDER_USERNAME', 'user@provider.com');
         $this->userPassword = env('CREDENTIAL_ECOMMERCE_PROVIDER_PASSWORD', 'provider.com');
         $this->apiLogin = env('CREDENTIAL_ECOMMERCE_PROVIDER_API_LOGIN', 1);

         // Get Access Token
         $this->accessToken = Cache::get('kwik_access_token');

         if (is_null($this->accessToken)) {
             $this->getToken();
         }
     }


    public function getToken()
    {
        // Connect To API
         $data = '{
                  "domain_name" : "'.$this->domainName.'",
                  "email" :"'.$this->username.'",
                  "password" : "'.$this->userPassword.'",
                  "api_login" : "'.$this->apiLogin.'"
                }';

        $response = $this->postData($this->baseUrl , '/vendor_login' , $data);

        if(isset($response['success']) && $response['success']){

            $data = $response['payload'];

            Cache::put('kwik_access_token',$response['payload']->data->access_token, 60 * 5);
            Cache::put('kwik_vendor_id',$response['payload']->data->vendor_details->vendor_id, 60 * 5);
            Cache::put('kwik_form_id',$response['payload']->data->formSettings[0]->form_id);
            Cache::put('kwik_user_id',$response['payload']->data->formSettings[0]->user_id);

            $this->accessToken = $response['payload']->data->access_token;

        }else{

            $data = null;
        }

        return $data;

    }


    public function getEstimatedFare($data){

        if (is_null($this->accessToken)) {
            $this->getToken();
        }

        $getCostData = [];

        $getBillBreakDown = null;

        foreach($data['carted_items'] as $index => $cartedItem) {

            $prepareShippingData = $this->prepareShippingData($data, $cartedItem);


            Cache::put('parcel_amount', $prepareShippingData['parcel_amount']['amount'], 60 * 5);

            $getCost = $this->getCost($prepareShippingData);

            if(isset($getCost['success']) && $getCost['success']){

                $getBillBreakDown = $this->billBreakdown($getCost);

                Cache::put('kwik_priceEstimate_data',  $getCost['payload']->data, 60 * 5);

                Cache::put('kwik_billBreakDown_data',  $getBillBreakDown['payload']->data, 60 * 5);

                $getCostData[]  = [ 'billBreakdown' =>  $getBillBreakDown['payload']->data ,
                                    'estimatedPrice' =>  $getCost['payload']->data,
                                    'company_id' => $cartedItem['company_id'],
                                    'product_id' => $cartedItem['product_id']
                                  ];

            }else{

                return [
                    'success' => false,
                    'message' => $getCost['message']
                ];
            }
        }

        if(empty($getCostData)){

            return ['success' => false ,'message' => 'Could not load estimated fare'];

        }

        return [
            'success' => true,
            'data' => $getBillBreakDown ,
            'billBreakDown_estimatedPrice' => $getCostData,
            'message' => 'Price estimate fetched successfully',
        ];

    }



    public function createTask($orders){

        if (is_null($this->accessToken)) {
            $this->getToken();
        }
        $billBreakDown = json_decode($orders->delivery_billBreakdown);
        $priceEstimate = json_decode($orders->delivery_estimatedPrice);

        $data = $this->processTaskCreationData($billBreakDown,$priceEstimate);

        $response = $this->postData($this->baseUrl , '/create_task_via_vendor',$data);

        if(isset($response['success']) && $response['success']){
            return $response;
        }else{
            return ['success' => false , 'message' => $response['message']];
        }

    }





    private function getCost($data){

        $data = '{
              "custom_field_template": "pricing-template",
              "access_token":"'.$this->accessToken.'",
              "domain_name": "'.$this->domainName.'",
              "timezone": 60,
              "vendor_id": "'.$data['vendor_details']['vendor_id'].'",
              "is_multiple_tasks": 1,
              "layout_type": 0,
              "pickup_custom_field_template": "pricing-template",
              "deliveries": [
                    {
                      "address": "'.$data['address']['deliveryAddress'].'",
                      "name": "'.$data['user']['customerName'].'",
                      "latitude" :"'.$data['address']['deliveryLatitude'].'",
                      "longitude" : "'.$data['address']['deliveryLongitude'].'",
                      "time": "'.$data['address']['deliveryDate'].'",
                      "phone": "'.$data['user']['customerPhone'].'",
                      "has_return_task": false,
                      "is_package_insured": 0
                    }
             ],
              "has_pickup": 1,
              "has_delivery": 1,
              "auto_assignment": 1,
              "user_id": "'.$data['user_id'].'",
              "pickups": [
                    '.$this->processPickUpAddresses($data['address']['pickups']).'
              ],
              "payment_method": 32,
              "form_id": "'.$data['form_id'].'",
              "vehicle_id": "'.$data['vehicle']['vehicle_type'].'",
              "delivery_instruction": "Hey,Please deliver the parcel with safety.Thanks in advance",
              "delivery_images": "https://s3.ap-south-1.amazonaws.com/kwik-project/task_images/kjjX1603884709732-stripeconnect.png",
              "is_loader_required": 0,
              "loaders_amount": 0,
              "loaders_count": 0,
              "is_cod_job": 0,
              "parcel_amount": "'.$data['parcel_amount']['amount'].'"
            }';

        $response = $this->postData($this->baseUrl , '/send_payment_for_task',$data);

        return $response;
    }


    private  function billBreakdown($data){

        $formId  = Cache::get('kwik_form_id');

        $billBreakDownCost  = $this->prepareCostData($data,$formId);

        $response = $this->postData($this->baseUrl , '/get_bill_breakdown',$billBreakDownCost);

        return $response;
    }






    private function prepareShippingData($data,$cartedItem){

        $vendorId    = Cache::get('kwik_vendor_id');
        $formId      = Cache::get('kwik_form_id');
        $userId      = Cache::get('kwik_user_id');

        $vehicleType =  $this->vehicleType($this->accessToken,$vendorId);

        $data = [

            'user' => [
                'customerName'  =>  $data['first_name'] .' '.$data['last_name'],
                'customerEmail' =>  $data['email'],
                'customerPhone' =>  $data['phone_number'],
            ],

            'address' => [
                'deliveryAddress'    =>  $data['address'],
                'deliveryLatitude'   =>  $data['latitude'],
                'deliveryLongitude'  =>  $data['longitude'],
                'deliveryDate'       => \Carbon\Carbon::now(),
                'pickups'            => $this->processSmePickUps($cartedItem['company_id'])
//                'pickups'            => $this->processSmePickUps('bf4afe42-1fe1-11ee-a7ce-acde48001122')

            ],

            'vendor_details' => [
                'vendor_id' => $vendorId,
            ],

            'vehicle' => [
                'vehicle_type' => $vehicleType->data[2]->vehicle_id,
            ],
            'token' => [
                'access_token' => $this->accessToken,
            ],

            'parcel_amount' => [
                'amount' => $cartedItem['amount']
//                'amount' => 40000
            ],

            'form_id'=>  $formId,

            'user_id' =>  $userId

        ];

        return $data;
    }



    private function prepareCostData($data , $formId){

        $costData =  '{
                  "access_token": "'.$this->accessToken.'",
                  "benefit_type": null,
                  "amount": "'.$data['payload']->data->per_task_cost.'",
                  "insurance_amount": 0,
                  "total_no_of_tasks": "'.$data['payload']->data->total_no_of_tasks.'",
                  "pickup_time": "'.\Carbon\Carbon::now().'",
                  "user_id": 1,
                  "form_id": "'.$formId.'",
                  "promo_value": null,
                  "domain_name": "'.$this->domainName.'",
                  "credits": 0,
                  "total_service_charge":"'.$data['payload']->data->total_service_charge.'",
                  "vehicle_id": 4,
                  "delivery_images": "https://s3.ap-south-1.amazonaws.com/kwik-project/task_images/wPqj1603886372690-stripeconnect.png",
                  "is_loader_required": 0,
                  "loaders_amount":"'.$data['payload']->data->loaders_amount.'",
                  "loaders_count":"'.$data['payload']->data->loaders_count.'",
                  "is_cod_job":"'.$data['payload']->data->is_cod_job.'",
                  "parcel_amount":"'.Cache::get('parcel_amount').'",
                  "delivery_charge_by_buyer": 0,
                  "delivery_instruction": "Hey,Please handover parcel with safety.\nThanks"
            }';



        return  $costData;
    }




    private function vehicleType($token,$vendorId){

         $url = $this->baseUrl.'/getVehicle?access_token='.$token.'&is_vendor='.$vendorId.'&size=1';

        $response = Http::withHeaders(['accept' =>'application/json','content-type' => 'application/json'])->get($url);

        $result = json_decode($response);

        return    $result;
    }




    private function postData($baseUrl, $uri, $data=[]){

         try{
             $curl = curl_init();

             curl_setopt_array($curl, array(
                 CURLOPT_URL => $baseUrl.$uri,
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_ENCODING => '',
                 CURLOPT_MAXREDIRS => 10,
                 CURLOPT_TIMEOUT => 0,
                 CURLOPT_FOLLOWLOCATION => true,
                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                 CURLOPT_CUSTOMREQUEST => 'POST',
                 CURLOPT_POSTFIELDS =>$data,
                 CURLOPT_HTTPHEADER => array(
                     'Accept: application/json',
                     'Content-Type: application/json'
                 ),
             ));

             $response = curl_exec($curl);


             curl_close($curl);

             $res = json_decode($response);

             if(isset($res->status) && $res->status == 200){

                 return [
                     'success' => true,
                     'message' => $res->message,
                     'payload' => $res,
                 ];
             }else{

                 return [
                     'success' => false,
                     'message' => $res->message ?? 'An issue occurred',
                     'payload' => [],
                 ];

             }
         }catch (Exception $exception){

             return [
                 'success' => false,
                 'message' => $exception->getMessage(),
                 'payload' => [],
             ];

        }

    }


    private function processSmePickUps($smeUuid){

        $db = DB::connection('core_mysql');

        $company = $db->table("companies")->where('uuid',$smeUuid)->first();


        if(!$company){
            return null;
        }

        $users = $db->table("users")->where('company_id',$company->id)->get();

        foreach($users  as $index => $user){
            $data[] =    [
                'pickupDate'     => \Carbon\Carbon::now(),
                'pickupAddress'  => isset($user->company->extra_data->locations) ? $user->company->extra_data['location']['address'] : 'Ikeja Lagos',
                'pickupSmeName'  => $user->firstname.' '.$user->lastname,
                'pickupSmePhone' => $user->phone,
                'pickupSmeEmail' => $user->email,
                "latitude"       => isset($user->company->extra_data->locations) ? $user->company->extra_data['location']['latitude'] : '6.6018',
                "longitude"      => isset($user->company->extra_data->locations) ? $user->company->extra_data['location']['longitude'] : '3.3515',
            ];
        }

        return $data;
}

    private function processPickUpAddresses($pickupAddresses){

        //return $pickupAddresses;
        $addressList = [];
        $addressListString = null;

        if(!is_null( $pickupAddresses)){

            foreach($pickupAddresses as $address){

                $eachAddress =   '{
                          "address": "'.$address['pickupAddress'].'",
                          "name":"'.$address['pickupSmeName'].'",
                          "latitude" : "'.$address['latitude'].'",
                          "longitude" : "'.$address['longitude'].'",
                          "time": "'.$address['pickupDate'].'",
                          "phone": "'.$address['pickupSmePhone'].'",
                          "email": "'.$address['pickupSmeEmail'].'"
                        }';
                array_push($addressList ,  $eachAddress);

            }

            //convert to string
            $addressListString =  implode(',', $addressList);
        }
        return   $addressListString ;
    }


    private function processTaskCreationData($billBreakDown,$priceEstimate){

        $vendorId = Cache::get('kwik_vendor_id');

        $pickUpAddress =  $priceEstimate->pickups[0]->address;
        $pickUpName =  $priceEstimate->pickups[0]->name;
        $pickUpLatitude =  $priceEstimate->pickups[0]->latitude;
        $pickUpLongitude =  $priceEstimate->pickups[0]->longitude;
        $pickUptime = \Carbon\Carbon::now(); // $priceEstimate->pickups[0]->time;
        $pickUpPhone =  $priceEstimate->pickups[0]->phone;
        $pickUpEmail =  $priceEstimate->pickups[0]->email;


        $deliveryAddress =  $priceEstimate->deliveries[0]->address;
        $deliveryName =  $priceEstimate->deliveries[0]->name;
        $deliveryLatitude =  $priceEstimate->deliveries[0]->latitude;
        $deliveryLongitude =  $priceEstimate->deliveries[0]->longitude;
        $deliverytime = \Carbon\Carbon::now();// $priceEstimate->deliveries[0]->time;
        $deliveryPhone =  $priceEstimate->deliveries[0]->phone;


        $insuranceAmount      = $priceEstimate->insurance_amount;
        $totalNumberOfTasks   = $priceEstimate->total_no_of_tasks;
        $totalServiceCharge   = $priceEstimate->total_service_charge;

        $amount               = $priceEstimate->per_task_cost;
        $isLoaderRequired     = $priceEstimate->is_loader_required;
        $isLoaderAmount       = $priceEstimate->loaders_amount;
        $isLoaderCount        = $priceEstimate->loaders_count;
        $deliveryInstruction  = $priceEstimate->delivery_instruction;

        $surgePricing = $billBreakDown->SURGE_PRICING;
        $surgeType = $billBreakDown->SURGE_TYPE;
        $parcelAMount = $billBreakDown->PARCEL_AMOUNT;

        $data =  '{
                  "domain_name": "'.$this->domainName.'",
                  "access_token": "'.$this->accessToken.'",
                  "vendor_id": "'.$vendorId.'",
                  "is_multiple_tasks": 1,
                  "fleet_id": "",
                  "latitude": 0,
                  "longitude": 0,
                  "timezone": 60,
                  "has_pickup": 1,
                  "has_delivery": 1,
                  "pickup_delivery_relationship": 0,
                  "layout_type": 0,
                  "auto_assignment": 1,
                  "team_id": "",
                  "parcel_amount": "'.$parcelAMount.'",
                  "pickups": [
                    {
                      "address": "'.$pickUpAddress.'",
                      "name": "'.$pickUpName.'",
                      "latitude": "'.$pickUpLatitude.'",
                      "longitude": "'.$pickUpLongitude.'",
                      "time": "'.$pickUptime.'",
                      "phone": "'.$pickUpPhone.'",
                      "email": "'.$pickUpEmail.'"
                    }
                  ],
                  "deliveries": [
                    {
                      "address": "'.$deliveryAddress.'",
                      "name": "'.$deliveryName.'",
                      "latitude": "'.$deliveryLatitude.'",
                      "longitude": "'.$deliveryLongitude.'",
                      "time": "'.$deliverytime.'",
                      "phone": "'.$deliveryPhone.'",
                      "has_return_task": false,
                      "is_package_insured": 0,
                      "hadVairablePayment": 1,
                      "hadFixedPayment": 0
                    }
                  ],
                  "insurance_amount": "'.$insuranceAmount.'",
                  "total_no_of_tasks": "'.$totalNumberOfTasks.'",
                  "total_service_charge": "'.$totalServiceCharge.'",
                  "payment_method": 32,
                  "amount": "'.$amount.'",
                  "surge_cost": 7,
                  "surge_type": 3,
                  "is_cod_job": 0,
                  "cash_handling_charges": 0,
                  "cash_handling_percentage": 0,
                  "net_processed_amount": 0,
                  "kwister_cash_handling_charge": "0",
                  "delivery_charge_by_buyer": 1,
                  "delivery_charge": 0,
                  "collect_on_delivery": 0,
                  "delivery_instruction": "'.$deliveryInstruction.'",
                  "loaders_amount": "'.$isLoaderAmount.'",
                  "loaders_count":"'.$isLoaderCount.'",
                  "is_loader_required":"'.$isLoaderRequired.'",
                  "delivery_images": "https://s3.ap-south-1.amazonaws.com/kwik-project/task_images/wPqj1603886372690-stripeconnect.png",
                  "vehicle_id": 4
                }';


      return $data;
    }




}