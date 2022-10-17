<?php
class ModelCatalogNews extends Model {
	public function addNews($data) {
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "news SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'");
		
	}

	public function editNews($news_id, $data) {
		

		$this->db->query("UPDATE " . DB_PREFIX . "news SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "' WHERE news_id = '" . (int)$news_id . "'");
		
		$this->cache->delete('news');
	}

	public function deleteNews($news_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "news` WHERE news_id = '" . (int)$news_id . "'");
		
		$this->cache->delete('news');
	}

	public function getNews($news_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "news WHERE news_id = '" . (int)$news_id . "'");

		return $query->row;
	}

	public function getNewss($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "news ";

			$sort_data = array(
				'title',
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
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

	public function getTotalNews() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "news");

		return $query->row['total'];
	}
}