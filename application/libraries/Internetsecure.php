<?

	class internetsecure{
	
		function __construct(){
			$this->ci =& get_instance();
		}

        //--- Create profile
		function create_profile($paramaters = array()){
		
			$settings = $this->ci->config->item('internetsecure');
		
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<TranxRequest>
				
				<xxxRequestMode>X</xxxRequestMode> 
				<GatewayID>{$settings['gateway_id']}</GatewayID>
				<Products>1.00::1::001::Card Authorization::{TEST}</Products>
				<xxxName>{$paramaters['first_name']} {$paramaters['last_name']}</xxxName>
				<xxxCompany></xxxCompany>
				<xxxAddress>{$paramaters['address']}</xxxAddress>
				<xxxCity>{$paramaters['city']}</xxxCity>
				<xxxProvince>{$paramaters['state']}</xxxProvince>
				<xxxPostal>{$paramaters['zip']}</xxxPostal>
				<xxxCountry>CA</xxxCountry>
				<xxxPhone></xxxPhone>
				<xxxEmail>{$paramaters['email']}</xxxEmail>
				
				<xxxCard_Number>{$paramaters['card_number']}</xxxCard_Number>
				<xxxCCMonth>{$paramaters['card_exp_month']}</xxxCCMonth>
				<xxxCCYear>{$paramaters['card_exp_year']}</xxxCCYear>
				<CVV2></CVV2>
				<CVV2Indicator>0</CVV2Indicator>
				<xxxTransType>02</xxxTransType>
				<YourVariableName>{$paramaters['id']}</YourVariableName>
				
				<xxxCustomerDB>1</xxxCustomerDB>
			
			</TranxRequest>
			";

			$object = $this->curl_it($xml);

/*
* Testing to debug the IS object returned, issue was no email address provided	

			echo $xml;
			// Parse response

			echo "<pre>";
			print_r($object);
			echo "</pre>";
			
*/			

			
			$transaction_page_response = $object['TranxResponse']['Page'];
			$transaction_response_message = $object['TranxResponse']['Verbiage'];
			
			if(!$this->check_page_status($transaction_page_response))
			{
			
				$array = array();
				$array['error'] = "1";
				$array['message'] = $transaction_response_message;
			
			}
			else
			{
			
				$token = $object['TranxResponse']['Token'];
				
				$array = array();
				$array['error'] = "0";
				$array['token'] = $token;
			
			}
			
			return $array;
		
		}

        function void($transaction_id = null){

            $settings = $this->ci->config->item('internetsecure');

            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <TranxRequest>
                <GatewayID>{$settings['gateway_id']}</GatewayID>
                <xxxTransType>21</xxxTransType>
            </TranxRequest>";

        }

        //--- Capture card info (token)
        function authorize($token = null, $amount = 0, $description = null){

            $settings = $this->ci->config->item('internetsecure');

            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <TranxRequest>
				<xxxRequestMode>X</xxxRequestMode>
				<GatewayID>{$settings['gateway_id']}</GatewayID>
				<Products>{$amount}::1::12345::{$description}::{TEST}</Products>
				<Token>{$token}</Token>
				<xxxTransType>15</xxxTransType>
			</TranxRequest>
			";

            // Parse response
            $object = $this->curl_it($xml, "https://cimb.internetsecure.com/cimbadmin");
            $transaction_response = trim( $object['TranxResponse']['Succeed'] );
/*
            echo "<pre>";
            print_r($object);
            exit;
*/
            if($transaction_response == 'N'){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "Failed to charge credit card. Please check billing profile and try again.";

            }else{
                $array = array();
                $array['error'] = "0";
            }

            $array['source'] = $object;

            return $array;

        }

        //--- Settle a previous transaction
		function settle($token = null, $amount = 0, $description = null)
		{
		
			$settings = $this->ci->config->item('internetsecure');
		
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<TranxRequest>
			
				<GatewayID>{$settings['gateway_id']}</GatewayID>

				<Details>
					<Token>{$token}</Token>
					<xxxAmount>".number_format($amount, 2)."</xxxAmount>
					<xxxSendCustomerEmailReceipt>N</xxxSendCustomerEmailReceipt>
					<xxxSendMerchantEmailReceipt>N</xxxSendMerchantEmailReceipt>
					<xxxDescription>{$description}</xxxDescription>
					<xxxPaymentType>00</xxxPaymentType>
					<xxxTransType>15</xxxTransType>
					<xxxTotalTax>0</xxxTotalTax>
					<xxxMerchantInvoiceNumber>".time()."</xxxMerchantInvoiceNumber>
				</Details>
				
			</TranxRequest> 
			";
			
			// Parse response
			$object = $this->curl_it($xml, "https://cimb.internetsecure.com/cimbadmin");
			$transaction_response = trim( $object['TranxResponse']['Succeed'] );
			
			if($transaction_response == 'N'){
			
				$array = array();
				$array['error'] = "1";
				$array['message'] = "Failed to charge credit card. Please check billing profile and try again.";

            }else{
				$array = array();
				$array['error'] = "0";
			}
			
			return $array;
		
		}

        //--- Make Call
		private function curl_it($XML = '', $url = "https://secure.internetsecure.com/process.cgi")
		{
			$CI =& get_instance();
			if(!$XML) return false;
			else
			{
			
				$request = curl_init($url);
				
				$post_string = "xxxRequestData={$XML}&xxxRequestMode=X";
				
				curl_setopt($request, CURLOPT_HEADER, 0);
				curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
				$post_response = curl_exec($request);
				curl_close ($request);
			    
			   return $CI->system_vars->xml2array($post_response);
			
			}
		
		}

        //--- Check Status
        private function check_page_status($status)
        {

            switch($status)
            {

                case "90000": // Success
                case "02000":
                    return true;
                    break;

                default: // Failed
                    return false;
                    break;

            }

        }
	
	}