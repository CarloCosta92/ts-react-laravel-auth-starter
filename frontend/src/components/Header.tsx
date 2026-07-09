import { useNavigate } from "react-router-dom";
import { useAuth } from "../AuthContext";
import { logout } from "../api";

export default function Header() {
  const { token, setToken } = useAuth();
  const navigate = useNavigate();


  async function handleLogout() {
  if (token) {
    try {
      await logout(token);
    } catch {
      // anche se fallisce la chiamata, procediamo comunque a pulire lato client
    }
  }
  setToken(null);
  navigate("/login");
}

  return (
    <header className="header">
      <span className="logo">Auth Demo</span>
      {token && (
        <button className="btn" onClick={handleLogout}>
          Logout
        </button>
      )}
    </header>
  );
}