<?php

namespace App\Models;

use CodeIgniter\Model;

class TouchTypesModel extends Model
{
    protected $table = 'touch_types';
    protected $primaryKey = 'touch_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];

    public function getTouchTypes($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table($this->table . ' as t');

        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('t.*');
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.name', $searchTerm)
                ->orLike('t.description', $searchTerm)
                ->groupEnd();
        }

        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
