<?php
class ModelExtensionModuleProduct extends Model {

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_stock_status_id'])) {
			$sql .= " AND p.stock_status_id = '" . (int)$data['filter_stock_status_id'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_stock_status_id'])) {
			$sql .= " AND p.stock_status_id = '" . (int)$data['filter_stock_status_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTopViewProducts($data = array()) {
		$sql = "SELECT p.image,p.product_id,pd.name,p.price,p.status,p.viewed FROM oc_product p LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)";

		if (!empty($data['category_id'])) {
			$sql .= " LEFT JOIN oc_product_to_category pc ON (p.product_id =pc.product_id ) WHERE pc.category_id = '" . (int)$data['category_id'] . "' AND pd.language_id = 1 ORDER BY p.viewed DESC ";
		}else {
			$sql .=" WHERE pd.language_id = 1 ORDER BY p.viewed DESC";
		}


		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}else {
			$sql .= " LIMIT 5 ";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getLowestViewProducts($data = array()) {
		$sql = "SELECT p.image,p.product_id,pd.name,p.price,p.status,p.viewed FROM oc_product p LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)";

		if (!empty($data['category_id'])) {
			$sql .= " LEFT JOIN oc_product_to_category pc ON (p.product_id =pc.product_id ) WHERE pc.category_id = '" . (int)$data['category_id'] . "' AND pd.language_id = 1 ORDER BY p.viewed ASC ";
		}else {
			$sql .=" WHERE pd.language_id = 1 ORDER BY p.viewed ASC";
		}


		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}else {
			$sql .= " LIMIT 5 ";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getHighestSellingProducts($data = array()) {
		$sql = "SELECT p.image,p.product_id,pd.name,p.price,p.status,COUNT(*) AS orders FROM oc_order_product op LEFT JOIN oc_product p ON(op.product_id = p.product_id ) LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)";

		if (!empty($data['category_id'])) {
			$sql .= "WHERE p.product_id IN (SELECT product_id FROM oc_product_to_category WHERE category_id = '" . (int)$data['category_id'] . "')";
		}

		$sql .= "GROUP BY op.name ORDER BY  orders DESC";

		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}else {
			$sql .= " LIMIT 5 ";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getLowestSellingProducts($data = array()) {
		$sql = "SELECT p.image,p.product_id,pd.name,p.price,p.status,COUNT(*) AS orders FROM oc_order_product op LEFT JOIN oc_product p ON(op.product_id = p.product_id ) LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)";

		if (!empty($data['category_id'])) {
			$sql .= "WHERE p.product_id IN (SELECT product_id FROM oc_product_to_category WHERE category_id = '" . (int)$data['category_id'] . "')";
		}

		$sql .= "GROUP BY op.name ORDER BY  orders ASC";

		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}else {
			$sql .= " LIMIT 5 ";
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}
}
