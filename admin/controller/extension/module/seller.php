<?php
class ControllerExtensionModuleSeller extends Controller {
	private $error = array();
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `oc_sellers` (
			`seller_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			`seller_name` varchar(99) NOT NULL,
			`seller_description` varchar(999) NOT NULL,
			`logo` varchar(999) NOT NULL,
			`banner_image` varchar(999) NOT NULL,
			`email` varchar(99) NOT NULL
			)");
		$this->db->query("CREATE TABLE IF NOT EXISTS `oc_product_to_seller` (
			`seller_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL
			)");
	}
	public function uninstall(){
		$this->db->query("DELETE FROM `oc_sellers` ");
		$this->db->query("DELETE FROM `oc_product_to_seller` ");
	}
	public function index() {
		$this->install();
		$this->load->language('extension/module/seller');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('module_seller', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('extension/module/seller', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		if (isset($this->request->post['module_seller_status'])) {
			$data['module_seller_status'] = $this->request->post['module_seller_status'];
		} else {
			$data['module_seller_status'] = $this->config->get('module_seller_status');
		}
		if(isset($this->request->post['module_seller_status']) == 0){
			$this->uninstall();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seller', $data));

	}

	public function add() {
		$this->load->language('extension/module/seller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/seller');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_seller->addSeller($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/seller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/seller');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_seller->editSeller($this->request->get['seller_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/seller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/seller');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $seller_id) {
				$this->model_extension_module_seller->deleteSeller($seller_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function getList() {
		$this->load->language('extension/module/seller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/seller');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/module/seller/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/seller/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['sellers'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$this->load->model('tool/image');

		$seller_total = $this->model_extension_module_seller->getTotalSeller();
		
		$results = $this->model_extension_module_seller->getSellers($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['logo'])) {
				$image = $this->model_tool_image->resize($result['logo'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['sellers'][] = array(
				'seller_id'		 => $result['seller_id'],
				'image'      	 => $image,
				'seller_name'    => $result['seller_name'],
				'edit'           => $this->url->link('extension/module/seller/edit', 'user_token=' . $this->session->data['user_token'] . '&seller_id=' . $result['seller_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . '&sort=title' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $seller_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($seller_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seller_total - $this->config->get('config_limit_admin'))) ? $seller_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seller_total, ceil($seller_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seller_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['seller_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['seller_name'])) {
			$data['error_seller_name'] = $this->error['seller_name'];
		} else {
			$data['error_seller_name'] = array();
		}

		if (isset($this->error['seller_description'])) {
			$data['error_seller_description'] = $this->error['seller_description'];
		} else {
			$data['error_seller_description'] = array();
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['store_name'])) {
			$data['error_store_name'] = $this->error['store_name'];
		} else {
			$data['error_store_name'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['seller_id'])) {
			$data['action'] = $this->url->link('extension/module/seller/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/seller/edit', 'user_token=' . $this->session->data['user_token'] . '&seller_id=' . $this->request->get['seller_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/module/seller/getlist', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['seller_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$seller_info = $this->model_extension_module_seller->getSeller($this->request->get['seller_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();


		if (isset($this->request->post['seller_name'])) {
			$data['seller_name'] = $this->request->post['seller_name'];
		} elseif (!empty($seller_info)) {
			$data['seller_name'] = $seller_info['seller_name'];
		} else {
			$data['seller_name'] = '';
		}

		if (isset($this->request->post['seller_description'])) {
			$data['seller_description'] = $this->request->post['seller_description'];
		} elseif (!empty($seller_info)) {
			$data['seller_description'] = $seller_info['seller_description'];
		} else {
			$data['seller_description'] = '';
		}

		if (isset($this->request->post['logo'])) {
			$data['logo'] = $this->request->post['logo'];
		} elseif (!empty($seller_info)) {
			$data['logo'] = $seller_info['logo'];
		} else {
			$data['logo'] = '';
		}

		if (isset($this->request->post['banner_image'])) {
			$data['banner_image'] = $this->request->post['banner_image'];
		} elseif (!empty($seller_info)) {
			$data['banner_image'] = $seller_info['banner_image'];
		} else {
			$data['banner_image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['logo']) && is_file(DIR_IMAGE . $this->request->post['logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['logo'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($seller_info['logo'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['banner_image']) && is_file(DIR_IMAGE . $this->request->post['banner_image'])) {
			$data['thumb_banner'] = $this->model_tool_image->resize($this->request->post['banner_image'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['banner_image'])) {
			$data['thumb_banner'] = $this->model_tool_image->resize($seller_info['banner_image'], 100, 100);
		} else {
			$data['thumb_banner'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($seller_info)) {
			$data['email'] = $seller_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($seller_info)) {
			$data['telephone'] = $seller_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} elseif (!empty($seller_info)) {
			$data['store_name'] = $seller_info['store_name'];
		} else {
			$data['store_name'] = '';
		}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} elseif (!empty($seller_info)) {
			$data['address'] = $seller_info['address'];
		} else {
			$data['address'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/seller_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/seller')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['seller_name']) < 1) || (utf8_strlen($this->request->post['seller_name']) > 64)) {
			$this->error['seller_name'] = $this->language->get('error_seller_name');
		}

		if (utf8_strlen($this->request->post['seller_description']) < 3) {
			$this->error['seller_description'] = $this->language->get('error_seller_description');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['store_name']) < 3) || (utf8_strlen($this->request->post['store_name']) > 256)) {
			$this->error['store_name'] = $this->language->get('error_store_name');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_seller'])) {
			$this->load->model('extension/module/seller');

			$filter_data = array(
				'filter_seller' => $this->request->get['filter_seller'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_module_seller->getSellers($filter_data);
			foreach ($results as $result) {
				$json[] = array(
					'seller_id' 			=> $result['seller_id'],
					'seller_name'           => strip_tags(html_entity_decode($result['seller_name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['seller_name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seller')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
