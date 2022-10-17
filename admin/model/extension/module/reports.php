<?php
class ModelExtensionModuleReports extends Model {

    public function TotalProduct(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product");

		return $query->row['total'];
    }

    public function TopViewProduct(){
        $query = $this->db->query("SELECT pd.name AS name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.viewed>0 ORDER BY p.viewed DESC LIMIT 1");

		if (!empty($query->row['name'])) {
            return $query->row['name'];
        }else{
            return "There is no viewed product!!!";
        }
    } 

    public function LowestViewProduct(){
        $query = $this->db->query("SELECT pd.name AS name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.viewed>0 ORDER BY p.viewed ASC LIMIT 1");

		if (!empty($query->row['name'])) {
            return $query->row['name'];
        }else{
            return "There is no viewed product!!!";
        }
    }

    public function HighestSellingProduct(){
        $query = $this->db->query("SELECT op.name AS name FROM " . DB_PREFIX . "order_product op GROUP BY op.name HAVING COUNT(*) >=1 ORDER BY  COUNT(*) DESC LIMIT 1");

		if (!empty($query->row['name'])) {
            return $query->row['name'];
        }else{
            return "There is no sells!!!";
        }
    }

    public function LowestSellingProduct(){
        $query = $this->db->query("SELECT op.name AS name FROM " . DB_PREFIX . "order_product op GROUP BY op.name HAVING COUNT(*) >=1 ORDER BY  COUNT(*) ASC LIMIT 1");

		if (!empty($query->row['name'])) {
            return $query->row['name'];
        }else{
            return "There is no sells!!!";
        }
    }

    public function TotalOutOfStockProduct(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) WHERE p.stock_status_id = 5");

		return $query->row['total'];
    }

    public function TotalCategory(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category");

		return $query->row['total'];
    }

    public function TotalEnableCategory(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c WHERE c.status = 1 ");

		return $query->row['total'];
    }

    public function TotalDisableCategory(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c WHERE c.status = 0 ");

		return $query->row['total'];
    }

    public function TotalManufacturer(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer");

		return $query->row['total'];
    }

    public function TopBrand(){
        $query = $this->db->query("SELECT m.name AS name FROM " . DB_PREFIX . "order_product op LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) GROUP BY m.name HAVING COUNT(*) >=1 ORDER BY COUNT(*) DESC LIMIT 1");
        if (!empty($query->row['name'])) {
            return $query->row['name'];
        }else{
            return "There is no top brand!!!";
        }
    }

    public function TotalCustomer(){
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer");

		return $query->row['total'];
    }

    public function TotalSales($date_start,$date_end){
        $sql = "SELECT SUM(total) AS total FROM " . DB_PREFIX . "order";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " WHERE date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function TotalTax($date_start,$date_end){
        $sql = "SELECT SUM(op.tax) AS tax FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id)";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " WHERE o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['tax'];
    }

    public function TotalReturn($date_start,$date_end){
        $sql = "SELECT SUM(o.total) AS total_return FROM " . DB_PREFIX . "order o WHERE o.order_status_id = 11";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total_return'];
    }

    public function TotalOrder($date_start,$date_end){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " WHERE date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function TotalCompleteOrder($date_start,$date_end){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE os.order_status_id = 5";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function TotalCanceledOrder($date_start,$date_end){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE os.order_status_id = 7";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function TotalPendingOrder($date_start,$date_end){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE os.order_status_id = 1";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function TotalRefundedOrder($date_start,$date_end){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE os.order_status_id = 11";
        
        if (!empty($date_start && $date_end)) {
			$sql .= " AND o.date_added BETWEEN  '" . $this->db->escape($date_start) . "' AND '" . $this->db->escape($date_end) . "' ";
		}

        $query = $this->db->query($sql);
		return $query->row['total'];
    }

    public function getCategories($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "category_description ";
			
			if (!empty($data['filter_category'])) {
				$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_category']) . "%'";
			}

			$sort_data = array(
				'ame',
				'sort_order'

			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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

}