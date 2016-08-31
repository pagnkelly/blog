<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message_model extends CI_Model {


   public function save($username, $email, $content){
      $this -> db -> insert('t_message', array(
          'username' => $username,
          'email' => $email,
          'content' => $content
      ));
   }

   public  function  get_by_username($username){
      return $this -> db -> get_where('t_message', array(
          'username' => $username
      )) -> row();
   }
    public  function  get_by_id($message_id){
        return $this -> db -> get_where('t_message', array(
            'message_id' => $message_id
        )) -> row();
    }
    public function get_by_page($page){
        $this -> db -> select('*');
        $this -> db -> from('t_message message');

        $this -> db -> limit(6, $page);
        $this -> db -> order_by('message.add_time', 'desc');
        return $this -> db -> get() -> result();
    }

    public function get_total_count(){
        return $this -> db -> count_all('t_message');
    }
    public function delete($message_id){
        $this -> db -> delete('t_message', array('message_id' => $message_id));
        return $this -> db -> affected_rows();
    }
    public function delete_all($message_ids){
        $this -> db ->where_in('message_id',$message_ids);
        $this -> db -> delete('t_message');
        return $this -> db -> affected_rows();
    }
    public function update($message_id,$reply){
        $this -> db -> where('message_id',$message_id);
        $this -> db -> update('t_message', array(

            'reply' => $reply
        ));
        return $this -> db -> affected_rows();
    }
}



















