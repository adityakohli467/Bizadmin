<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
    
    function __construct() {
		parent::__construct();
       $this->load->library('session');
       $this->load->model('general_model');
	}
	
	 public function index(){
	     
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
	     
	     $emailSettings = $this->general_model->fetchSmtpSettings('9999','9999');
	     if($emailSettings){
	        $this->configureSMTP($emailSettings); 
	     }
          
           
	    $captcha_question = sprintf("%04d", mt_rand(0, 9999)); // e.g., 4832
        $captcha_answer = md5($captcha_question);
        
        $this->session->set_userdata('captcha_answer', $captcha_answer);
        $data['captcha_question'] = $captcha_question;
        $data['captcha_answer'] = $captcha_answer;
        
        $data['landingPageContent'] = $this->load->view('general/MainWebsitePages/landingpage', [], TRUE);

	     $this->load->view('general/homepage',$data);
	   
	 }
	 
	 // send email once query submmited from main bizadmin website
	  public function submit() {
        $response = ['success' => false, 'message' => ''];

        // Validate form data
        $email = $this->input->post('email', TRUE);
        $contact_number = $this->input->post('contact_number', TRUE);
        $captcha = $this->input->post('captcha', TRUE);
        $captcha_answer = $this->input->post('captcha_answer', TRUE);

        // Validate CAPTCHA
        if (md5($captcha) !== $captcha_answer || !$this->session->userdata('captcha_answer') || $this->session->userdata('captcha_answer') !== $captcha_answer) {
            $response['message'] = 'Invalid CAPTCHA.';
            echo json_encode($response);
            return;
        }

        // Validate other fields
        if (empty($email) || empty($contact_number)) {
            $response['message'] = 'All fields are required.';
            echo json_encode($response);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email address.';
            echo json_encode($response);
            return;
        }
        

        // Prepare email
        $to = 'kaushika@aaria.com.au';
        $subject = 'Bizadmin New Contact Form Submission';
        $message = "
    <strong>Email:</strong> $email<br>
    <strong>Contact Number:</strong> $contact_number";
        

        // Call sendEmail function from MY_Controller
        if ($this->sendEmail($to, $subject, $message)) {
            $response['success'] = true;
            // Clear CAPTCHA session
            $this->session->unset_userdata('captcha_answer');
        } else {
            $response['message'] = 'Failed to send email. Please try again.';
        }

        echo json_encode($response);
    }
    
    // load main website (bizadmin.com.au) inner page dynamically 
    public function load_page() {
        $page = $this->input->get('page', TRUE);
        $valid_pages = ['landingpage', 'suppliers', 'hrm', 'shifts','catering','checklists','cleaning','temperature','documents','cash'];

        if (!in_array($page, $valid_pages)) {
            echo '<p class="text-red-600 text-center">Invalid page requested.</p>';
            return;
        }

     
        $view_file_path = 'general/MainWebsitePages/' . $page;
        if (file_exists(APPPATH . 'views/general/MainWebsitePages/' . $page . '.php')) {
            echo $this->load->view($view_file_path, [], TRUE);
        } else {
            echo '<p class="text-red-600 text-center">Page content not found.</p>';
        }
    }
	
}
?>