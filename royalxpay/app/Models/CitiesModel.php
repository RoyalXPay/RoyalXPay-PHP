<?php

namespace App\Models;

use CodeIgniter\Model;

class CitiesModel extends Model
{

    protected $table = 'cities';
    protected $primaryKey = 'city_id';
    protected $allowedFields = ['city_id', 'state_id', 'city_name'];

    public function getData($searchArray = array(), $offset = '', $limit = '', $coutOnly = '')
    {
        
        if ($coutOnly) {
            $sql = "SELECT COUNT(ad.$this->primaryKey) as total_count FROM $this->table AS ad ";
        } else {
            $sql = "SELECT ad.* FROM $this->table AS ad ";
        }
        $sql .= " ORDER BY ad.city_name";

        if ($limit) {
            $sql .= " LIMIT $offset,$limit";
        }

        $query = $this->db->query($sql);
        $result = $query->getResultArray();
        return $result;

    }

    public function getCitiesByState($id)
    {
        $builder = $this->db->table("cities");
        $builder->select("city_id, city_name");
        $builder->where("state_id", $id);
        $result = $builder->get();

        $cities = $result->getResultArray();
        $cityOptions = [];
        foreach ($cities as $city) {
            $cityOptions[$city['city_id']] = $city['city_name'];
        }
        return json_encode($cityOptions);
    }
}
