<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FoodCategory;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::with('category')->orderBy('id', 'desc')->paginate(5); 
        return view('foods.index', compact('foods'));
    }

    public function create()
    {
        $categories = FoodCategory::all();
        return view('foods.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:food_categories,id',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        Food::create($request->all());

        return redirect()->route('foods.index')->with('success', 'Food added successfully.');
    }

    public function edit(Food $food)
    {
        $categories = FoodCategory::all();
        return view('foods.edit', compact('food', 'categories'));
    }

    public function update(Request $request, Food $food)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:food_categories,id',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $food->update($request->all());

        return redirect()->route('foods.index')->with('success', 'Food updated successfully.');
    }

    public function destroy(Food $food)
    {
        $food->delete();
        return redirect()->route('foods.index')->with('success', 'Food deleted successfully.');
    }
}
