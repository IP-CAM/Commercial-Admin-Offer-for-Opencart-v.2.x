<?php

class ModelExtensionModuleCommercialOfferAttribute extends Model {
    static string $commercial_offer_attribute_table = DB_PREFIX . 'commercial_offer_attribute';

    public function getByCommercialOfferId($id) {
        $this->load->model('catalog/attribute');

        $data = array();

        $query = $this->db->query(
            "SELECT attribute_id FROM " . 
            static::$commercial_offer_attribute_table . 
            " WHERE commercial_offer_id = '" . (int)$id . 
            "' GROUP BY attribute_id"
        );

        foreach ($query->rows as $row) {
            $attribute_id = $row['attribute_id'];

            $attribute_info = $this->model_catalog_attribute->getAttribute($attribute_id);

            if ($attribute_info) {
                $data[] = array(
                    'attribute_id' => $attribute_id,
                    'name'         => $attribute_info['name'],
                );
            }
        }
        
        return $data;
    }
}
