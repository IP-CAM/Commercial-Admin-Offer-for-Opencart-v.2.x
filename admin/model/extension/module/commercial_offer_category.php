<?php

class ModelExtensionModuleCommercialOfferCategory extends Model {
    static string $commercial_offer_category_table = DB_PREFIX . 'commercial_offer_category';

    public function getByCommercialOfferId($id) {
        $this->load->model('catalog/category');

        $data = array();

        $query = $this->db->query(
            "SELECT category_id FROM " . 
            static::$commercial_offer_category_table . 
            " WHERE commercial_offer_id = '" . (int)$id . 
            "' GROUP BY category_id"
        );

        foreach ($query->rows as $row) {
            $category_id = $row['category_id'];

            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $data[] = array(
                    'category_id' => $category_id,
                    'name'         => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
                );
            }
        }
        
        return $data;
    }
}
