<?php

class ModelExtensionModuleCommercialOffer extends Model
{
    static string $commercial_offers_table = DB_PREFIX . 'commercial_offers';
    static string $commercial_offer_category_table = DB_PREFIX . 'commercial_offer_category';
    static string $commercial_offer_attribute_table = DB_PREFIX . 'commercial_offer_attribute';

    public function get()
    {
        $sql = "SELECT * FROM " . static::$commercial_offers_table;

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function add($data)
    {
        $this->db->query(
            "INSERT INTO " . 
            static::$commercial_offers_table  . 
            " SET name = '" . $this->db->escape($data['name']) .
            "', is_active = '" . (int)$data['status'] .
             "', date_added = NOW()"
        );

        $offer_id = $this->db->getLastId();

        if (isset($data['product_category'])) {
            foreach ($data['product_category'] as $category_id) {
                $this->db->query(
                    "INSERT INTO " . static::$commercial_offer_category_table  .
                        " SET commercial_offer_id = '" . $this->db->escape($offer_id) . "'," .
                        " category_id = '" . $this->db->escape($category_id) . "'"
                );
            }
        }

        if (isset($data['product_attribute'])) {
            foreach ($data['product_attribute'] as $attribute_id) {
                $this->db->query(
                    "INSERT INTO " . static::$commercial_offer_attribute_table  .
                        " SET commercial_offer_id = '" . $this->db->escape($offer_id) . "'," .
                        " attribute_id = '" . $this->db->escape($attribute_id) . "'"
                );
            }
        }

        return $offer_id;
    }

    public function update($data)
    {
        $commercial_offer_id = $data['commercial_offer_id'];

        $this->db->query(
            "UPDATE " . 
            static::$commercial_offers_table  . 
            " SET name = '" . $this->db->escape($data['name']) . 
            "', is_active = '" . (int)$data['status'] .
            "'  WHERE id = '" . (int)$commercial_offer_id . "'"
        );

        $this->db->query("DELETE FROM " . static::$commercial_offer_attribute_table . " WHERE commercial_offer_id = '" . (int)$commercial_offer_id . "'");

        if (!empty($data['product_attribute'])) {
            foreach ($data['product_attribute'] as $product_attribute) {
                $this->db->query(
                    "INSERT INTO " . static::$commercial_offer_attribute_table  .
                        " SET commercial_offer_id = '" . $this->db->escape($commercial_offer_id) . "'," .
                        " attribute_id = '" . $this->db->escape($product_attribute) . "'"
                );
            }
        }

        $this->db->query("DELETE FROM " . static::$commercial_offer_category_table . " WHERE commercial_offer_id = '" . (int)$commercial_offer_id . "'");

        if (!empty($data['product_category'])) {
            foreach ($data['product_category'] as $product_category) {
                $this->db->query(
                    "INSERT INTO " . static::$commercial_offer_category_table  .
                        " SET commercial_offer_id = '" . $this->db->escape($commercial_offer_id) . "'," .
                        " category_id = '" . $this->db->escape($product_category) . "'"
                );
            }
        } else {
        }
    }

    public function delete($commercial_offer_id)
    {
        $this->db->query("DELETE FROM " . static::$commercial_offers_table . " WHERE id = '" . (int)$commercial_offer_id . "'");
        $this->db->query("DELETE FROM " . static::$commercial_offer_category_table . " WHERE commercial_offer_id = '" . (int)$commercial_offer_id . "'");
        $this->db->query("DELETE FROM " . static::$commercial_offer_attribute_table . " WHERE commercial_offer_id = '" . (int)$commercial_offer_id . "'");
    }

    public function find($id)
    {
        $query = $this->db->query("SELECT * FROM " . static::$commercial_offers_table . " WHERE id = '" . (int)$id . "'");

        return $query->row;
    }
}
