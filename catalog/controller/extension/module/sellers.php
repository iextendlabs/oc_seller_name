<?php
class ControllerExtensionModulesellers extends Controller {
	private $error = array();
	public function info() {
		$this->load->language('extension/module/sellers');

		$this->load->model('extension/module/sellers');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['seller_id'])) {
			$seller_id = $this->request->get['seller_id'];
		} else {
			$seller_id = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
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

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_seller_list'),
			'href' => $this->url->link('extension/module/sellers/list')
		);

		$seller_info = $this->model_extension_module_sellers->getSeller($seller_id);

		if ($seller_info) {
			$this->document->setTitle($seller_info['seller_name']);

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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$data['text_seller_list'] = $seller_info['seller_name'];
			
			if ($seller_info['logo']) {
				$data['logo'] = $this->model_tool_image->resize($seller_info['logo'],130,100 );
			} else {
				$data['logo'] = $this->model_tool_image->resize('no_image.png', 130,100);
			}

			if ($seller_info['banner_image']) {
				$data['banner_image'] = $this->model_tool_image->resize($seller_info['banner_image'], 1140,150);
			} else {
				$data['banner_image'] = $this->model_tool_image->resize('no_image.png', 1140,150);
			}

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			$data['compare'] = $this->url->link('product/compare');

			$data['products'] = array();

			$filter_data = array(
				'filter_seller_id'       => $seller_info['seller_id'],
				'filter_seller'       => $seller_info['seller_name'],
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $limit,
				'limit'                  => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'seller=' . $result['seller_name'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('extension/module/sellers', 'seller_id=' . $this->request->get['seller_id'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/module/sellers', $data));
		} else {
			$url = '';

			if (isset($this->request->get['seller_id'])) {
				$url .= '&seller_id=' . $this->request->get['seller_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function list() {
		$this->load->language('extension/module/sellers');

		$this->load->model('extension/module/sellers');

		$this->document->setTitle($this->language->get('text_seller_list'));
		
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_seller_list'),
			'href' => $this->url->link('extension/module/sellers/list')
		);
		$data['sellers'] = array();
		
		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$this->load->model('tool/image');

		$seller_total = $this->model_extension_module_sellers->getTotalSellers();

		$results = $this->model_extension_module_sellers->getSellers($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['logo'])) {
				$image = $this->model_tool_image->resize($result['logo'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['sellers'][]=array(
				'image'      	 => $image,
				'name'   => $result['seller_name'],
				'id'     => $result['seller_id'],
				'href' =>$this->url->link('extension/module/sellers/info', '&seller_id=' . $result['seller_id'] . $url)
			);
		}
		$data['form_href'] =$this->url->link('extension/module/sellers/form', '',true);

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$url = '';
		
		$pagination = new Pagination();
		$pagination->total = $seller_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/sellers/list', '', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($seller_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seller_total - $this->config->get('config_limit_admin'))) ? $seller_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seller_total, ceil($seller_total / $this->config->get('config_limit_admin')));

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/sellers_list', $data));
	}

	public function form() {

		$this->load->language('extension/module/sellers');

		$this->document->setTitle($this->language->get('text_seller_form'));

		$this->load->model('extension/module/sellers');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if($_FILES['logo']['name'] != null){
				
				$logo_file =$_FILES['logo'];
				$logo_name =$_FILES['logo']['name'];
				$logo_tmp_name =$_FILES['logo']['tmp_name'];
					
				$logo_tempExtension = explode('.',$logo_name);
				$logo_fileExtension = strtolower(end($logo_tempExtension));
				$logo_fileName = $logo_tempExtension[0];

				$isAllowed = array('jpg','png','ico');

				if(in_array($logo_fileExtension,$isAllowed)){
					$newLogoFileName = $logo_fileName . "." . $logo_fileExtension;
					$path = DIR_IMAGE . "logo/";
					if (!file_exists($path)){
						mkdir($path,0777,true);
					}
					$logoFileDestination = $path . $newLogoFileName;
					$this->request->post['logo'] = "logo/".$newLogoFileName;
					move_uploaded_file($logo_tmp_name,$logoFileDestination);
				}else{
					$this->response->redirect($this->url->link('extension/module/sellers/form', 'error_logo=' . $this->language->get('error_logo'), true));
				}
			}else{
				$this->request->post['logo'] = '';
			}

			if($_FILES['banner']['name'] != null){

				$banner_file =$_FILES['banner'];
				$banner_name =$_FILES['banner']['name'];
				$banner_tmp_name =$_FILES['banner']['tmp_name'];
					
				$banner_tempExtension = explode('.',$banner_name);
				$banner_fileExtension = strtolower(end($banner_tempExtension));
				$banner_fileName = $banner_tempExtension[0];

				$isAllowed = array('jpg','png','ico');

				if(in_array($banner_fileExtension,$isAllowed)){
					$newBannerFileName = $banner_fileName . "." . $banner_fileExtension;
					$path = DIR_IMAGE . "banner/";
					if (!file_exists($path)){
						mkdir($path,0777,true);
					}
					$BannerFileDestination = $path . $newBannerFileName;
					$this->request->post['banner_image'] = "banner/".$newBannerFileName;
					move_uploaded_file($banner_tmp_name,$BannerFileDestination);
				}else{
					$this->response->redirect($this->url->link('extension/module/sellers/form', 'error_banner=' . $this->language->get('error_banner'), true));
				}
			}else{
				$this->request->post['banner_image'] = '';
			}

			$this->model_extension_module_sellers->addSeller($this->request->post);

			$this->response->redirect($this->url->link('extension/module/sellers/list', '', true));
		}

		$data['text_form'] = $this->language->get('text_add');
		
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

		if (isset($this->request->get['error_logo'])) {
			$data['error_logo'] = $this->request->get['error_logo'];
		} else {
			$data['error_logo'] = array();
		}

		if (isset($this->request->get['error_banner'])) {
			$data['error_banner'] = $this->request->get['error_banner'];
		} else {
			$data['error_banner'] = array();
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_seller_list'),
			'href' => $this->url->link('extension/module/sellers/list')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_seller_form'),
			'href' => $this->url->link('extension/module/sellers/form')
		);

		$data['action'] = $this->url->link('extension/module/sellers/form', '', true);
		$data['image_action'] = $this->url->link('extension/module/sellers/move_file', '', true);
		
		$data['cancel'] = $this->url->link('extension/module/sellers/list', '', true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/seller_form', $data));
	}

	protected function validateForm() {
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

}