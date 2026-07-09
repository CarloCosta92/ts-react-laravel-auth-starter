<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return response()->json([
        "status" => "success",
        "message" => "Utente autenticato",
        "data" => [
            "user" => $request->user()
        ]
    ]);
});

Route::post("/register", function (Request $request) {
    $validated = $request->validate([
        "name" => "required|string",
        "email" => "required|string|email|unique:users",
        "password" => "required|string|min:8"
    ]);

    $user = User::create([
        "name" => $validated["name"],
        "email" => $validated["email"],
        "password" => Hash::make($validated["password"])
    ]);

    $token = $user->createToken("auth_token")->plainTextToken;

    return response()->json([
        "status" => "success",
        "message" => "Registrazione effettuata con successo",
        "data" => [
            "user" => $user,
            "token" => $token
        ]
    ]);
});




Route::post("/login", function (Request $request) {
    $validated = $request->validate([
        "email" => "required|string|email",
        "password" => "required|string"
    ]);

    $user = User::where("email", $validated["email"])->first();

    if (!$user || !Hash::check($validated["password"], $user->password)) {
        return response()->json([
            "status" => "error",
            "message" => "Credenziali non valide",
            "data" => null
        ], 401);
    }
    $token = $user->createToken("auth_token")->plainTextToken;

    return response()->json([
        "status" => "success",
        "message" => "Login effettuato con successo",
        "data" => [
            "user" => $user,
            "token" => $token
        ]
    ]);
});

Route::middleware("auth:sanctum")->post("/logout", function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        "status" => "success",
        "message" => "Logout effettuato",
        "data" => null
    ]);
});
