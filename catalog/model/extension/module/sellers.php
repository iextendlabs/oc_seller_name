<?php
class ModelExtensionModuleSellers extends Model {
	public function addSeller($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "sellers SET seller_name = '" . $this->db->escape($data['seller_name']) . "', seller_description = '" . $this->db->escape($data['seller_description']) . "', logo = '" . $this->db->escape($data['logo']) . "', banner_image = '" . $this->db->escape($data['banner_image']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', store_name = '" . $this->db->escape($data['store_name']) . "', address = '" . $this->db->escape($data['address']) . "' , meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'");
		
	}
	
	public function getSeller($seller_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sellers WHERE seller_id = '" . (int)$seller_id . "' ");

		return $query->row;
	}

	public function getSellers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "sellers";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalSellers(){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sellers");

		return $query->row['total'];
	}
}