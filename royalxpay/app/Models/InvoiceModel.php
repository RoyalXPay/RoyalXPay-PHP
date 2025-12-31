<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    protected $allowedFields = [
        'invoice_code',
        'product_name',
        'company_name',
        'rent_or_sale',
        'invoice_date',
        'quantity',
        'amount_per_unit',
        'paid_amount',
        'vat',
        'total_paid_amount',
        'citylight_logo',
        'citylight_address',
        'contact_details',
        'citylight_email',
        'citylight_website',
        'bank_details',
        'signature',
        'stamp',
        'return_policies',
        'terms_and_conditions',
        'created_at',
        'updated_at'
    ];

    public function getInvoiceDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('t.*');
        }

        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.invoice_code', $searchTerm)
                ->orLike('t.product_name', $searchTerm)
                ->orLike('t.company_name', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Limit the results if limit and offset are provided
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
}
