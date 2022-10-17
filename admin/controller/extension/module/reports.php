<?php
class ControllerExtensionModuleReports extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/report');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('module_reports', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('extension/module/reports', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		if (isset($this->request->post['module_reports_status'])) {
			$data['module_reports_status'] = $this->request->post['module_reports_status'];
		} else {
			$data['module_reports_status'] = $this->config->get('module_reports_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/report', $data));

	}

	public function info() {
		$this->load->language('extension/module/reports');

		$this->document->setTitle($this->language->get('text_dashboard'));

		$this->load->model('extension/module/reports');
		
		$url = '';
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_dashboard'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (isset($this->request->get['custom_date_start'])) {
			$custom_date_start = $this->request->get['custom_date_start'];
		} else {
			$custom_date_start = '';
		}

		if (isset($this->request->get['custom_date_end'])) {
			$custom_date_end = $this->request->get['custom_date_end'];
		} else {
			$custom_date_end = '';
		}

		if (isset($this->request->get['day_start'])) {
			$day_start = $this->request->get['day_start'];
		} else {
			$day_start = '';
		}

		if (isset($this->request->get['day_end'])) {
			$day_end = $this->request->get['day_end'];
		} else {
			$day_end = '';
		}

		if (isset($this->request->get['month_start'])) {
			$month_start = $this->request->get['month_start'];
		} else {
			$month_start = '';
		}

		if (isset($this->request->get['month_end'])) {
			$month_end = $this->request->get['month_end'];
		} else {
			$month_end = '';
		}

		if (isset($this->request->get['year_start'])) {
			$year_start = $this->request->get['year_start'];
		} else {
			$year_start = '';
		}

		if (isset($this->request->get['year_end'])) {
			$year_end = $this->request->get['year_end'];
		} else {
			$year_end = '';
		}

		$url = '';

		if (isset($this->request->get['custom_date_start'])) {
			$url .= '&custom_date_start=' . $this->request->get['custom_date_start'];
		}

		if (isset($this->request->get['custom_date_end'])) {
			$url .= '&custom_date_end=' . $this->request->get['custom_date_end'];
		}
		
		if (isset($this->request->get['day_start'])) {
			$url .= '&day_start=' . $this->request->get['day_start'];
		}

		if (isset($this->request->get['day_end'])) {
			$url .= '&day_end=' . $this->request->get['day_end'];
		}

		if (isset($this->request->get['month_start'])) {
			$url .= '&month_start=' . $this->request->get['month_start'];
		}

		if (isset($this->request->get['month_end'])) {
			$url .= '&month_end=' . $this->request->get['month_end'];
		}

		if (isset($this->request->get['year_start'])) {
			$url .= '&year_start=' . $this->request->get['year_start'];
		}

		if (isset($this->request->get['year_end'])) {
			$url .= '&year_end=' . $this->request->get['year_end'];
		}

		if (!empty($custom_date_start && $custom_date_end)) {
			$date_start = $custom_date_start;
			$date_end = $custom_date_end;
		}elseif (!empty($day_start && $day_end)) {
			$date_start = $day_start;
			$date_end = $day_end;
			$data['text_sales_report']= 'Daily Sales Report'; 
		}elseif (!empty($month_start && $month_end)) {
			$date_start = $month_start;
			$date_end = $month_end; 
			$data['text_sales_report']= 'Monthly Sales Report'; 
		}elseif (!empty($year_start && $year_end)) {
			$date_start = $year_start;
			$date_end = $year_end;
			$data['text_sales_report']= 'Yearly Sales Report'; 
		}else {
            $date_start ='';
			$date_end ='';
		}

		$sales = $this->model_extension_module_reports->TotalSales($date_start , $date_end);
		$tax = $this->model_extension_module_reports->TotalTax($date_start , $date_end);
		$return = $this->model_extension_module_reports->TotalReturn($date_start , $date_end);

		
		if ($sales == 0) {
			$data['sales'] = 0.000;
		}elseif ($sales > 1000000000000) {
			$data['sales'] = $this->currency->format(round($sales / 1000000000000, 1), $this->config->get('config_currency')). 'T';
		} elseif ($sales > 1000000000) {
			$data['sales'] =$this->currency->format(round($sales / 1000000000, 1), $this->config->get('config_currency')). 'B';
		} elseif ($sales > 1000000) {
			$data['sales'] = $this->currency->format(round($sales / 1000000, 1), $this->config->get('config_currency')). 'M';
		} elseif ($sales > 1000) {
			$data['sales'] = $this->currency->format(round($sales / 1000, 1), $this->config->get('config_currency')). 'K';
		} elseif ($sales > 100) {
			$data['sales'] = $this->currency->format(round($sales * 1, 1), $this->config->get('config_currency'));
		} elseif ($sales > 10) {
			$data['sales'] = $this->currency->format(round($sales * 1, 1), $this->config->get('config_currency'));
		} else {
			$data['sales'] = $this->currency->format(round($sales * 1, 1), $this->config->get('config_currency'));
		}


		if ($return == 0) {
			$data['return'] = 0.000;
		}elseif ($return > 1000000000000) {
			$data['return'] = $this->currency->format(round($return / 1000000000000, 1), $this->config->get('config_currency')). 'T';
		} elseif ($return > 1000000000) {
			$data['return'] =$this->currency->format(round($return / 1000000000, 1), $this->config->get('config_currency')). 'B';
		} elseif ($return > 1000000) {
			$data['return'] = $this->currency->format(round($return / 1000000, 1), $this->config->get('config_currency')). 'M';
		} elseif ($return > 1000) {
			$data['return'] = $this->currency->format(round($return / 1000, 1), $this->config->get('config_currency')). 'K';
		} elseif ($return > 100) {
			$data['return'] = $this->currency->format(round($return * 1, 1), $this->config->get('config_currency'));
		} elseif ($return > 10) {
			$data['return'] = $this->currency->format(round($return * 1, 1), $this->config->get('config_currency'));
		} else {
			$data['return'] = $this->currency->format(round($return* 1, 1), $this->config->get('config_currency'));
		}


		if ($tax == 0) {
			$data['tax'] = 0.000;
		}elseif ($tax > 1000000000000) {
			$data['tax'] = $this->currency->format(round($tax / 1000000000000, 1), $this->config->get('config_currency')). 'T';
		} elseif ($tax > 1000000000) {
			$data['tax'] =$this->currency->format(round($tax / 1000000000, 1), $this->config->get('config_currency')). 'B';
		} elseif ($tax > 1000000) {
			$data['tax'] = $this->currency->format(round($tax / 1000000, 1), $this->config->get('config_currency')). 'M';
		} elseif ($tax > 1000) {
			$data['tax'] = $this->currency->format(round($tax / 1000, 1), $this->config->get('config_currency')). 'K';
		} elseif ($tax > 100) {
			$data['tax'] = $this->currency->format(round($tax * 1, 1), $this->config->get('config_currency'));
		} elseif ($tax > 10) {
			$data['tax'] = $this->currency->format(round($tax * 1, 1), $this->config->get('config_currency'));
		} else {
			$data['tax'] = $this->currency->format(round($tax * 1, 1), $this->config->get('config_currency'));
		}


		$data['user_token'] = $this->session->data['user_token'];
		$data['total_order'] = $this->model_extension_module_reports->TotalOrder($date_start , $date_end);
		$data['total_complete_order'] = $this->model_extension_module_reports->TotalCompleteOrder($date_start , $date_end);
		$data['total_canceled_order'] = $this->model_extension_module_reports->TotalCanceledOrder($date_start , $date_end);
		$data['total_panding_order'] = $this->model_extension_module_reports->TotalPendingOrder($date_start , $date_end);
		$data['total_refunded_order'] = $this->model_extension_module_reports->TotalRefundedOrder($date_start , $date_end);
		$data['total_product'] = $this->model_extension_module_reports->TotalProduct();
		$data['total_OutOfStock_product'] = $this->model_extension_module_reports->TotalOutOfStockProduct();
		$data['total_HighestSelling_product'] = $this->model_extension_module_reports->HighestSellingProduct();
		$data['total_LowestSelling_product'] = $this->model_extension_module_reports->LowestSellingProduct();
		$data['total_TopView_product'] = $this->model_extension_module_reports->TopViewProduct();
		$data['total_LowestView_product'] = $this->model_extension_module_reports->LowestViewProduct();
		$data['total_category'] = $this->model_extension_module_reports->TotalCategory();
		$data['total_enable_category'] = $this->model_extension_module_reports->TotalEnableCategory();
		$data['total_disable_category'] = $this->model_extension_module_reports->TotalDisableCategory();
		$data['total_manufacturer'] = $this->model_extension_module_reports->TotalManufacturer();
		$data['top_brand'] = $this->model_extension_module_reports->TopBrand();
		$data['total_customer'] = $this->model_extension_module_reports->TotalCustomer();
		$data['day_start'] = date("Y-m-d 00:00:00");
		$data['day_end'] = date("Y-m-d 23:00:00");
		$data['month_start'] = date("Y-m-01 00:00:00");
		$data['month_end'] = date("Y-m-31 23:00:00");
		$data['year_start'] = date("Y-01-01 00:00:00");
		$data['year_end'] = date("Y-12-31 23:00:00");

		$data['report_setting'] = $this->url->link('extension/module/reports/report_setting', 'user_token='. $this->session->data['user_token'], true);
		$data['href_order'] =  $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['href_panding'] =  $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status_id='.'1' . $url, true);
		$data['href_refunded'] =  $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status_id=11' . $url, true);
		$data['href_complete'] =  $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status_id=5' . $url, true);
		$data['href_canceled'] =  $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&filter_order_status_id=7' . $url, true);
		$data['href_product'] =  $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_outofstock_product'] =  $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&filter_stock_status_id=5', true);
		$data['href_topView_product'] =  $this->url->link('extension/module/reports/topViewProduct', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_lowestview_product'] =  $this->url->link('extension/module/reports/lowestViewProduct', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_highestselling_product'] =  $this->url->link('extension/module/reports/highestSellingProduct', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_lowestselling_product'] =  $this->url->link('extension/module/reports/lowestSellingProduct', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_customer'] =  $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] , true);
		$data['href_category'] =  $this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'] , true);

		$data['isFirstLoad'] = $this->isFirstLoad();
	
		$data['order_status'] = $this->config->get('module_order_status');
		$data['sales_status'] = $this->config->get('module_sales_status');
		$data['return_status'] = $this->config->get('module_return_status');
		$data['tax_status'] = $this->config->get('module_tax_status');
		$data['complete_order_status'] = $this->config->get('module_complete_order_status');
		$data['pending_order_status'] = $this->config->get('module_pending_order_status');
		$data['canceled_order_status'] = $this->config->get('module_canceled_order_status');
		$data['refunded_order_status'] = $this->config->get('module_refunded_order_status');
		$data['product_status'] = $this->config->get('module_product_status');
		$data['outofstock_product_status'] = $this->config->get('module_outofstock_product_status');
		$data['lowestselling_product_status'] = $this->config->get('module_lowestselling_product_status');
		$data['highestselling_product_status'] = $this->config->get('module_highestselling_product_status');
		$data['topview_product_status'] = $this->config->get('module_topview_product_status');
		$data['lowestview_product_status'] = $this->config->get('module_lowestview_product_status');
		$data['customers_status'] = $this->config->get('module_customers_status');
		$data['category_status'] = $this->config->get('module_report_category_status');
		$data['enable_category_status'] = $this->config->get('module_enable_category_status');
		$data['disable_category_status'] = $this->config->get('module_disable_category_status');
		$data['nanufacturer_status'] = $this->config->get('module_nanufacturer_status');
		$data['topbrand_status'] = $this->config->get('module_topbrand_status');
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports', $data));
	}

	public function report_setting() {
		$this->load->language('extension/module/report_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('module_order', $this->request->post);
			$this->model_setting_setting->editSetting('module_sales', $this->request->post);
			$this->model_setting_setting->editSetting('module_return', $this->request->post);
			$this->model_setting_setting->editSetting('module_tax', $this->request->post);
			$this->model_setting_setting->editSetting('module_complete_order', $this->request->post);
			$this->model_setting_setting->editSetting('module_pending_order', $this->request->post);
			$this->model_setting_setting->editSetting('module_canceled_order', $this->request->post);
			$this->model_setting_setting->editSetting('module_refunded_order', $this->request->post);
			$this->model_setting_setting->editSetting('module_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_outofstock_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_lowestselling_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_highestselling_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_topview_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_lowestview_product', $this->request->post);
			$this->model_setting_setting->editSetting('module_customers', $this->request->post);
			$this->model_setting_setting->editSetting('module_report_category', $this->request->post);
			$this->model_setting_setting->editSetting('module_enable_category', $this->request->post);
			$this->model_setting_setting->editSetting('module_disable_category', $this->request->post);
			$this->model_setting_setting->editSetting('module_nanufacturer', $this->request->post);
			$this->model_setting_setting->editSetting('module_topbrand', $this->request->post);

			$this->response->redirect($this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['action'] = $this->url->link('extension/module/reports/report_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		if (isset($this->request->post['module_order_status'])) {
			$data['module_order_status'] = $this->request->post['module_order_status'];
		} else {
			$data['module_order_status'] = $this->config->get('module_order_status');
		}

		if (isset($this->request->post['module_sales_status'])) {
			$data['module_sales_status'] = $this->request->post['module_sales_status'];
		} else {
			$data['module_sales_status'] = $this->config->get('module_sales_status');
		}

		if (isset($this->request->post['module_return_status'])) {
			$data['module_return_status'] = $this->request->post['module_return_status'];
		} else {
			$data['module_return_status'] = $this->config->get('module_return_status');
		}

		if (isset($this->request->post['module_tax_status'])) {
			$data['module_tax_status'] = $this->request->post['module_tax_status'];
		} else {
			$data['module_tax_status'] = $this->config->get('module_tax_status');
		}

		if (isset($this->request->post['module_complete_order_status'])) {
			$data['module_complete_order_status'] = $this->request->post['module_complete_order_status'];
		} else {
			$data['module_complete_order_status'] = $this->config->get('module_complete_order_status');
		}

		if (isset($this->request->post['module_pending_order_status'])) {
			$data['module_pending_order_status'] = $this->request->post['module_pending_order_status'];
		} else {
			$data['module_pending_order_status'] = $this->config->get('module_pending_order_status');
		}

		if (isset($this->request->post['module_canceled_order_status'])) {
			$data['module_canceled_order_status'] = $this->request->post['module_canceled_order_status'];
		} else {
			$data['module_canceled_order_status'] = $this->config->get('module_canceled_order_status');
		}

		if (isset($this->request->post['module_refunded_order_status'])) {
			$data['module_refunded_order_status'] = $this->request->post['module_refunded_order_status'];
		} else {
			$data['module_refunded_order_status'] = $this->config->get('module_refunded_order_status');
		}

		if (isset($this->request->post['module_product_status'])) {
			$data['module_product_status'] = $this->request->post['module_product_status'];
		} else {
			$data['module_product_status'] = $this->config->get('module_product_status');
		}

		if (isset($this->request->post['module_outofstock_product_status'])) {
			$data['module_outofstock_product_status'] = $this->request->post['module_outofstock_product_status'];
		} else {
			$data['module_outofstock_product_status'] = $this->config->get('module_outofstock_product_status');
		}

		if (isset($this->request->post['module_lowestselling_product_status'])) {
			$data['module_lowestselling_product_status'] = $this->request->post['module_lowestselling_product_status'];
		} else {
			$data['module_lowestselling_product_status'] = $this->config->get('module_lowestselling_product_status');
		}

		if (isset($this->request->post['module_highestselling_product_status'])) {
			$data['module_highestselling_product_status'] = $this->request->post['module_highestselling_product_status'];
		} else {
			$data['module_highestselling_product_status'] = $this->config->get('module_highestselling_product_status');
		}

		if (isset($this->request->post['module_topview_product_status'])) {
			$data['module_topview_product_status'] = $this->request->post['module_topview_product_status'];
		} else {
			$data['module_topview_product_status'] = $this->config->get('module_topview_product_status');
		}

		if (isset($this->request->post['module_lowestview_product_status'])) {
			$data['module_lowestview_product_status'] = $this->request->post['module_lowestview_product_status'];
		} else {
			$data['module_lowestview_product_status'] = $this->config->get('module_lowestview_product_status');
		}

		if (isset($this->request->post['module_customers_status'])) {
			$data['module_customers_status'] = $this->request->post['module_customers_status'];
		} else {
			$data['module_customers_status'] = $this->config->get('module_customers_status');
		}

		if (isset($this->request->post['module_report_category_status'])) {
			$data['module_report_category_status'] = $this->request->post['module_report_category_status'];
		} else {
			$data['module_report_category_status'] = $this->config->get('module_report_category_status');
		}

		if (isset($this->request->post['module_enable_category_status'])) {
			$data['module_enable_category_status'] = $this->request->post['module_enable_category_status'];
		} else {
			$data['module_enable_category_status'] = $this->config->get('module_enable_category_status');
		}

		if (isset($this->request->post['module_disable_category_status'])) {
			$data['module_disable_category_status'] = $this->request->post['module_disable_category_status'];
		} else {
			$data['module_disable_category_status'] = $this->config->get('module_disable_category_status');
		}

		if (isset($this->request->post['module_nanufacturer_status'])) {
			$data['module_nanufacturer_status'] = $this->request->post['module_nanufacturer_status'];
		} else {
			$data['module_nanufacturer_status'] = $this->config->get('module_nanufacturer_status');
		}

		if (isset($this->request->post['module_topbrand_status'])) {
			$data['module_topbrand_status'] = $this->request->post['module_topbrand_status'];
		} else {
			$data['module_topbrand_status'] = $this->config->get('module_topbrand_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/report_setting', $data));
	}

	public function product() {
		$this->load->language('extension/module/product');

		$this->load->model('extension/module/product');

		if (isset($this->request->get['filter_stock_status_id'])) {
			$filter_stock_status_id = $this->request->get['filter_stock_status_id'];
		} else {
			$filter_stock_status_id = '';
		}
		if ($filter_stock_status_id == 5) {
			$data['heading_title'] = 'Out of Stock Products';
			$data['text_list'] = 'Out of Stock Product list';
			$this->document->setTitle("Out of Stock Products");
		}else {
			$data['heading_title'] = 'Products ';
			$data['text_list'] = 'Product list';
			$this->document->setTitle("Products");
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_stock_status_id'])) {
			$url .= '&filter_stock_status_id=' . $this->request->get['filter_stock_status_id'];
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
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['products'] = array();

		$filter_data = array(
			'filter_stock_status_id'   => $filter_stock_status_id,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_extension_module_product->getTotalProducts($filter_data);

		$results = $this->model_extension_module_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_module_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['filter_stock_status_id'])) {
			$url .= '&filter_stock_status_id=' . $this->request->get['filter_stock_status_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_stock_status_id'])) {
			$url .= '&filter_stock_status_id=' . $this->request->get['filter_stock_status_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/reports/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_stock_status_id'] = $filter_stock_status_id;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/total_product_list', $data));
	}

	public function topViewProduct() {
		$this->load->language('extension/module/product');
		
		$this->document->setTitle("Top Viewed Products");

		$this->load->model('extension/module/product');

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = '';
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$filter_data = array(
			'limit'           		=> $limit,
			'category_id'           => $category_id
		);

		$data['heading_title'] ="Top Viewed Products";
		$data['text_list'] ="Top Viewed Product List";

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/topViewProduct', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['products'] = array();

		$this->load->model('tool/image');

		$results = $this->model_extension_module_product->getTopViewProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_module_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'viewed'   => $result['viewed'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$data['limit'] = $limit;
		$data['column_viewed'] = 'Viewed';
		$data['function'] = 'topViewProduct';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/product_list', $data));
	}

	public function lowestViewProduct() {
		$this->load->language('extension/module/product');

		$this->document->setTitle("Lowest Viewed Products");

		$this->load->model('extension/module/product');

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = '';
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$filter_data = array(
			'limit'           		=> $limit,
			'category_id'           => $category_id
		);

		$data['heading_title'] ="Lowest Viewed Products";
		$data['text_list'] ="Lowest Viewed Product List";
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/lowestViewProduct', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['products'] = array();

		$this->load->model('tool/image');

		$results = $this->model_extension_module_product->getLowestViewProducts($filter_data);
 
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_module_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'viewed'   => $result['viewed'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$data['limit'] = $limit;
		$data['column_viewed'] = 'Viewed';
		$data['function'] = 'lowestViewProduct';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/product_list', $data));
	}

	public function highestSellingProduct() {
		$this->load->language('extension/module/product');

		$this->document->setTitle("Highest Selling Products");

		$this->load->model('extension/module/product');

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = '';
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$filter_data = array(
			'limit'           		=> $limit,
			'category_id'           => $category_id
		);

		$data['heading_title'] ="Highest Selling Products";
		$data['text_list'] ="Highest Selling Product List";
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/highestSellingProduct', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['products'] = array();

		$this->load->model('tool/image');

		$results = $this->model_extension_module_product->getHighestSellingProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_module_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'orders'   => $result['orders'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$data['limit'] = $limit;
		$data['column_orders'] = 'Orders';
		$data['function'] = 'highestSellingProduct';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/product_list', $data));
	}

	public function lowestSellingProduct() {
		$this->load->language('extension/module/product');

		$this->document->setTitle("Lowest Selling Products");

		$this->load->model('extension/module/product');

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = '';
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$filter_data = array(
			'limit'           		=> $limit,
			'category_id'           => $category_id
		);
		
		$data['heading_title'] ="Lowest Selling Products";
		$data['text_list'] ="Lowest Selling Product List";
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/lowestSellingProduct', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['products'] = array();

		$this->load->model('tool/image');

		$results = $this->model_extension_module_product->getLowestSellingProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_extension_module_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'orders'   => $result['orders'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		$data['limit'] = $limit;
		$data['column_orders'] = 'Orders';
		$data['function'] = 'lowestSellingProduct';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/product_list', $data));
	}

	public function order() {
		$this->load->language('extension/module/order');

		$this->load->model('extension/module/order');

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}

		if (isset($this->request->get['day_start'])) {
			$day_start = $this->request->get['day_start'];
		} else {
			$day_start = '';
		}

		if (isset($this->request->get['day_end'])) {
			$day_end = $this->request->get['day_end'];
		} else {
			$day_end = '';
		}
		if (isset($this->request->get['month_start'])) {
			$month_start = $this->request->get['month_start'];
		} else {
			$month_start = '';
		}

		if (isset($this->request->get['month_end'])) {
			$month_end = $this->request->get['month_end'];
		} else {
			$month_end = '';
		}

		if (isset($this->request->get['year_start'])) {
			$year_start = $this->request->get['year_start'];
		} else {
			$year_start = '';
		}

		if (isset($this->request->get['year_end'])) {
			$year_end = $this->request->get['year_end'];
		} else {
			$year_end = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['day_start'])) {
			$url .= '&day_start=' . $this->request->get['day_start'];
		}

		if (isset($this->request->get['day_end'])) {
			$url .= '&day_end=' . $this->request->get['day_end'];
		}

		if (isset($this->request->get['month_start'])) {
			$url .= '&month_start=' . $this->request->get['month_start'];
		}

		if (isset($this->request->get['month_end'])) {
			$url .= '&month_end=' . $this->request->get['month_end'];
		}

		if (isset($this->request->get['year_start'])) {
			$url .= '&year_start=' . $this->request->get['year_start'];
		}

		if (isset($this->request->get['year_end'])) {
			$url .= '&year_end=' . $this->request->get['year_end'];
		}

		if ($filter_order_status_id == 1) {
			$data['heading_title'] = "Pending Orders";
			$data['text_list'] = "Pending Order List";
			$this->document->setTitle("Pending Orders");
		}elseif ($filter_order_status_id == 5) {
			$data['heading_title'] = "Complet Orders";
			$data['text_list'] = "Complet Order List";
			$this->document->setTitle("Complet Orders");
		}elseif ($filter_order_status_id == 7) {
			$data['heading_title'] = "Canceled Orders";
			$data['text_list'] = "Canceled Order List";
			$this->document->setTitle("Canceled Orders");
		}elseif ($filter_order_status_id == 11) {
			$data['heading_title'] = "Refunded Orders";
			$data['text_list'] = "Refunded Order List";
			$this->document->setTitle("Refunded Orders");
		}else {
			$data['heading_title'] = "Orders";
			$data['text_list'] = "Order List";
			$this->document->setTitle("Orders");
		}

		if (!empty($day_start && $day_end)) {
			$date_start = $day_start;
			$date_end = $day_end;
		}elseif (!empty($month_start && $month_end)) {
			$date_start = $month_start;
			$date_end = $month_end; 
		}else {
			$date_start = $year_start;
			$date_end = $year_end;
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('report'),
			'href' => $this->url->link('extension/module/reports/info', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['add'] = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true));

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_status_id' => $filter_order_status_id,
			'date_start' 			 => $date_start,
			'date_end' 				 => $date_end,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_extension_module_order->getTotalOrders($filter_data);

		$results = $this->model_extension_module_order->getOrders($filter_data);

		foreach ($results as $result) {
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
				'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
				'edit'          => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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
		$url = '';

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['day_start'])) {
			$url .= '&day_start=' . $this->request->get['day_start'];
		}

		if (isset($this->request->get['day_end'])) {
			$url .= '&day_end=' . $this->request->get['day_end'];
		}

		if (isset($this->request->get['month_start'])) {
			$url .= '&month_start=' . $this->request->get['month_start'];
		}

		if (isset($this->request->get['month_end'])) {
			$url .= '&month_end=' . $this->request->get['month_end'];
		}

		if (isset($this->request->get['year_start'])) {
			$url .= '&year_start=' . $this->request->get['year_start'];
		}

		if (isset($this->request->get['year_end'])) {
			$url .= '&year_end=' . $this->request->get['year_end'];
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['sort_order'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		$url = '';

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['day_start'])) {
			$url .= '&day_start=' . $this->request->get['day_start'];
		}

		if (isset($this->request->get['day_end'])) {
			$url .= '&day_end=' . $this->request->get['day_end'];
		}

		if (isset($this->request->get['month_start'])) {
			$url .= '&month_start=' . $this->request->get['month_start'];
		}

		if (isset($this->request->get['month_end'])) {
			$url .= '&month_end=' . $this->request->get['month_end'];
		}

		if (isset($this->request->get['year_start'])) {
			$url .= '&year_start=' . $this->request->get['year_start'];
		}

		if (isset($this->request->get['year_end'])) {
			$url .= '&year_end=' . $this->request->get['year_end'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/reports/order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
		
		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);
			
			$session->start();
					
			$this->model_user_api->deleteApiSessionBySessionId($session->getId());
			
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/order_list', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_category'])) {
			$this->load->model('extension/module/reports');

			$filter_data = array(
				'filter_category' => $this->request->get['filter_category'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_module_reports->getCategories($filter_data);
			foreach ($results as $result) {
				$json[] = array(
					'category_id' 			=> $result['category_id'],
					'name'           => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function isFirstLoad(){
		$this->load->language('extension/module/reports');

		if (($this->config->get('module_order_status') == null) && ($this->config->get('module_sales_status') == null) && ($this->config->get('module_return_status') ==null) && ($this->config->get('module_tax_status') == null) && ($this->config->get('module_complete_order_status') == null) && ($this->config->get('module_pending_order_status') == null) && ($this->config->get('module_canceled_order_status') == null) && ($this->config->get('module_refunded_order_status') == null) && ($this->config->get('module_product_status') == null) && ($this->config->get('module_outofstock_product_status') == null) && ($this->config->get('module_lowestselling_product_status') == null) && ($this->config->get('module_highestselling_product_status') == null) && ($this->config->get('module_topview_product_status') == null) && ($this->config->get('module_lowestview_product_status') == null) && ($this->config->get('module_customers_status') == null) && ($this->config->get('module_report_category_status') == null) && ($this->config->get('module_enable_category_status') == null) && ($this->config->get('module_disable_category_status') == null) && ($this->config->get('module_nanufacturer_status') == null) && ($this->config->get('module_topbrand_status')) == null) {
			return  $this->language->get('text_isFirstLoad');
		}
	}
}
