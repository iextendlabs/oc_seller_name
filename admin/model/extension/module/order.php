<?php
class ModelExtensionModuleOrder extends Model {

	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";
		
		$sql .= " WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['date_start'] && $data['date_end'])) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($data['date_start']) . "' AND '" . $this->db->escape($data['date_end']) . "' ";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);


		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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
	
	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";
		
		$sql .= " WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['date_start'] && $data['date_end'])) {
			$sql .= " AND date_added BETWEEN  '" . $this->db->escape($data['date_start']) . "' AND '" . $this->db->escape($data['date_end']) . "' ";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
