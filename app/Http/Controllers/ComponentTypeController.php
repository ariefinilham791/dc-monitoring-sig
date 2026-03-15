<?php

namespace App\Http\Controllers;

use App\Models\ComponentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ComponentTypeController extends Controller
{
    public function index()
    {
        $componentTypes = ComponentType::orderBy('sort_order')->orderBy('name')->get();
        return view('account.component-types.index', compact('componentTypes'));
    }

    public function create()
    {
        return view('account.component-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:component_types,slug',
            'sort_order' => 'nullable|integer|min:0',
            'status_only' => 'nullable|boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['status_only'] = (bool) ($request->boolean('status_only') ?? false);
        $validated['attributes'] = $this->parseAttributesFromRequest($request);

        $validated = $this->onlyExistingColumns($validated);
        ComponentType::create($validated);
        return redirect()->route('component-type.index')->with('success', 'Tipe component berhasil ditambahkan.');
    }

    public function edit(ComponentType $componentType)
    {
        return view('account.component-types.edit', compact('componentType'));
    }

    public function update(Request $request, ComponentType $componentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:component_types,slug,' . $componentType->id,
            'sort_order' => 'nullable|integer|min:0',
            'status_only' => 'nullable|boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['status_only'] = (bool) ($request->boolean('status_only') ?? false);
        $validated['attributes'] = $this->parseAttributesFromRequest($request);
        $componentType->update($this->onlyExistingColumns($validated));
        return redirect()->route('component-type.index')->with('success', 'Tipe component berhasil diperbarui.');
    }

    public function destroy(ComponentType $componentType)
    {
        $componentType->delete();
        return redirect()->route('component-type.index')->with('success', 'Tipe component berhasil dihapus.');
    }

    private function onlyExistingColumns(array $data): array
    {
        if (! Schema::hasTable('component_types')) {
            return $data;
        }
        $columns = Schema::getColumnListing('component_types');
        return array_intersect_key($data, array_flip($columns));
    }

    private function parseAttributesFromRequest(Request $request): array
    {
        $names = $request->input('attr_name', []);
        if (! is_array($names)) {
            return [];
        }
        $out = [];
        foreach (array_filter($names) as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $out[] = [
                'name' => $name,
                'slug' => Str::slug($name),
            ];
        }
        return $out;
    }
}
