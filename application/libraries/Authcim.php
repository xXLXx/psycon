<?

	class authcim
	{
	
		public $test_mode = TRUE;
        public $authnet_login_id = null;
        public $authnet_transaction_key = null;
	
		function __construct($params = array()){

            $this->ci =& get_instance();

            $this->authnet_login_id = $params['login'];
            $this->authnet_transaction_key = $params['key'];

		}
		
		function delete_profile($paymentId="")
		{
		
		
				$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
						<deleteCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
							<merchantAuthentication>
								<name>{$this->authnet_login_id}</name>
								<transactionKey>{$this->authnet_transaction_key}</transactionKey>
							</merchantAuthentication>
							<customerProfileId>{$paymentId}</customerProfileId>
						</deleteCustomerProfileRequest>";
						
				$CurlResponse = $this->curl_it($XML);
				
				//print_r($CurlResponse);
				//exit;
				/* Parse Object, convert to array */
				$MessageArray = (array) $CurlResponse['deleteCustomerProfileResponse']['messages'];
				$DescriptionArray = (array) $MessageArray['message'];
				
				/* Grab only neccessary error checking messages */
				$ResultCode = trim(strtolower($MessageArray['resultCode']));
				
				/* Check for error */
				if($ResultCode!='ok')
				{
				
					$ResultMessage = $DescriptionArray['text'];
				
					return array
					(
						'status'=>false,
						'message'=>$ResultMessage
					);
					
				}
				else
				{
					
					// Parse The Customer Results
					//$ResultMessage = $DescriptionArray['text'];
					
					//$PaymentArray = (array) $CurlResponse['deleteCustomerProfileResponse']['customerPaymentProfileIdList'];
					//$PaymentId = $PaymentArray['numericString'];
				
					return array
					(
						'status'=>true,
						'message'=>"Profile was deleted successfully"
					);
					
				}
	
		}
		
		function get_profile($customer_id)
		{
			$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
					<getCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
					  <merchantAuthentication>
					    <name>{$this->authnet_login_id}</name>
					    <transactionKey>{$this->authnet_transaction_key}</transactionKey>
					  </merchantAuthentication>
					  <customerProfileId>{$customer_id}</customerProfileId>
					</getCustomerProfileRequest>";
					
			$CurlResponse = $this->curl_it($XML);
			
			$MessageArray = (array) $CurlResponse['getCustomerProfileResponse']['messages'];
			$DescriptionArray = (array) $MessageArray['message'];
			
			/* Grab only neccessary error checking messages */
			$ResultCode = trim(strtolower($MessageArray['resultCode']));
			
			/* Check for error */
			if($ResultCode!='ok')
			{
			
				$ResultMessage = $DescriptionArray['text'];
			
				return array
				(
					'status'=>false,
					'message'=>$ResultMessage
				);
				
			}
			else
			{
				
				// Parse The Customer Results
				$ResultMessage = $DescriptionArray['text'];
				
				
			
				return array
				(
					'status'=>true,
					'profile'=>$CurlResponse['getCustomerProfileResponse']['profile']
				);
				
			}
		}
		
		function create_profile($PostData = '')
		{
		
			if(is_array($PostData))
			{
			
				/* Build XML Query */
				$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				
				<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
				
					<merchantAuthentication>
						<name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
					</merchantAuthentication>
					
					<profile>
						
						<merchantCustomerId>".time()."</merchantCustomerId>
						<description>CIM Customer</description>
						<email>".(isset($PostData['email']) ? $PostData['email'] : "")."</email>
						
						<paymentProfiles>
							<billTo>
								<firstName>{$PostData['first_name']}</firstName>
								<lastName>{$PostData['last_name']}</lastName>
								<address>{$PostData['address']}</address>
								<city>{$PostData['city']}</city>
								<state>{$PostData['state']}</state>
								<zip>{$PostData['zip']}</zip>
								<country>{$PostData['country']}</country>
							</billTo>
							<payment>
								<creditCard>
									<cardNumber>{$PostData['card_number']}</cardNumber>
									<expirationDate>{$PostData['card_exp_year']}-{$PostData['card_exp_month']}</expirationDate>
								</creditCard>
							</payment>
						</paymentProfiles>
					
					</profile>
				
				</createCustomerProfileRequest>
				";
				
				$CurlResponse = $this->curl_it($XML);
				
				/* Parse Object, convert to array */
				if(isset($CurlResponse['createCustomerProfileResponse']['messages']))
					$MessageArray = (array) $CurlResponse['createCustomerProfileResponse']['messages'];
				else
					$MessageArray = false;
					
				if(isset($MessageArray['message']))
					$DescriptionArray = (array) $MessageArray['message'];
				else
					$DescriptionArray = false;
				
				/* Grab only neccessary error checking messages */
				if(isset($MessageArray['resultCode']))
					$ResultCode = trim(strtolower($MessageArray['resultCode']));
				else
					$ResultCode = false;
				
				/* Check for error */
				if($ResultCode!='ok')
				{
				
					if(isset($DescriptionArray['text']))
					{
						$ResultMessage = $DescriptionArray['text'];
					}
					elseif(isset($DescriptionArray[0]['text']))
					{
						$ResultMessage = $DescriptionArray[0]['text'];
					}
					elseif(isset($CurlResponse['ErrorResponse']['messages']['message']['text']))
					{
						$ResultMessage = $CurlResponse['ErrorResponse']['messages']['message']['text'];
					}
					else
					{
						$ResultMessage = "There was an error processing you card… Please check your card information";
					}
				
					return array
					(
						'status'=>false,
						'message'=>$ResultMessage
					);
					
				}
				else
				{
					
					// Parse The Customer Results
					$ResultMessage = $DescriptionArray['text'];
					
					$PaymentArray = (array) $CurlResponse['createCustomerProfileResponse']['customerPaymentProfileIdList'];
					$PaymentId = $PaymentArray['numericString'];
					
					// Authorize this account for $1.00
					// If sucess, pass data back…
					// If not, DELETE the account and produce error
					
					$profile_id = $CurlResponse['createCustomerProfileResponse']['customerProfileId'];
					
					$responseObject = $this->authorize_card($profile_id, $PaymentId, '1.00', time(), 'Credit Card Authorization');
					
					if($responseObject['status']=='1')
					{
				
						return array
						(
							'status'=>true,
							'customer_id'=>$profile_id,
							'payment_id'=>$PaymentId
						);
					
					}
					else
					{
					
						return array
						(
							'status'=>false,
							'message'=>$responseObject['message']
						);
					
					}
					
				}
			
			}
			else
			{
			
				return array
				(
					'status'=>false,
					'message'=>'Missing Payment Profile Array'
				);
			
			}
			
		}

        //--- Auth-Only
        public function authorize($ProfileId = '', $BillingId = '', $ChargeAmount = '', $invoiceNumber = '', $Description = ''){
            return $this->charge_card($ProfileId, $BillingId, $ChargeAmount, $invoiceNumber, $Description, 'auth_only');
        }

        //-- Capture/Settle authoirzed transaction
        public function settle($ProfileId = '', $BillingId = '', $ChargeAmount = '', $auth_code = ''){

            //--- profileTransCaptureOnly
            $XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                <createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
                    <merchantAuthentication>
                        <name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
                    </merchantAuthentication>
                    <transaction>
                        <profileTransCaptureOnly>
                            <amount>{$ChargeAmount}</amount>
                            <customerProfileId>{$ProfileId}</customerProfileId>
                            <customerPaymentProfileId>{$BillingId}</customerPaymentProfileId>
                            <approvalCode>{$auth_code}</approvalCode>
                        </profileTransCaptureOnly>
                    </transaction>
                </createCustomerProfileTransactionRequest>";

            $Response = $this->curl_it($XML);

            $ResponseArray = explode(',', $Response['createCustomerProfileTransactionResponse']['directResponse']);

            if($ResponseArray['0'] != '1'){

                return array(
                    'status'=>false,
                    'message'=>$ResponseArray[3]
                );

            }else{

                return array(
                    'status'=>true,
                    'transaction_id'=>$ResponseArray[6],
                    'auth_code'=>$ResponseArray[4]
                );

            }

        }

        //--- Void transaction
        public function void($ProfileId = '', $BillingId = '', $transaction_id = ''){

            //--- profileTransCaptureOnly
            $XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                <createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
                    <merchantAuthentication>
                        <name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
                    </merchantAuthentication>
                    <transaction>
                        <profileTransVoid>
                            <customerProfileId>{$ProfileId}</customerProfileId>
                            <customerPaymentProfileId>{$BillingId}</customerPaymentProfileId>
                            <transId>{$transaction_id}</transId>
                        </profileTransVoid>
                    </transaction>
                </createCustomerProfileTransactionRequest>";

            $Response = $this->curl_it($XML);

            $ResponseArray = explode(',', $Response['createCustomerProfileTransactionResponse']['directResponse']);

            if($ResponseArray['0'] != '1'){

                return array(
                    'status'=>false,
                    'message'=>$ResponseArray[3]
                );

            }else{

                return array(
                    'status'=>true,
                    'transaction_id'=>$ResponseArray[6],
                    'auth_code'=>$ResponseArray[4]
                );

            }

        }

        //--- auth/capture
		function charge_card($ProfileId = '', $BillingId = '', $ChargeAmount = '', $invoiceNumber = '', $Description = '', $transactionType = 'auth_capture')
		{

            switch($transactionType){

                case "auth_capture":
                    $transactionNodeName = "profileTransAuthCapture";
                    break;

                case "auth_only":
                    $transactionNodeName = "profileTransAuthOnly";
                    break;
            }
		
			if($ProfileId&&$BillingId&&$ChargeAmount)
			{
			
				/* Build XML Query */
				$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				
				<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
				
					<merchantAuthentication>
						<name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
					</merchantAuthentication>
					
					<transaction>
					
						<{$transactionNodeName}>
							<amount>{$ChargeAmount}</amount>
							<customerProfileId>{$ProfileId}</customerProfileId>
							<customerPaymentProfileId>{$BillingId}</customerPaymentProfileId>
							<order>
								<invoiceNumber>{$invoiceNumber}</invoiceNumber>
								<description>{$Description}</description>
							</order>
						</{$transactionNodeName}>
					</transaction>
				
				</createCustomerProfileTransactionRequest>
				";
				
				$Response = $this->curl_it($XML);
				
				$ResponseArray = explode(',', $Response['createCustomerProfileTransactionResponse']['directResponse']);
				
				if($ResponseArray['0']!='1')
				{
					return array
					(
						'status'=>false,
						'message'=>$ResponseArray[3]
					);
				}
				else
				{
					return array
					(
						'status'=>true,
						'transaction_id'=>$ResponseArray[6],
						'auth_code'=>$ResponseArray[4]
					);
				}
			
			}
			else
			{
				return array
				(
					'status'=>false,
					'message'=>'Missing essential billing information'
				);
			}
		
		}
		
		function authorize_card($ProfileId = '', $BillingId = '', $ChargeAmount = '', $invoiceNumber = '', $Description = '')
		{
		
			if($ProfileId&&$BillingId&&$ChargeAmount)
			{
			
				/* Build XML Query */
				$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				
				<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
				
					<merchantAuthentication>
						<name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
					</merchantAuthentication>
					
					<transaction>
					
						<profileTransAuthOnly>
							<amount>{$ChargeAmount}</amount>
							<customerProfileId>{$ProfileId}</customerProfileId>
							<customerPaymentProfileId>{$BillingId}</customerPaymentProfileId>
							<order>
								<invoiceNumber>{$invoiceNumber}</invoiceNumber>
								<description>{$Description}</description>
							</order>
						</profileTransAuthOnly>
						
					</transaction>
				
				</createCustomerProfileTransactionRequest>
				";
				
				$Response = $this->curl_it($XML);
				
				$ResponseArray = explode(',', $Response['createCustomerProfileTransactionResponse']['directResponse']);
				
				if($ResponseArray['0']!='1')
				{
					return array
					(
						'status'=>false,
						'message'=>$ResponseArray[3]
					);
				}
				else
				{
					return array
					(
						'status'=>true,
						'transaction_id'=>$ResponseArray[6],
						'auth_code'=>$ResponseArray[4]
					);
				}
			
			}
			else
			{
				return array
				(
					'status'=>false,
					'message'=>'Missing essential billing information'
				);
			}
		
		}
		
		function refund_card($ProfileId = '', $BillingId = '', $RefundAmount = '', $invoiceNumber = '', $Description = '')
		{
		
			if($ProfileId&&$BillingId&&$RefundAmount)
			{
			
				/* Build XML Query */
				$XML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				
				<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">
				
					<merchantAuthentication>
						<name>{$this->authnet_login_id}</name>
						<transactionKey>{$this->authnet_transaction_key}</transactionKey>
					</merchantAuthentication>
					
					<transaction>
					
						<profileTransRefund>
							<amount>{$RefundAmount}</amount>
							<customerProfileId>{$ProfileId}</customerProfileId>
							<customerPaymentProfileId>{$BillingId}</customerPaymentProfileId>
							<order>
								<invoiceNumber>{$invoiceNumber}</invoiceNumber>
								<description>{$Description}</description>
							</order>
						</profileTransRefund>
						
					</transaction>
				
				</createCustomerProfileTransactionRequest>
				";
				
				$Response = $this->curl_it($XML);
				
				$ResponseArray = explode(',', $Response['directResponse']);
				
				if($ResponseArray['0']!='1')
				{
				
					return array
					(
						'status'=>false,
						'message'=>$ResponseArray[3]
					);
					
				}
				else
				{
				
					return array
					(
						'status'=>true,
						'transaction_id'=>$ResponseArray[6],
						'auth_code'=>$ResponseArray[4]
					);
					
				}
			
			}
			else
			{
				return array
				(
					'status'=>false,
					'message'=>'Missing essential billing information'
				);
			}
		
		}
		
		function curl_it($XML = '')
		{
			$CI =& get_instance();
			if(!$XML) return false;
			else
			{
			
				if($this->test_mode) $aURL = "https://apitest.authorize.net/xml/v1/request.api";
				else $aURL = "https://api.authorize.net/xml/v1/request.api";
			
				$ch = curl_init($aURL);
				// curl_setopt($ch, CURLOPT_MUTE, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $XML);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
			    
			    return $CI->system_vars->xml2array($output);
			
			}
		
		}
	
	}

?>