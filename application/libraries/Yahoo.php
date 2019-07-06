<?

    class yahoo
    {
    
    	function weather($zip = null)
		{
		
			$CI =& get_instance();
			$CI->load->library('simplexml');
		
			$url = 'http://weather.yahooapis.com/forecastrss?p='.$zip;
			
			$handle = fopen($url, "rb");
			$xml = stream_get_contents($handle);
			fclose($handle);
			
			$weatherData = $CI->simplexml->xml_parse($xml);
			
			if(strpos(strtolower($weatherData['channel']['description']),'error')===false)
			{
			
				$weatherCode = $weatherData['channel']['item']['yweather:condition']['@attributes']['code'];
				
				switch($weatherCode)
				{ 
				
					case "0":
					case "1":
					case "2":
					case "3":
					case "4":
					$image = 'rain';
					break;
					
					case "5":
					case "6":
					case "7":
					case "13":
					case "14":
					case "15":
					case "16":
					case "17":
					case "41":
					case "42":
					case "43":
					case "46":
					$image = 'snow';
					break;
					
					case "8":
					case "9":
					case "10":
					case "11":
					case "12":
					case "35":
					case "40":
					$image = 'rain';
					break;
					
					case "18":
					case "19":
					case "20":
					case "21":
					case "22":
					case "23":
					case "24":
					case "25":
					case "26":
					case "27":
					case "28":
					case "29":
					case "30":
					case "44":
					$image = 'cloudy';
					break;
					
					case "31":
					case "32":
					case "33":
					case "34":
					$image = 'clear';
					break;
					
					case "31":
					case "32":
					case "33":
					case "34":
					case "36":
					$image = 'clear';
					break;
					
					case "37":
					case "38":
					case "39":
					case "45":
					case "47":
					$image = 'lightning';
					break;
				
				}
				
				// Check if night or day
				$todayAtSeven = strtotime(date("Y-m-d")." 19:00:00");
				if(time()>=$todayAtSeven) $image = "n_".$image;
				else $image = "d_".$image;
				
				$Return['condition'] = $weatherData['channel']['item']['yweather:condition']['@attributes']['text'];
				$Return['temp'] = $weatherData['channel']['item']['yweather:condition']['@attributes']['temp'];
				$Return['city'] = $weatherData['channel']['yweather:location']['@attributes']['city'];
				$Return['region'] = $weatherData['channel']['yweather:location']['@attributes']['region'];
				$Return['image'] = $image;
				
				return $Return;
			
			}
			else
			{
			
				return false;
			
			}
			
		}

        function address_plotter($Address,$City,$State,$Zip = '')
        {

            $CI =& get_instance();
            $YahooAPI = $CI->config->item('yahoo_api');

            $URL = trim("http://where.yahooapis.com/geocode?location=".urlencode(trim("{$Address} {$City} {$State} {$Zip}"))."&flags=j&appid=".urlencode($YahooAPI)."&".$args);
			
            /// CURL The Query
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address_data = json_decode(curl_exec($ch));
            curl_close($ch);
            
            $obj = $address_data->ResultSet;

			if($obj->Error == '0')
			{
			
				// 
				$return = (array) $obj->Results[0];
				
				return array
				(
					'Latitude' => $return['latitude'],
					'Longitude' => $return['longitude']
				);
				
				exit;
			
			}
			else
			{
			
				return false;
			
			}

		}

        function plot_zip($Zip)
        {

             $CI =& get_instance();
            $YahooAPI = $CI->config->item('yahoo_api');

            $URL = trim("http://where.yahooapis.com/geocode?location=".urlencode(trim($Zip))."&flags=j&appid=".urlencode($YahooAPI));
			
            /// CURL The Query
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address_data = json_decode(curl_exec($ch));
            curl_close($ch);
            
            $obj = $address_data->ResultSet;

			if($obj->Error == '0')
			{
			
				// 
				$return = (array) $obj->Results[0];
				
				return array
				(
					'Latitude' => $return['latitude'],
					'Longitude' => $return['longitude'],
					'City' => $return['city'],
					'County' => $return['county'],
					'State' => $return['state'],
					'StateCode' => $return['statecode'],
					'CountryCode' => $return['countrycode'],
					'Country' => $return['country']
				);
				
				exit;
			
			}
			else
			{
			
				return false;
			
			}

	}

        function latlon_range($lat, $lon, $range = 10)
        {

		$lat_range = $range/69.172;
		$lon_range = abs($range/(cos($lat) * 69.172));

		$min_lat = number_format($lat - $lat_range, "4", ".", "");
		$max_lat = number_format($lat + $lat_range, "4", ".", "");
		$min_lon = number_format($lon - $lon_range, "4", ".", "");
		$max_lon = number_format($lon + $lon_range, "4", ".", "");

		return array("min_lon"=>$min_lon,"max_lon"=>$max_lon,"min_lat"=>$min_lat,"max_lat"=>$max_lat);

	}

	function calculate_mileage($lat1, $lat2, $lon1, $lon2)
        {

          $lat1 = deg2rad($lat1);
          $lon1 = deg2rad($lon1);
          $lat2 = deg2rad($lat2);
          $lon2 = deg2rad($lon2);

          // Find the deltas
          $delta_lat = $lat2 - $lat1;
          $delta_lon = $lon2 - $lon1;

          // Find the Great Circle distance
          $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
          $distance = 3956 * 2 * atan2(sqrt($temp),sqrt(1-$temp));

          return round($distance);
       }

    }

?>