<?

	class api_test extends CI_Controller
	{
	
		function __construct()
		{
			parent :: __construct();
		}
		
		function index()
		{
		
			$params = array();
			$params['billing_profile_id'] = "1";
			
			$data = $this->curl_it("http://psycon.owws.com/api/delete_billing_profile/12345", $params);
			
			print_r($data);
		
		}
		
		function charge_profile()
		{
		
			$params = array();
			$params['billing_profile_id'] = "1";
			$params['summary'] = "This is just a test";
			$params['total_charge'] = "5.00";
			
			$data = $this->curl_it("http://psycon.owws.com/api/charge_billing_profile/12345", $params);
			
			print_r($data);
		
		}
		
		function create_profile()
		{
		
			$params = array();
			$params['first_name'] = "Robert";
			$params['last_name'] = "Kehoe";
			$params['address'] = "4940 Barcelona Way";
			$params['city'] = "Colorado Springs";
			$params['state'] = "CO";
			$params['zip'] = "80917";
			$params['country'] = "US";
			$params['card_number'] = "5424000000000015";
			$params['card_exp_month'] = "04";
			$params['card_exp_year'] = "2015";
			
			$data = $this->curl_it("http://psycon.owws.com/api/create_billing_profile/12345", $params);
			
			print_r($data);
		
		}
		
		private function curl_it($post_url = "", $post_values)
		{
		
		    $post_string = "";
	
		    foreach( $post_values as $key => $value )
		    {
					$post_string .= "$key=" . urlencode( $value ) . "&";
		    }
	
		    $post_string = rtrim($post_string, "& ");
		    $request = curl_init($post_url); // initiate curl object
	
		    curl_setopt($request, CURLOPT_HEADER, 0);
		    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
		    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
		    $post_response = curl_exec($request);
		    curl_close ($request);
	
		    return $post_response;
		    
		}
	
	}