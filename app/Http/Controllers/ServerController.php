<?php

namespace App\Http\Controllers;

use App\Models\ComponentType;
use App\Models\Location;
use App\Models\Server;
use App\Models\ServerChecklistItem;
use App\Models\ServerComponent;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::with('location')->orderBy('sort_order')->orderBy('hostname')->get();
        return view('account.servers.index', compact('servers'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('account.servers.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'hostname' => 'required|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'os' => 'nullable|string|max:100',
            'server_type' => 'required|in:physical,virtual,cloud',
            'physical_status' => 'required|in:active,maintenance,decommissioned,inactive',
            'notes' => 'nullable|string|max:65535',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        Server::create($validated);
        return redirect()->route('server.index')->with('success', 'Server berhasil ditambahkan.');
    }

    public function show(Server $server)
    {
        $server->load(['location', 'components.componentType']);

        try {
            $server->load('checklistItems');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), "doesn't exist")) {
                $server->setRelation('checklistItems', new Collection);
            } else {
                throw $e;
            }
        }

        $componentTypes = ComponentType::orderBy('sort_order')->orderBy('name')->get();
        return view('account.servers.show', compact('server', 'componentTypes'));
    }

    public function storeComponent(Request $request, Server $server)
    {
        $validated = $request->validate([
            'component_type_id' => 'required|exists:component_types,id',
            'label' => 'required|string|max:100',
        ]);
        $validated['server_id'] = $server->id;

        $type = ComponentType::find($validated['component_type_id']);
        $values = [];
        if ($type && is_array($type->attributes)) {
            foreach ($type->attributes as $attr) {
                $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($attr['name'] ?? '');
                if ($slug !== '') {
                    $values[$slug] = $request->input('attr_' . $slug, '');
                }
            }
        }
        $validated['values'] = $values;
        $maxOrder = (int) ServerComponent::where('server_id', $server->id)->max('sort_order');
        $validated['sort_order'] = $maxOrder + 1;
        ServerComponent::create($validated);
        return redirect()->route('server.show', $server)->with('success', 'Component berhasil ditambahkan.');
    }

    public function destroyComponentsBulk(Request $request, Server $server)
    {
        $ids = $request->input('components', []);
        if (! is_array($ids)) {
            $ids = [];
        }
        $ids = array_filter(array_map('intval', $ids));
        if (count($ids) > 0) {
            ServerComponent::where('server_id', $server->id)->whereIn('id', $ids)->delete();
        }
        return redirect()->route('server.show', $server)->with('success', count($ids) > 0 ? count($ids) . ' component berhasil dihapus.' : 'Tidak ada component terpilih.');
    }

    public function editComponent(Server $server, ServerComponent $server_component)
    {
        if ($server_component->server_id !== $server->id) {
            abort(404);
        }
        $server_component->load('componentType');
        return view('account.servers.components.edit', compact('server', 'server_component'));
    }

    public function updateComponent(Request $request, Server $server, ServerComponent $server_component)
    {
        if ($server_component->server_id !== $server->id) {
            abort(404);
        }
        $validated = $request->validate([
            'label' => 'required|string|max:100',
        ]);
        $type = $server_component->componentType;
        $values = [];
        if ($type && is_array($type->attributes)) {
            foreach ($type->attributes as $attr) {
                $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($attr['name'] ?? '');
                if ($slug !== '') {
                    $values[$slug] = $request->input('attr_' . $slug, '');
                }
            }
        }
        $validated['values'] = $values;
        $server_component->update($validated);
        return redirect()->route('server.show', $server)->with('success', 'Spesifikasi berhasil diperbarui.');
    }

    public function destroyComponent(Server $server, ServerComponent $server_component)
    {
        if ($server_component->server_id !== $server->id) {
            abort(404);
        }
        $server_component->delete();
        return redirect()->route('server.show', $server)->with('success', 'Component berhasil dihapus.');
    }

    public function storeChecklistItem(Request $request, Server $server)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $validated['server_id'] = $server->id;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        ServerChecklistItem::create($validated);
        return redirect()->route('server.show', $server)->with('success', 'Item checklist berhasil ditambahkan.');
    }

    public function toggleChecklistItem(Server $server, ServerChecklistItem $server_checklist_item)
    {
        if ($server_checklist_item->server_id !== $server->id) {
            abort(404);
        }
        $server_checklist_item->update(['is_checked' => ! $server_checklist_item->is_checked]);
        return redirect()->route('server.show', $server)->with('success', 'Checklist diperbarui.');
    }

    public function destroyChecklistItem(Server $server, ServerChecklistItem $server_checklist_item)
    {
        if ($server_checklist_item->server_id !== $server->id) {
            abort(404);
        }
        $server_checklist_item->delete();
        return redirect()->route('server.show', $server)->with('success', 'Item checklist berhasil dihapus.');
    }

    public function edit(Server $server)
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('account.servers.edit', compact('server', 'locations'));
    }

    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'hostname' => 'required|string|max:100',
            'ip_address' => 'nullable|string|max:45',
            'os' => 'nullable|string|max:100',
            'server_type' => 'required|in:physical,virtual,cloud',
            'physical_status' => 'required|in:active,maintenance,decommissioned,inactive',
            'notes' => 'nullable|string|max:65535',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $server->update($validated);
        return redirect()->route('server.index')->with('success', 'Server berhasil diperbarui.');
    }

    public function destroy(Server $server)
    {
        $server->delete();
        return redirect()->route('server.index')->with('success', 'Server berhasil dihapus.');
    }
}
