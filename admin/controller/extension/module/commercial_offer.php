<?php

class ControllerExtensionModuleCommercialOffer extends Controller
{
    static private array $templates = array(
        'index' => 'extension/module/commercial_offer/index.tpl',
        'add'   => 'extension/module/commercial_offer/add.tpl',
        'edit'  => 'extension/module/commercial_offer/edit.tpl',
    );

    private $token;

    private $error = array();

    private $data = array();

    /**
     * Show a page with a list of commercial offers
     */
    public function index()
    {
        $this->setToken();

        $this->loadLanguages();

        $this->document->setTitle($this->language->get('text_module_list'));

        $this->createBreadcrumbs();

        $this->loadMainControllers();

        $this->data['add'] = $this->createLinkFor('add');
        $this->data['delete'] = $this->createLinkFor('delete');

        $this->data['commercial_offers'] = $this->getCommercialOffers();

        if (isset($this->request->post['selected'])) {
            $this->data['selected'] = (array)$this->request->post['selected'];
        } else {
            $this->data['selected'] = array();
        }

        $this->response->setOutput($this->load->view(static::$templates['index'], $this->data));
    }

    /**
     * Get a list of commercial offers
     */
    private function getCommercialOffers() {
        $this->load->model('extension/module/commercial_offer');

        $offers = $this->model_extension_module_commercial_offer->get();
        
        $data = array();

        foreach ($offers as $offer) {
            $data[] = array(
                'commercial_offer_id' => $offer['id'],
                'name'       => $offer['name'],
                'is_active'  => $offer['is_active'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($offer['date_added'])),
                'edit'       => $this->url->link('extension/module/commercial_offer/edit', $this->token . '&commercial_offer_id=' . $offer['id'], true),
            );
        }

        return $data;
    }

    private function createLinkFor($action) {
        $link = 'extension/module/commercial_offer' . '/' . $action;

        return $this->url->link($link, $this->token, true);
    }

    public function add()
    {
        $this->setToken();

        $this->loadLanguages();

        $this->document->setTitle($this->language->get('heading_title'));

        $this->createBreadcrumbs();

        $this->loadMainControllers();

        $this->data['product_categories'] = $this->loadCategories();
        $this->data['product_attributes'] = $this->loadAttributes();

        $data['action'] = $this->url->link('extension/module/commercial_offer/store', $this->token, true);

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view(static::$templates['add'], $this->data));
    }

    public function edit()
    {
        $this->setToken();

        $this->loadLanguages();

        $this->document->setTitle($this->language->get('heading_title'));

        $this->createBreadcrumbs();

        $this->loadMainControllers();

        $commercial_offer_id = $this->request->get['commercial_offer_id'];

        if (!$commercial_offer_id) {
            $this->redirectTo('index');
        }

        $this->data['commercial_offer'] = $this->findCommercialOffer($commercial_offer_id);
        $this->data['product_categories'] = $this->getCategories($commercial_offer_id);
        $this->data['product_attributes'] = $this->getAttributes($commercial_offer_id);

        $data['action'] = $this->url->link('extension/module/commercial_offer/update', $this->token, true);

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->response->setOutput($this->load->view(static::$templates['edit'], $this->data));
    }

    public function update()
    {
        $this->setToken();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->load->model('extension/module/commercial_offer');

            $this->model_extension_module_commercial_offer->update($this->request->post);

            $this->setSuccessSession();
        }

        $this->redirectTo('index');
    }

    public function store()
    {
        $this->setToken();

        if (!($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm())) {
            $this->redirectTo('add');
        }

        $this->load->model('extension/module/commercial_offer');

        $this->model_extension_module_commercial_offer->add($this->request->post);

        $this->setSuccessSession();

        $this->redirectTo('index');
    }

    public function delete()
    {
        $this->setToken();

        $this->load->model('extension/module/commercial_offer');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $commercial_offer_id) {
                $this->model_extension_module_commercial_offer->delete($commercial_offer_id);
            }

            $this->setSuccessSession();
        }

        $this->redirectTo('index');
    }

    private function redirectTo($action)
    {
        $link = 'extension/module/commercial_offer';

        switch ($action) {
            case 'index':
                break;
            case 'add':
                $link = $link . '/' . $action;
        }

        $this->response->redirect($this->url->link($link, $this->token, 'SSL'));
    }

    private function setSuccessSession() {
        $this->session->data['success'] = $this->language->get('text_success');
    }

    private function findCommercialOffer($id)
    {
        $this->load->model('extension/module/commercial_offer');

        return $this->model_extension_module_commercial_offer->find($id);
    }

    private function getAttributes($commercial_offer_id)
    {
        $this->load->model('extension/module/commercial_offer_attribute');

        return $this->model_extension_module_commercial_offer_attribute->getByCommercialOfferId($commercial_offer_id);
    }

    private function getCategories($commercial_offer_id)
    {
        $this->load->model('extension/module/commercial_offer_category');

        return $this->model_extension_module_commercial_offer_category->getByCommercialOfferId($commercial_offer_id);
    }

    private function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/commercial_offer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    private function loadMainControllers()
    {
        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');
    }

    private function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/commercial_offer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return true;
    }

    private function loadAttributes()
    {
        $this->load->model('catalog/attribute');

        $data = array();

        if (isset($this->request->post['product_attribute'])) {
            $product_attributes = $this->request->post['product_attribute'];
        } elseif (isset($this->request->get['product_id'])) {
            $product_attributes = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
        } else {
            $product_attributes = array();
        }

        $data = array();

        foreach ($product_attributes as $product_attribute) {
            $attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

            if ($attribute_info) {
                $data[] = array(
                    'attribute_id'                  => $product_attribute['attribute_id'],
                    'name'                          => $attribute_info['name'],
                    'product_attribute_description' => $product_attribute['product_attribute_description']
                );
            }
        }

        return $data;
    }

    private function loadCategories()
    {
        $this->load->model('catalog/category');

        $data = array();

        if (isset($this->request->post['product_category'])) {
            $categories = $this->request->post['product_category'];
        } elseif (isset($this->request->get['product_id'])) {
            $categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
        } else {
            $categories = array();
        }

        $data = array();

        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $data[] = array(
                    'category_id' => $category_info['category_id'],
                    'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                );
            }
        }

        return $data;
    }

    private function loadLanguages()
    {
        $this->language->load('extension/module/commercial_offer');

        $this->data['heading_title'] = $this->language->get('text_module_list');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_add'] = $this->language->get('button_add');
        $this->data['button_edit'] = $this->language->get('button_edit');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_confirm'] = $this->language->get('text_confirm');
        $this->data['text_no_results'] = $this->language->get('text_no_results');
    }

    private function setToken()
    {
        $token = 'token=' . $this->session->data['token'];

        $this->token = $token;
        $this->data['token'] = $token;
    }


    /// Create a list of breadcrumbs.
    private function createBreadcrumbs()
    {
        $breadcrumbs = array(
            array(
                'text'      => $this->language->get('text_home'),
                'href'      => $this->url->link('common/home', $this->token, 'SSL'),
                'separator' => false
            ),
            array(
                'text'      => $this->language->get('text_list'),
                'href'      => $this->url->link('extension/extension', $this->token, 'SSL'),
                'separator' => ' :: '
            ),
            array(
                'text'      => $this->language->get('text_module_list'),
                'href'      => $this->url->link('extension/module/commercial_offer', $this->token, 'SSL'),
                'separator' => ' :: '
            )
        );

        $this->data['breadcrumbs'] = $breadcrumbs;
    }


    public function install()
    {
        $this->createCommercialOffersTable();
        $this->createCommercialOfferAttributeTable();
        $this->createCommercialOfferCategoryTable();
    }

    private function createCommercialOffersTable()
    {
        $table = 'commercial_offers';
        $table_exists_sql = sprintf(
            "SHOW TABLES IN `%s` " .
                "LIKE '%s'",
            DB_DATABASE,
            DB_PREFIX . $table
        );
        $data = $this->db->query($table_exists_sql)->rows;

        if (sizeof($data) == 0) {
            $create_table_sql = sprintf(
                'CREATE TABLE `%s`.' .
                    '`%s` (' .
                    '`id` INT NOT NULL AUTO_INCREMENT, ' .
                    '`name` VARCHAR (255) NOT NULL, ' .
                    '`is_active` INT(1) NOT NULL DEFAULT 0, ' .
                    '`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, ' .
                    'PRIMARY KEY (`id`))',
                DB_DATABASE,
                DB_PREFIX . $table
            );

            $this->db->query($create_table_sql);
        }
    }

    private function createCommercialOfferAttributeTable()
    {

        $table = 'commercial_offer_attribute';
        $table_exists_sql = sprintf(
            "SHOW TABLES IN `%s` " .
                "LIKE '%s'",
            DB_DATABASE,
            DB_PREFIX . $table
        );
        $data = $this->db->query($table_exists_sql)->rows;

        if (sizeof($data) == 0) {
            $create_table_sql = sprintf(
                'CREATE TABLE `%s`.' .
                    '`%s` (' .
                    '`id` INT NOT NULL AUTO_INCREMENT, ' .
                    '`commercial_offer_id` INT(11) NOT NULL, ' .
                    '`attribute_id` INT(11) NOT NULL, ' .
                    'PRIMARY KEY (`id`))',
                DB_DATABASE,
                DB_PREFIX . $table
            );

            $this->db->query($create_table_sql);
        }
    }

    private function createCommercialOfferCategoryTable()
    {
        $table = 'commercial_offer_category';
        $table_exists_sql = sprintf(
            "SHOW TABLES IN `%s` " .
                "LIKE '%s'",
            DB_DATABASE,
            DB_PREFIX . $table
        );
        $data = $this->db->query($table_exists_sql)->rows;

        if (sizeof($data) == 0) {
            $create_table_sql = sprintf(
                'CREATE TABLE `%s`.' .
                    '`%s` (' .
                    '`id` INT NOT NULL AUTO_INCREMENT, ' .
                    '`commercial_offer_id` int(11) NOT NULL, ' .
                    '`category_id` int(11) NOT NULL, ' .
                    'PRIMARY KEY (`id`))',
                DB_DATABASE,
                DB_PREFIX . $table
            );

            $this->db->query($create_table_sql);
        }
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "commercial_offers`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "commercial_offer_attribute`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "commercial_offer_category`");
    }
}
