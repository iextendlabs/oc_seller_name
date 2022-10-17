<?php
class ControllerNewsNews extends Controller {
    public function index() {

        $this->load->language('news/news');
        $this->load->model('catalog/news');


		if (isset($this->request->get['news_id'])) {
			$news_id = (int)$this->request->get['news_id'];
		} else {
			$news_id = 0;
		}

        $news_info = $this->model_catalog_news->getNews($news_id);
        
        if($news_info){
            $data['title'] = $news_info['title'];
			$data['description'] = html_entity_decode($news_info['description']);

            $data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
            $data['allnews'] = $this->url->link('information/news');
            
            $this->response->setOutput($this->load->view('news/news', $data));
        }

            
    }
}