<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');
        App::get_ci()->load->model('Login_model');
        App::get_ci()->load->model('Post_model');
        App::get_ci()->load->model('Boosterpack_model');
        App::get_ci()->load->model('Like_model');

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id){ // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }

    public function comment(int $post_id, string $message) // or can be App::get_ci()->input->post('news_id') , but better for GET REQUEST USE THIS ( tests )
    {
        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $post_id = intval($post_id);

        if (empty($post_id) || empty($message)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);

            Comment_model::create(Comment_model::prepareData($post_id, $message));

        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        $posts =  Post_model::preparation($post, 'full_info');

        return $this->response_success(['post' => $posts]);
    }

    public function login()
    {
        // But data from modal window sent by POST request.  App::get_ci()->input...  to get it.
        if ($this->is_post()) {
            $user_id = User_model::authenticate(
                $this->get_input_stream('login'),
                $this->get_input_stream('password')
            );

            Login_model::start_session($user_id);
        }

        if (User_model::is_logged()) {
            return $this->response_success(['user' => User_model::get_session_id()]);
        } else {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
    }

    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    /**
     * @return mixed
     */
    public function add_money()
    {
        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        if ($this->is_post()) {
            if ($this->get_input_stream('sum') < 1) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
            }

            $result = $this->response_success(['amount' => $user->add_money($this->get_input_stream('sum'))]);
        } else {
            $result = $this->response_success(['amount' => $user->add_money(rand(1,55))]);
        }

        return $result;
    }

    public function buy_boosterpack()
    {
        if ($this->is_post()) {
            $result = Boosterpack_model::winning_likes($this->get_input_stream('id'));

            return $this->response_success(['amount' => $result]);
        } else {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_ACCESS);
        }
    }

    public function like(int $relation_id, string $type = 'post')
    {
        Like_model::create(Like_model::prepareData($relation_id, $type));

        return $this->response_success(['likes' => Like_model::like_counter($relation_id, $type)]);
        // Колво лайков под постом \ комментарием чтобы обновить
    }

    /**
     * @return bool
     */
    private function is_post()
    {
        return App::get_ci()->input->method(TRUE) === 'POST';
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function get_input_stream(string $key)
    {
        return json_decode(file_get_contents('php://input'), true)[$key];
    }
}
