<?

	class contact extends CI_Controller
	{
	
		function __construct()
		{
			parent :: __construct();
			$this->load->helper('captcha');
			$this->settings = $this->system_vars->get_settings();
		}
		
		function index()
		{
		
			$getPage = $this->db->query("SELECT * FROM pages WHERE url = 'contact' LIMIT 1");
			$t = $getPage->row_array();
			
			$random_captcha = rand(10000,99999);
			$this->session->set_userdata('captcha', $random_captcha);
			
			$vals = array
			(
				'word' => $random_captcha,
				'img_path' => './media/captcha/',
				'img_url' => site_url('/media/captcha/')."/",
				'font_path' => './system/fonts/texb.ttf',
				'img_width' => '150',
				'img_height' => 30,
				'expiration' => 7200
			);
			
			$cap = create_captcha($vals);
			$t['captcha'] = $cap['image'];
			
			
			$this->load->view('header');
			$this->load->view('pages/contact', $t);
			$this->load->view('footer');
		
		}
		
		function submit()
		{
		
			$this->form_validation->set_rules('name','Name','xss_clean|trim|required');
			$this->form_validation->set_rules('phone','Phone','xss_clean|trim');
			$this->form_validation->set_rules('email','Email Address','xss_clean|trim|required|valid_email');
			$this->form_validation->set_rules('username','Username','xss_clean|trim');
			$this->form_validation->set_rules('subject','Subject','xss_clean|trim|required');
			$this->form_validation->set_rules('comments','Comments','xss_clean|trim|required');
			$this->form_validation->set_rules('g-recaptcha-response','Captcha','required|trim|xss_clean');
			
			if(!$this->form_validation->run()){
			
				$this->index();
			
			}
			else
			{

                $this->load->model('zendesk');
                $this->zendesk->create_new_ticket(set_value('name'), set_value('email'), set_value('subject'), set_value('comments'), set_value('phone'), set_value('username'));

                $this->session->set_flashdata('response', "Your contact inquiry has been submitted. Please allow up to 48 hours for someone to respond.");
				
				redirect('/contact');
			
			}
		
		}
		
		function check_captcha($string = null)
		{
		
			if($string)
			{
			
				$random_captcha = $this->session->userdata('captcha');
			
				if($string==$random_captcha)
				{
				
					return true;
				
				}
				else
				{
				
					$this->form_validation->set_message('check_captcha', "The security code you entered is invalid");
					return false;
									
				}
			
			}
			else
			{
			
				return true;
			
			}
		
		}
	
	}