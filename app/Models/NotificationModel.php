<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $protectFields = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['title', 'description', 'attachment', 'created_at', 'updated_at'];

    /**
     * Method to get notifications with optional search and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of notifications or count of records
     */
    public function getNotifications($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table . ' as notifications');

        // If only count is required, select the count of records
        if ($countOnly) {
            $builder->select("COUNT(notifications.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('notifications.*');
        }

        // If search term is provided, filter by title or description
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('notifications.title', $searchTerm)
                ->orLike('notifications.description', $searchTerm)
                ->groupEnd();
        }

        // Order the results by the primary key (id) in descending order
        $builder->orderBy("notifications.{$this->primaryKey}", 'DESC');

        // If pagination is required, add limit and offset
        if ($limit !== '' && $offset !== '') {
            $builder->limit($limit, $offset);
        }

        // Execute the query and get the results
        $query = $builder->get();

        // Return the count if requested, otherwise return the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
