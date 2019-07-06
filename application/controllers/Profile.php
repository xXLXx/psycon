<?

	class profile extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			// Load reader data
			$this->load->model('reader');
			$this->reader->init($this->uri->segment('2'));
						
		}
		
		function view()
		{
            $array['testis'] = $this->reader->get_testimonials(null,1);
			$this->load->view('header');
			$this->load->view('profile/profile_view',$array);
			$this->load->view('footer');
		
		}
		
		function send_question($username)
		{
		
			if(!isset($this->member->data['id']))
			{
			
				$this->session->set_flashdata('response', "You must be logged in before you can send a question to any expert.");
				$this->session->set_userdata('redirect', $this->uri->uri_string());
				
				redirect('/register/login');
			
			}
			else
			{
			
				$t['packages'] = $this->reader->get_email_packages();
		
				$this->load->view('header');
				$this->load->view('profile/send_question', $t);
				$this->load->view('footer');
			
			}
		
		}
		
		function submit_question($username)
		{
		
			$this->form_validation->set_rules("birth_time","Birth Time","required|trim|xss_clean");
			$this->form_validation->set_rules("birth_place","Birth Place","required|trim|xss_clean");
			$this->form_validation->set_rules("package","Package","required|trim|xss_clean");
			$this->form_validation->set_rules("questions","Question(s)","required|trim|xss_clean");
			$this->form_validation->set_rules("instructions","Special Instructions","trim|xss_clean");
			$this->form_validation->set_rules("additional_info","Additional Information","trim|xss_clean");
			
			if(!$this->form_validation->run())
			{
			
				$this->send_question($username);
			
			}
			else
			{
			
				// Save to session
				$this->session->set_userdata('birth_time', set_value('birth_time'));
				$this->session->set_userdata('birth_place', set_value('birth_place'));
				$this->session->set_userdata('package', set_value('package'));
				$this->session->set_userdata('questions', set_value('questions'));
				$this->session->set_userdata('instructions', set_value('instructions'));
				$this->session->set_userdata('additional_info', set_value('additional_info'));
				
				redirect("/profile/{$username}/preview_question");
			
			}
		
		}
		
		function preview_question($username)
		{
		
			$t = $this->reader->get_email_package($this->session->userdata('package'));
		
			$this->load->view('header');
			$this->load->view('profile/send_question_preview', $t);
			$this->load->view('footer');
		
		}
		
		function confirm_question($username)
		{
		
			$email_balance = $this->member_funds->email_balance();
			
			$package = $this->reader->get_email_package($this->session->userdata('package'));
			$total_charge = $package['price'];
			
			if($email_balance < $total_charge)
			{
			
				$this->session->set_userdata('redirect', "/profile/{$username}/preview_question");
				$this->session->set_flashdata('error', "You do not have enough email credits to submit this order. Please fund your account with more email credits using the form below and we will redirect you back to the previous page to complete your email reading order.");
			
				redirect("/my_account/transactions/fund_your_account");
			
			}
			else
			{
			

				
				// Calculate Completion days and date
				$totalDays = number_format(($package['total_questions']*$this->reader->data['email_total_days']));
						
				if($totalDays > 3) $totalDays = 3;
				
				$dateOfCompletion = date("Y-m-d", strtotime("+{$totalDays} days"));
				
				// Insert question into database
				$insert = array();
				$insert['profile_id'] = $this->reader->data['id'];
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['package_title'] = $package['title'];
				$insert['total_questions'] = $package['total_questions'];
				$insert['name'] = $this->member->data['first_name']." ".$this->member->data['last_name'];;
				$insert['dob'] = $this->member->data['dob'];
				$insert['birth_time'] = $this->session->userdata('birth_time');
				$insert['birth_place'] = $this->session->userdata('birth_place');
				$insert['questions'] = $this->session->userdata('questions');
				$insert['instructions'] = $this->session->userdata('instructions');
				$insert['additional_info'] = $this->session->userdata('additional_info');
				$insert['price'] = $total_charge;
				$insert['completion_date'] = $dateOfCompletion;
			
				$this->db->insert('questions', $insert);
				$question_id = $this->db->insert_id();

                //questions
                $this->member_funds->process_reading($total_charge, 'email', $question_id, $this->reader->data['id'],$question_id, $this->reader->data['region']);
				//$this->member_funds->use_email_funds($total_charge);
				
				// Unset session vars
				$this->session->unset_userdata('birth_time');
				$this->session->unset_userdata('birth_place');
				$this->session->unset_userdata('package');
				$this->session->unset_userdata('questions');
				$this->session->unset_userdata('instructions');
				$this->session->unset_userdata('additional_info');
				
				// Redirect with confirmation
				$this->session->set_flashdata('response', "Your email reading request has been submitted to this reader.");
				
				redirect("/my_account/email_readings/client_view/{$question_id}");

			}
		
		}
		
		function page_reader($username)
		{
		
			if(!isset($this->member->data['id']))
			{
			
				$this->session->set_flashdata('response', "You must be logged in before you can page a reader. Use the form below to login and we will redirect you back to the previous page.");
				$this->session->set_userdata('redirect', $this->uri->uri_string());
				
				redirect('/register/login');
			
			}
			else
			{
		
				$this->load->view('header');
				$this->load->view('profile/page_reader');
				$this->load->view('footer');
			
			}
		
		}
		
		function page_reader_submit($username)
		{
		
			$this->form_validation->set_rules('when','When do you want to chat','required|trim|xss_clean');
			$this->form_validation->set_rules('date1','Date & Time','trim|xss_clean');
			$this->form_validation->set_rules('date2','Alternative Date & Time','trim|xss_clean');
			$this->form_validation->set_rules('comments','Comments','required|trim|xss_clean');
			$this->form_validation->set_rules('g-recaptcha-response','Captcha','required|trim|xss_clean');
			
			if(!$this->form_validation->run())
			{
			
				$this->page_reader($username);
			
			}
			else
			{
			
				// Notify the reader
				if(set_value('when')=='now')
				{
					
					$message = "Hello {$this->reader->data['username']}, {$this->member->data['username']} is attempting to page you for a chat they want to do in the next 15 minutes. <p>Comments:<br />".set_value('comments')."</p>";
					
				}
				else
				{
				
					$date1 = date("m/d/Y @ h:i A", strtotime(str_replace("@","",set_value('date1'))));
					$date2 = date("m/d/Y @ h:i A", strtotime(str_replace("@","",set_value('date2'))));
				
					$message = "Hello {$this->reader->data['username']}, {$this->member->data['username']} is attempting to page you for a chat they want to do on either one of these dates/time:<br />{$date1}<br />{$date2}.<p>Comments:<br />".set_value('comments')."</p>";
				
				}
				
				$this->member->notify($this->reader->data['id'], $this->member->data['id'], "Page Request", $message);
				
				$this->session->set_flashdata('response', "{$this->reader->data['username']} has been paged with your comments. ");
				
				redirect("/profile/{$username}");
			
			}
		
		}
	
	}
	