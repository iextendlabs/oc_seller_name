<?php
class ControllerInformationNews extends Controller {
	public function index() {
		$this->load->language('information/news');

		$this->load->model('catalog/news');
		$allNews = $this->model_catalog_news->getNewss();

		$data['allNews'] = array();

		foreach($allNews as $singleNews){
			$data['allNews'][] = array(
				'title' => $singleNews['title'],
				'href'  => $this->url->link('news/news', 'news_id=' . $singleNews['news_id']),
				'date_time' =>$singleNews['date_time']
			);
		}

		$latestNews = $this->model_catalog_news->getLatestNews();

		$data['latestNews'] = array();

		foreach($latestNews as $news){
			$data['latestNews'][] = array(
				'title' => $news['title'],
				'href'  => $this->url->link('news/news', 'news_id=' . $news['news_id']),
				'date_time' =>$news['date_time']
			);
		}

		

		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('information/news', $data));
	}

	public function allNews() {
		$this->load->language('information/news');

		$this->load->model('catalog/news');
		$allNews = $this->model_catalog_news->getAllNews();

		$data['allNews'] = array();

		foreach($allNews as $singleNews){
			$data['allNews'][] = array(
				'title' => $singleNews['title'],
				'href'  => $this->url->link('news/news', 'news_id=' . $singleNews['news_id']),
				'date_time' =>$singleNews['date_time']
			);
		}
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('information/allnews', $data));
	}
}
