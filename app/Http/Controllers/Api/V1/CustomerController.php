<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        try {

            $customers = Customer::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Customers fetched successfully',
                'data' => $customers
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers'
            ],500);

        }
    }
}