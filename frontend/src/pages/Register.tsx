import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { ApiError, register } from "../api";
import { useAuth } from "../AuthContext";

export default function Register() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [error, setError] = useState("");
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
  const { setToken } = useAuth();
  const navigate = useNavigate();

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    setFieldErrors({});
    try {
      const res = await register(name, email, password, passwordConfirmation);
      setToken(res.data!.token);
      navigate("/");
    } catch (err) {
      if (err instanceof ApiError && err.data) {
        setFieldErrors(err.data);
      } else {
        setError("Errore durante la registrazione");
      }
    }
  }

  return (
    <div className="page">
      <div className="card">
        <h2>Registrati</h2>
        <form onSubmit={handleSubmit}>
          <div className="field">
            <input
              type="text"
              placeholder="Nome"
              value={name}
              onChange={(e) => setName(e.target.value)}
            />
            {fieldErrors.name && <p className="error">{fieldErrors.name[0]}</p>}
          </div>
          <div className="field">
            <input
              type="email"
              placeholder="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
            {fieldErrors.email && <p className="error">{fieldErrors.email[0]}</p>}
          </div>
          <div className="field">
            <input
              type="password"
              placeholder="Password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
            {fieldErrors.password && <p className="error">{fieldErrors.password[0]}</p>}
          </div>
          <div className="field">
            <input
              type="password"
              placeholder="Conferma password"
              value={passwordConfirmation}
              onChange={(e) => setPasswordConfirmation(e.target.value)}
            />
          </div>
          {error && <p className="error">{error}</p>}
          <button className="btn" type="submit" style={{ width: "100%" }}>
            Registrati
          </button>
        </form>
      </div>
    </div>
  );
}