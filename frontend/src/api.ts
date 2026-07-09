const BASE_URL = "http://127.0.0.1:8000/api";

interface ApiResponse<T> {
  status: "success" | "error";
  message: string;
  data: T | null;
}

interface UserData {
  user: { id: number; name: string; email: string };
}

interface AuthData {
  user: { id: number; name: string; email: string };
  token: string;
}

export class ApiError extends Error {
  data: Record<string, string[]> | null;
  constructor(message: string, data: Record<string, string[]> | null) {
    super(message);
    this.data = data;
  }
}

export async function register(
  name: string,
  email: string,
  password: string,
  password_confirmation: string
): Promise<ApiResponse<AuthData>> {
  const res = await fetch(`${BASE_URL}/register`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, email, password, password_confirmation }),
  });
  const json: ApiResponse<AuthData> = await res.json();
  if (!res.ok) throw new ApiError(json.message, json.data as Record<string, string[]> | null);
  return json;
}

export async function login(
  email: string,
  password: string
): Promise<ApiResponse<AuthData>> {
  const res = await fetch(`${BASE_URL}/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password }),
  });
  const json: ApiResponse<AuthData> = await res.json();
  if (!res.ok) throw new ApiError(json.message, json.data as Record<string, string[]> | null);
  return json;
}

export async function getUser(token: string): Promise<ApiResponse<UserData>> {
  const res = await fetch(`${BASE_URL}/user`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  const json: ApiResponse<UserData> = await res.json();
  if (!res.ok) throw new ApiError(json.message, json.data as Record<string, string[]> | null);
  return json;
}

export async function logout(token: string): Promise<ApiResponse<null>> {
  const res = await fetch(`${BASE_URL}/logout`, {
    method: "POST",
    headers: { Authorization: `Bearer ${token}` },
  });
  const json: ApiResponse<null> = await res.json();
  if (!res.ok) throw new ApiError(json.message, json.data as Record<string, string[]> | null);
  return json;
}