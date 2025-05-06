<?php

namespace App\Http\Controllers;

use App\Models\Laptop;
use App\Models\Activity;
use Illuminate\Http\Request;

class LaptopInvController extends Controller
{
    
    //Display list of laptops with optional search and status filter
    public function index(Request $request)
    {
        $query = Laptop::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Status filter (available, assigned, maintenance)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laptops = $query->latest()->paginate(10);

        return view('admin.laptops.index', compact('laptops'));
    }

    //Show form to create a new laptop record
    public function create()
    {
        return view('admin.laptops.create');
    }

    //Store a newly created laptop record
    public function store(Request $request)
    {
        $request->validate([
            'asset_tag'     => 'required|unique:laptops',
            'brand'         => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'serial_number' => 'required|unique:laptops',
            'specs'         => 'nullable|string|max:1000',
            'status'        => 'required|in:available,assigned,maintenance',
        ]);

        $laptop = Laptop::create([
            'asset_tag'     => $request->asset_tag,
            'brand'         => $request->brand,
            'model'         => $request->model,
            'serial_number' => $request->serial_number,
            'specs'         => $request->specs,
            'status'        => $request->status,
        ]);

        Activity::create([
            'message' => 'New laptop added: ' . $laptop->asset_tag,
        ]);

        return redirect()->route('admin.laptops.index')
                         ->with('success', 'Laptop added successfully.');
    }

    //Display the details of a specific laptop
    public function show(Laptop $laptop)
    {
        return view('admin.laptops.show', compact('laptop'));
    }

    //Show form to edit an existing laptop
    public function edit(Laptop $laptop)
    {
        return view('admin.laptops.edit', compact('laptop'));
    }

    
    //Update the specified laptop
    public function update(Request $request, Laptop $laptop)
    {
        $request->validate([
            'asset_tag'     => 'required|unique:laptops,asset_tag,' . $laptop->id,
            'brand'         => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'serial_number' => 'required|unique:laptops,serial_number,' . $laptop->id,
            'specs'         => 'nullable|string|max:1000',
            'status'        => 'required|in:available,assigned,maintenance',
        ]);

        $laptop->update([
            'asset_tag'     => $request->asset_tag,
            'brand'         => $request->brand,
            'model'         => $request->model,
            'serial_number' => $request->serial_number,
            'specs'         => $request->specs,
            'status'        => $request->status,
        ]);

        Activity::create([
            'message' => 'Laptop updated: ' . $laptop->asset_tag,
        ]);

        return redirect()->route('admin.laptops.index')
                         ->with('success', 'Laptop updated successfully.');
    }

    //Delete the specified laptop
    public function destroy(Laptop $laptop)
    {
        Activity::create([
            'message' => 'Laptop deleted: ' . $laptop->asset_tag,
        ]);

        $laptop->delete();

        return redirect()->route('admin.laptops.index')
                         ->with('success', 'Laptop deleted successfully.');
    }
}
