<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this -> load -> model('admin_model');
        $this -> load -> model('blog_model');
        $this -> load -> model('message_model');
        $this -> load -> model('comment_model');
    }

    public function login(){
        $this->load->view('admin/login');
    }

    public function logout(){
        $this -> session -> unset_userdata('admin');
        redirect('admin/login');
    }

    public function check_login(){
        //1. 接数据
        $admin_name = $this -> input -> post('admin_name');
        $admin_pwd = $this -> input -> post('admin_pwd');


        //2. 查数据
        $this -> load -> model('admin_model');
        $row = $this -> admin_model -> get_by_name_pwd($admin_name, $admin_pwd);

        //跳转
        if($row){
            $this -> session -> set_userdata('admin', $row);
            $this->load->view('admin/admin-index');
        }else{
            $this->load->view('admin/login');
        }
    }

    public  function admin_mgr(){
        $offset = $this -> uri -> segment(3);
        if($offset == ''){
            $offset = 0;
        }

        $this->load->library('pagination');

        $config['base_url'] = 'admin/admin_mgr';
        $config['total_rows'] = $this -> admin_model -> get_admin_count();
        $config['per_page'] = 15;
        $config['uri_segment'] = 3;
//        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);

        $this -> load -> model('admin_model');

        $result = $this -> admin_model -> get_admin_by_page($config['per_page'], $offset);
//        if($result){
            $data = array(
                'admins' => $result,
                'total_rows' => $config['total_rows']
            );
            $this -> load -> view('admin/admin-mgr', $data);
//        }

    }

    public  function blog_mgr(){
        $offset = $this -> uri -> segment(3);
        if($offset == ''){
            $offset = 0;
        }

        $this->load->library('pagination');

        $config['base_url'] = 'admin/blog_mgr';
        $config['total_rows'] = $this -> blog_model -> get_blog_count();
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
//        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);


        $result = $this -> blog_model -> get_blog_by_page($config['per_page'], $offset);
//        if($result){
        $data = array(
            'blogs' => $result,
            'total_rows' => $config['total_rows']
        );

        $this -> load -> view('admin/blog-mgr', $data);
//        }

    }

    public function blog_mgr2(){

        $this -> load -> view('admin/blog-mgr2');
    }

    public function get_blogs(){
        $page = $this -> input -> get('page');
        $offset = ($page - 1) * 6;

        $blogs = $this -> blog_model -> get_by_page($offset);

        $totalCount = $this -> blog_model -> get_total_count();


        $res = array(
            'data' => $blogs,
            'isEnd' => ceil($totalCount/6) == $page ? true : false
        );

        echo json_encode($res);
    }

    public  function  delete_admin(){
        $admin_id = $this -> input -> get('admin_id');
        $this -> load -> model('admin_model');
        $this -> admin_model -> delete($admin_id);
        $this -> admin_mgr();
    }

    public function post_blog(){
        $this -> load -> view('admin/blog-post');
    }

    public function save_blog(){
        $admin_id = $this -> input -> post('admin_id');
        $title = htmlspecialchars($this -> input -> post('title'));
        $content = $this -> input -> post('content');

        //验证

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '3072';
        $config['file_name'] = date("YmdHis") . '_' . rand(10000, 99999);
//

        $this->load->library('upload', $config);
        $this->upload->do_upload('photo');
        $upload_data = $this->upload->data();
        if ( $upload_data['file_size'] > 0 )
        {
            $photo_url = 'uploads/'.$upload_data['file_name'];//.$upload_data['file_ext'];
            $rows = $this -> blog_model -> save($title, $content, $photo_url, $admin_id);
            if($rows > 0){
                redirect('admin/blog_mgr2');
            }
        }


    }

    public function delete_blog(){
        $blog_id = $this -> input -> get('blog_id');
        $rows =  $this -> blog_model -> delete($blog_id);
        if($rows > 0){
            echo 'success';
        }else{
            echo 'fail';
        }
    }
    public function delete_all_blog(){
        $blog_ids = $this -> input -> get('anums');
        $rows=$this ->blog_model ->delete_all($blog_ids);
        if($rows>0){
            echo 'success';
        }else{
            echo 'fail';
        }

    }

    public function edit_blog(){
        $blog_id = $this -> input -> get('blog_id');
        $blog = $this -> blog_model -> get_by_id($blog_id);
        if($blog){
            $this -> load -> view('admin/blog-edit', array(
                'blog' => $blog
            ));
        }
    }

    public function update_blog(){
        $blog_id = $this -> input -> post('blog_id');
        $title = htmlspecialchars($this -> input -> post('title'));
        $content = $this -> input -> post('content');

        //验证

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '3072';
        $config['file_name'] = date("YmdHis") . '_' . rand(10000, 99999);
//

        $this->load->library('upload', $config);
        $this->upload->do_upload('photo');
        $upload_data = $this->upload->data();
        if ( $upload_data['file_size'] > 0 )
        {
            $photo_url = 'uploads/'.$upload_data['file_name'];//.$upload_data['file_ext'];
            $rows = $this -> blog_model -> update($title, $content, $photo_url, $blog_id);
            if($rows > 0){
                redirect('admin/blog_mgr2');
            }
        }


    }
    public function message_mgr(){

        $this -> load -> view('admin/message-mgr');
    }
    public function get_message(){
        $page = $this -> input -> get('page');
        $offset = ($page - 1) * 6;

        $messages = $this -> message_model -> get_by_page($offset);

        $totalCount = $this -> message_model -> get_total_count();


        $res = array(
            'data' => $messages,
            'isEnd' => ceil($totalCount/6) == $page ? true : false
        );

        echo json_encode($res);
    }
    public function delete_message(){
        $message_id = $this -> input -> get('message_id');
        $rows =  $this -> message_model -> delete($message_id);
        if($rows > 0){
            echo 'success';
        }else{
            echo 'fail';
        }
    }
    public function delete_all_message(){
        $message_ids = $this -> input -> get('anums');
        $rows=$this ->message_model ->delete_all($message_ids);
        if($rows>0){
            echo 'success';
        }else{
            echo 'fail';
        }

    }
    public function reply_edit(){
        $message_id = $this -> input -> get('message_id');

        $message=$this -> message_model ->get_by_id($message_id);
         if( $message){
        $this -> load -> view('admin/reply-edit', array(
            'message' => $message
        ));
        }
    }
    public function reply_save(){
        $message_id = $this -> input -> post('message_id');
        $reply = $this -> input -> post('reply');

        $rows=$this -> message_model -> update($message_id,$reply);
        if($rows > 0){
            redirect('admin/message_mgr');
        }

    }
    public function get_comment(){
        $page = $this -> input -> get('page');
        $offset = ($page - 1) * 6;

        $comment = $this -> comment_model -> get_by_page($offset);


        $totalCount = $this -> comment_model -> get_total_count();


        $res = array(
            'data' => $comment,
            'isEnd' => ceil($totalCount/6) == $page ? true : false
        );


        echo json_encode($res);
    }
    public function comment_mgr(){

        $this -> load -> view('admin/comment-mgr');
    }
}



















