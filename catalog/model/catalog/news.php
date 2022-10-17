<?php
class ModelCatalogNews extends Model {
    public function getNews($news_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news WHERE news_id = '" . (int)$news_id . "'");

		return $query->row;
	}
	
	public function getAllNews() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news order by rand()");

		return $query->rows;
	}

	public function getNewss() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news order by rand() LIMIT 7");

		return $query->rows;
	}

	public function getLatestNews() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news ORDER BY date_time DESC LIMIT 8");

		return $query->rows;
	}
}

?>