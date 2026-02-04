<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = ApiKey::orderBy('created_at', 'desc')->paginate(20);

        return view('api-keys.index', compact('apiKeys'));
    }

    public function create()
    {
        return view('api-keys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $keyPair = ApiKey::generateKeyPair();

        $apiKey = ApiKey::create([
            'name' => $validated['name'],
            'public_key' => $keyPair['public_key'],
            'secret_key' => $keyPair['secret_key'], // Храним в открытом виде для HMAC
            'is_active' => true,
        ]);

        // Показываем секретный ключ только один раз при создании
        return redirect()->route('api-keys.show', $apiKey)
            ->with('secret_key', $keyPair['secret_key'])
            ->with('success', 'API ключ успешно создан. Сохраните секретный ключ - он больше не будет показан!');
    }

    public function show(ApiKey $apiKey)
    {
        return view('api-keys.show', compact('apiKey'));
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();

        return redirect()->route('api-keys.index')->with('success', 'API ключ успешно удален');
    }

    public function toggle(ApiKey $apiKey)
    {
        $apiKey->update(['is_active' => !$apiKey->is_active]);

        $status = $apiKey->is_active ? 'активирован' : 'деактивирован';

        return redirect()->route('api-keys.index')->with('success', "API ключ успешно {$status}");
    }
}
