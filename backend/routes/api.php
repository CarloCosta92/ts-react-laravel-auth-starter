<?php

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;


Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return response()->json([
        "status" => "success",
        "message" => "Utente autenticato",
        "data" => [
            "user" => $request->user()
        ]
    ]);
});

Route::post("/register", function (RegisterRequest $request) {
    $validated = $request->validated();

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




Route::post("/login", function (LoginRequest $request) {
    $validated = $request->validated();

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

Route::post("/forgot-password", function (ForgotPasswordRequest $request) {
    Password::sendResetLink(
        $request->validated(),
        function ($user, $token) {
            $user->notify(new \App\Notifications\ResetPasswordNotification($token));
        }
    );

    return response()->json([
        "status" => "success",
        "message" => "Se l'indirizzo esiste, riceverai un'email con le istruzioni per il reset",
        "data" => null
    ]);
});

Route::post("/reset-password", function (ResetPasswordRequest $request) {
    $status = Password::reset(
        $request->validated(),
        function ($user, $password) {
            $user->forceFill([
                "password" => Hash::make($password)
            ])->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()->json([
            "status" => "success",
            "message" => "Password aggiornata con successo",
            "data" => null
        ]);
    }

    return response()->json([
        "status" => "error",
        "message" => "Token non valido o scaduto",
        "data" => null
    ], 400);
});
