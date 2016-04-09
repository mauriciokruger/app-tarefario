<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topic extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		//$this->output->enable_profiler(TRUE);
	}



	public function populate($rows = 2500 )
	{

		$this->db->query('TRUNCATE TABLE `topics`');
		echo "<p>Truncated done</p>";

		$title = "This is an example topic title number: ";
		$msg = 'Here is a showcase of a card using several different items. It begins with the list card element, utilizing the item-avatar list item, an item-body element for images and text, and a footer with the item-divider classname.';

		for($i=1; $i<=$rows; $i++){

			$sql = "INSERT INTO `sistema`.`tarefas` (`id_tarefa`, `nome_tarefa`, `descricao`, `id_user`, `data_registro`) ";
			$sql .= "VALUES (NULL, '$title $i', '$msg', 'Usr#$i', CURRENT_TIMESTAMP); ";
			$this->db->query($sql);

		}
		echo "<p>Data: ".($i-1). " recores created!</p>";
	}


	public function listtopic()
	{

		$params =  $this->input->get();
		$where = "";

		if(array_key_exists('after', $params)){
			log_message('info', 'after : ' . $params['after']);
			$where = "t.id_tarefa > " . $params['after']. ' AND ';
		}

		if(array_key_exists('before', $params)){
			log_message('info', 'before : ' . $params['before']);
			$where = "t.id_tarefa < " . $params['before'] . ' AND ';
		}

		$sql = "
		SELECT t.id_tarefa AS tarefa_track, t.nome_tarefa AS tarefa_nome, us.nome AS nome_usuario, t.data_registro AS tarefa_data, t.id_user AS tarefa_usuario FROM tb_tarefas AS t
		INNER JOIN tb_tarefas_responsavel AS r
		ON t.id_tarefa = r.id_tarefa
		INNER JOIN tb_user_id_unico AS u
		ON t.id_user = u.id_unica
		INNER JOIN tb_user as us
		ON u.id_referencia = us.id_user
		WHERE $where id_responsavel = 16
		ORDER BY t.id_tarefa DESC LIMIT 15";
		log_message('info', $sql);

		$query = $this->db->query($sql);
		$result = $query->result();
		if($query->num_rows()>0)
		{
			echo json_encode($result);
		}


	}


	public function newtopic()
	{
		
		$data = json_decode(file_get_contents('php://input'),true);
		if(!$data){
			echo "Please enter informations";
			exit;
		}

		//log_message('info', print_r($data['params'], true));			
		
		log_message('info', print_r($data['params'], true));

		if($this->db->insert('tb_tarefas', $data['params'] ))
		{
			echo "success";
			exit;
		}else{
			echo "There is some error, can't push data into Database";
			exit;
		}
	}

	public function detail()
	{

		$data = json_decode(file_get_contents('php://input'),true);
		log_message('info', print_r($data['id'], TRUE));
		$sql = "SELECT t.id_tarefa AS tarefa_track, us.nome AS nome_usuario, t.id_user AS id_user, t.nome_tarefa AS tarefa_nome, t.descricao AS descricao_tarefa, t.data_registro AS tarefa_data, t.id_user AS tarefa_usuario FROM tb_tarefas AS t
		INNER JOIN tb_user_id_unico AS u
		ON t.id_user = u.id_unica
		INNER JOIN tb_user as us
		ON u.id_referencia = us.id_user
		WHERE t.id_tarefa = " . $data['id'];
		log_message('info', $sql);

		$query = $this->db->query( $sql );
		$result = $query->result();

		if($query->num_rows()>0)
		{
			echo json_encode($result);

		}else{
			echo "Error";
		}
		
	}

public function mostraInteracao()
	{

		$params =  json_decode(file_get_contents('php://input'),true);
		$id = $params['params'];

		$sql = "SELECT t.id_tarefa AS tarefa_track, t.data_time AS data_time, t.descricao AS descricao_tarefa, us.nome AS nome_usuario 
		FROM tb_tarefas_interacao AS t
		INNER JOIN tb_user_id_unico AS u
		ON t.id_user = u.id_referencia
		INNER JOIN tb_user as us
		ON u.id_referencia = us.id_user
		WHERE t.id_tarefa = $id ";
		
		$query = $this->db->query($sql);
		$result = $query->result();
		if($query->num_rows()>0)
		{	
			echo json_encode($result);
		}


	}

}
