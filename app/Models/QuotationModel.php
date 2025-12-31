<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationModel extends Model
{
    protected $table         = 'quotations';
    protected $primaryKey    = 'quotation_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['quotation_code', 'quotation_type', 'user_id', 'start_date', 'end_date', 'total_price', 'vat', 'discount', 'quotation_date', 'final_payable_price', 'citylight_logo', 'citylight_address', 'contact_details', 'citylight_email', 'citylight_website', 'bank_details', 'delivery_time', 'stamp', 'signature', 'return_policies', 'terms_conditions', 'notes', 'status', 'created_at', 'updated_at'];

    /**
     * Method to get products available for rent with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of products or count of records
     */
    public function getRentQuotations(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        $builder = $this->db->table('quotations');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(*) as total_count");
        } else {
            // Concatenate product information for the contract in one row
            $builder->select('quotations.*, users.name as customer_name');
        }

        // Join with the users table
        $builder->join('users', 'users.user_id = quotations.user_id', 'left');

        // Apply search filters if provided
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('quotations.quotation_code', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->groupEnd();
        }

        // Filter by contract type 'rent' (ensuring we only get rent contracts)
        $builder->where('quotations.quotation_type', 'rent');

        // Apply ordering by the primary key in descending order
        $builder->orderBy('quotations.quotation_id', 'DESC');

        // Apply pagination if limit and offset are provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    /**
     * Method to get products available for sale with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of products or count of records
     */
    public function getSaleQuotations(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        $builder = $this->db->table('quotations');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(*) as total_count");
        } else {
            // Concatenate product information for the contract in one row
            $builder->select('quotations.*, users.name as customer_name');
        }

        // Join with the users table
        $builder->join('users', 'users.user_id = quotations.user_id', 'left');

        // Apply search filters if provided
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('quotations.quotation_code', $searchTerm)
                ->orLike('users.name', $searchTerm)
                ->groupEnd();
        }

        // Filter by contract type 'sale' (ensuring we only get sale contracts)
        $builder->where('quotations.quotation_type', 'sale');

        // Apply ordering by the primary key in descending order
        $builder->orderBy('quotations.quotation_id', 'DESC');

        // Apply pagination if limit and offset are provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function generateQuotationCode($quotationType)
    {
        // Prefix based on the quotation type
        $prefix = $quotationType === 'rent' ? 'CL-RQ-' : 'CL-SQ-';
        $currentYear = date('y'); // Get the current year in two digits (e.g., 24 for 2024)

        // Find the last quotation number for the current year
        $lastQuotation = $this->where('quotation_code LIKE', $prefix . '%/' . $currentYear)
            ->orderBy('quotation_code', 'desc')
            ->first();

        // Determine the last number used
        if ($lastQuotation) {
            $lastNumber = (int)substr($lastQuotation['quotation_code'], 7, 4);
        } else {
            $lastNumber = 0;  // If no quotation found, start from 0
        }

        do {
            $nextNumber = $lastNumber + 1;
            $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $quotationCode = $prefix . $formattedNumber . '/' . $currentYear;

            // Check if the code exists in active or deleted quotations
            $existingQuotation = $this->where('quotation_code', $quotationCode)->first();
            $deletedQuotation = model('DeletedQuotation')->where('quotation_code', $quotationCode)->first();

            $lastNumber++;
        } while ($existingQuotation || $deletedQuotation);

        return $quotationCode;
    }

    public function getQuotationWithDetails($quotationId)
    {
        return $this->select('quotations.*, users.name as customer_name, users.phone')
            ->join('users', 'users.user_id = quotations.user_id', 'left')
            ->where('quotations.quotation_id', $quotationId)
            ->first();
    }

    public function quotationReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(quotations.{$this->primaryKey}) as total_count FROM {$this->table} AS quotations ";
        } else {
            $sql = "SELECT
                quotations.*,
                users.name AS customer_name,
                quotation_products.product_id,
                quotation_products.product_master_id,
                quotation_products.quantity,
                quotation_products.price,
                quotation_products.discount,
                product_master.product_name,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type
            FROM {$this->table} AS quotations ";
        }

        // Adding joins
        $sql .= "LEFT JOIN quotation_products ON quotation_products.quotation_id = quotations.quotation_id ";
        $sql .= "LEFT JOIN users ON users.user_id = quotations.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = quotation_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(quotation_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        $whereConditions = [];

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['quotation_code']) && $searchArray['quotation_code']) {
                $quotationCode = $this->db->escapeLikeString($searchArray['quotation_code']);
                $whereConditions[] = "quotations.quotation_code = '$quotationCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "quotations.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "quotations.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY quotations.quotation_id ORDER BY quotations.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }

    public function rentQuotationReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(quotations.{$this->primaryKey}) as total_count FROM {$this->table} AS quotations ";
        } else {
            $sql = "SELECT
                quotations.*,
                users.name AS customer_name,
                quotation_products.product_id,
                quotation_products.product_master_id,
                quotation_products.quantity,
                quotation_products.price,
                quotation_products.discount,
                product_master.product_name,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type
            FROM {$this->table} AS quotations ";
        }

        // Adding joins
        $sql .= "LEFT JOIN quotation_products ON quotation_products.quotation_id = quotations.quotation_id ";
        $sql .= "LEFT JOIN users ON users.user_id = quotations.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = quotation_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(quotation_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        // Defaulting to 'rent' type filter
        $whereConditions = ["quotations.quotation_type = 'rent'"];

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['quotation_code']) && $searchArray['quotation_code']) {
                $quotationCode = $this->db->escapeLikeString($searchArray['quotation_code']);
                $whereConditions[] = "quotations.quotation_code = '$quotationCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "quotations.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "quotations.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY quotations.quotation_id ORDER BY quotations.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }

    public function saleQuotationReport(array $searchArray = [], string $offset = '', string $limit = '', string $countOnly = '')
    {
        // Base SQL Query for selecting the count or results
        if ($countOnly) {
            $sql = "SELECT COUNT(quotations.{$this->primaryKey}) as total_count FROM {$this->table} AS quotations ";
        } else {
            $sql = "SELECT
                quotations.*,
                users.name AS customer_name,
                quotation_products.product_id,
                quotation_products.product_master_id,
                quotation_products.quantity,
                quotation_products.price,
                quotation_products.discount,
                product_master.product_name,
                product_master.description,
                product_master.product_images,
                sizes.size_in_inches,
                colors.name AS color_name,
                touch_types.name AS touch_name,
                glass_types.name AS glass_name,
                resolutions.resolution_type
            FROM {$this->table} AS quotations ";
        }

        // Adding joins
        $sql .= "LEFT JOIN quotation_products ON quotation_products.quotation_id = quotations.quotation_id ";
        $sql .= "LEFT JOIN users ON users.user_id = quotations.user_id ";
        $sql .= "LEFT JOIN products ON products.product_id = quotation_products.product_id ";
        $sql .= "LEFT JOIN product_master ON product_master.product_master_id = IFNULL(quotation_products.product_master_id, products.product_master_id) ";
        $sql .= "LEFT JOIN sizes ON sizes.size_id = product_master.size_id ";
        $sql .= "LEFT JOIN colors ON colors.color_id = product_master.color_id ";
        $sql .= "LEFT JOIN touch_types ON touch_types.touch_id = product_master.touch_id ";
        $sql .= "LEFT JOIN glass_types ON glass_types.glass_id = product_master.glass_id ";
        $sql .= "LEFT JOIN resolutions ON resolutions.resolution_id = product_master.resolution_id ";

        // Defaulting to 'sale' type filter
        $whereConditions = ["quotations.quotation_type = 'sale'"];

        // Applying search filters
        if (!empty($searchArray)) {
            if (isset($searchArray['txtsearch']) && $searchArray['txtsearch']) {
                $searchTerm = $this->db->escapeLikeString($searchArray['txtsearch']);
                $whereConditions[] = "(users.name LIKE '%$searchTerm%' OR users.phone LIKE '%$searchTerm%')";
            }
            if (isset($searchArray['quotation_code']) && $searchArray['quotation_code']) {
                $quotationCode = $this->db->escapeLikeString($searchArray['quotation_code']);
                $whereConditions[] = "quotations.quotation_code = '$quotationCode'";
            }
            if (isset($searchArray['product_name']) && $searchArray['product_name']) {
                $productName = $this->db->escapeLikeString($searchArray['product_name']);
                $whereConditions[] = "product_master.product_name LIKE '%$productName%'";
            }
            if (isset($searchArray['start_date']) && $searchArray['start_date']) {
                $startDate = $this->db->escapeString($searchArray['start_date']);
                $whereConditions[] = "quotations.created_at >= '$startDate'";
            }
            if (isset($searchArray['end_date']) && $searchArray['end_date']) {
                $endDate = $this->db->escapeString($searchArray['end_date']);
                $whereConditions[] = "quotations.created_at <= '$endDate'";
            }
            if (isset($searchArray['size']) && $searchArray['size']) {
                $size = $this->db->escapeLikeString($searchArray['size']);
                $whereConditions[] = "sizes.size_id = '$size'";
            }
            if (isset($searchArray['color']) && $searchArray['color']) {
                $color = $this->db->escapeLikeString($searchArray['color']);
                $whereConditions[] = "colors.color_id = '$color'";
            }
            if (isset($searchArray['touch_type']) && $searchArray['touch_type']) {
                $touchType = $this->db->escapeLikeString($searchArray['touch_type']);
                $whereConditions[] = "touch_types.touch_id = '$touchType'";
            }
            if (isset($searchArray['glass_type']) && $searchArray['glass_type']) {
                $glassType = $this->db->escapeLikeString($searchArray['glass_type']);
                $whereConditions[] = "glass_types.glass_id = '$glassType'";
            }
            if (isset($searchArray['resolution_type']) && $searchArray['resolution_type']) {
                $resolutionType = $this->db->escapeLikeString($searchArray['resolution_type']);
                $whereConditions[] = "resolutions.resolution_id = '$resolutionType'";
            }
        }

        // Adding WHERE conditions to the query
        if (!empty($whereConditions)) {
            $sql .= "WHERE " . implode(' AND ', $whereConditions) . " ";
        }

        // Ordering by the primary key in descending order
        $sql .= "GROUP BY quotations.quotation_id ORDER BY quotations.{$this->primaryKey} DESC ";

        // Pagination (Limit and Offset)
        if ($limit && $offset) {
            $sql .= "LIMIT $offset, $limit ";
        }

        // Executing the query
        $query = $this->db->query($sql);
        $result = $query->getResult();

        // Return either count or the result set
        if ($countOnly) {
            if (empty($result)) {
                return 0;
            } else {
                return $result[0]->total_count;
            }
        }

        return $result;
    }
}
