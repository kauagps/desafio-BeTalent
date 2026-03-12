<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Clients::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validated = $request->validate ([
                'name'=>'required|string|max:255',
                'email'=>'required|email|unique:clients,email',
                'cpf'=>'required|string|size:11|unique:clients,cpf',
            ]);

            $client = Clients::create($validated);

            return response()->json([
                'message'=>'Cliente cadastrado com sucesso!!!',
                'data' => $client
            ], 201);

        } catch (\Iluminate\Validation\ValidationException $e){
            return response()->json(['errors'=>$e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Clients $clients)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clients $clients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clients $clients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clients $clients)
    {
        //
    }
}
