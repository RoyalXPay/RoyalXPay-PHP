<?php

namespace App\Models;

use CodeIgniter\Model;

class CountryModel extends Model
{

    protected $table = 'countries';
    protected $primaryKey = 'country_id';
    protected $allowedFields = ['country_id', 'sort_name', 'country_name', 'phone_code'];

    public function getData($searchArray = array(), $offset = '', $limit = '', $coutOnly = '')
    {

        if ($coutOnly) {
            $sql = "SELECT COUNT(ad.$this->primaryKey) as total_count FROM $this->table AS ad ";
        } else {
            $sql = "SELECT ad.* FROM $this->table AS ad ";
        }

        if ($limit) {
            $sql .= " LIMIT $offset,$limit";
        }

        $query = $this->db->query($sql);
        $result = $query->getResultArray();

        return $result;
    }
}
