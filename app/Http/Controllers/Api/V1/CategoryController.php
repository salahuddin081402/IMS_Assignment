<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderByDesc('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Category list fetched successfully',
                'data' => $categories,
            ]);
        }catch (\Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching categories',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'status' => ['boolean'],
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        }catch (ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating category',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Category fetched successfully',
                'data' => $category,
            ]);
        }catch (\Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching category',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $category = Category::find($id);
            if (!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $validated = $request->validate([
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'status' => ['sometimes', 'boolean'],
            ]);
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        }catch (ValidationException $e){
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }catch (\Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating category',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $category = Category::find($id);
            if (!$category){
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        }catch (\Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting category',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
