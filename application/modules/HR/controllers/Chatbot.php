<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chatbot controller — HR admin assistant.
 *
 * Exposes a single AJAX endpoint (`ask`) that the dashboard chat widget posts
 * a natural-language question to. Restricted to admin/manager roles because it
 * surfaces location-wide HR data.
 */
class Chatbot extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->load->model('Chatbot_model');
    }

    /**
     * AJAX: answer a natural-language HR question.
     * POST param: question
     * Returns JSON: {status, answer, columns, rows, intent, range}
     */
    public function ask()
    {
        // Only admins/managers may query location-wide HR data.
        if (!($this->ion_auth->is_admin() || $this->ion_auth->in_group('manager'))) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'answer' => 'You are not authorised to use the HR assistant.',
                ]));
            return;
        }

        $question = (string) $this->input->post('question', true);

        try {
            $result = $this->Chatbot_model->answer($question);
            $payload = array_merge(['status' => 'success'], $result);
        } catch (Throwable $e) {
            log_message('error', 'Chatbot::ask failed: ' . $e->getMessage());
            $payload = [
                'status' => 'error',
                'answer' => 'Something went wrong while looking that up. Please try rephrasing your question.',
                'columns' => [],
                'rows' => [],
                'intent' => 'error',
                'range' => '',
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
