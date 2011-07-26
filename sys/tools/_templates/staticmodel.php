<textarea rows="10" cols="50" style="border: none; width: 100%;">
function get($ID) {
	$query = $this->db->query(<?php echo $get; ?>);
	return $query->row(0);
}

function getAll($where) {
	$query = $this->db->query(<?php echo $getall; ?>);
	return $query->result();
}

function add() {
	//add in the fields to be added
	$fields = array(<?php 
						$fields = ""; 
						foreach($tableFields as $f) {
							if(!$f->relation) {
								$fields .=  "'$f->name', "; 
							}
						} 
						echo substr($fields, 0, strlen($fields)-2); ?>);
	foreach($fields as $f) {
			$postval = $this->validator->post($f);
			if($postval) {
				$vals .= "$f = '$postval', ";
			}
	}
	$query = $this->db->query(<?php echo $add; ?>);
	return $query->insertID();
}

function update($ID) {
	//add in the fields to be updated
	$fields = array(<?php 
						$fields = ""; 
						foreach($tableFields as $e) { 
							if($e->editable && !$e->relation) {
								$fields .=  "'$e->name', "; 
							}
						} 
						echo substr($fields, 0, strlen($fields)-2); ?>);
	foreach($fields as $f) {
			$postval = $this->validator->post($f);
			if($postval) {
				$vals .= "$f = '$postval', ";
			}
	}
	$vals = substr($vals, 0, strlen($vals)-2);
	$query = $this->db->query(<?php echo $update; ?>);
}

function delete($ID) {
	$query = $this->db->query(<?php echo $delete; ?>);
}
</textarea>