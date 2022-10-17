<?php
class ModelExtensionModuleSeller extends Model {
	public function addSeller($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "sellers SET seller_name = '" . $this->db->escape($data['seller_name']) . "', seller_description = '" . $this->db->escape($data['seller_description']) . "', logo = '" . $this->db->escape($data['logo']) . "', banner_image = '" . $this->db->escape($data['banner_image']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', store_name = '" . $this->db->escape($data['store_name']) . "', address = '" . $this->db->escape($data['address']) . "'");
		
	}

	public function editSeller($seller_id, $data) {
		

		$this->db->query("UPDATE " . DB_PREFIX . "sellers SET seller_name = '" . $this->db->escape($data['seller_name']) . "', seller_description = '" . $this->db->escape($data['seller_description']) . "', logo = '" . $this->db->escape($data['logo']) . "', banner_image = '" . $this->db->escape($data['banner_image']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', store_name = '" . $this->db->escape($data['store_name']) . "', address = '" . $this->db->escape($data['address']) . "' WHERE seller_id = '" . (int)$seller_id . "'");
		
		$this->cache->delete('sellers');
	}

	public function deleteSeller($seller_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "sellers` WHERE seller_id = '" . (int)$seller_id . "'");
		
		$this->cache->delete('sellers');
	}

	public function getSeller($seller_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "sellers WHERE seller_id = '" . (int)$seller_id . "'");

		return $query->row;
	}

	public function getSellers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "sellers ";
			
			if (!empty($data['filter_seller'])) {
				$sql .= " WHERE seller_name LIKE '" . $this->db->escape($data['filter_seller']) . "%'";
			}

			$sort_data = array(
				'seller_name',
				'sort_order'

			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY seller_name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

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
	}

	public function getTotalSeller() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sellers");

		return $query->row['total'];
	}

	public function getSellerForProduct() {
		$query = $this->db->query("SELECT seller_id, seller_name FROM " . DB_PREFIX . "sellers");

		return $query->rows;
	}

}