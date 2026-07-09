import { useEffect, useState } from "react";
import { getUser } from "../api";
import { useAuth } from "../AuthContext";

interface User {
  id: number;
  name: string;
  email: string;
}

export default function Home() {
  const { token } = useAuth();
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    if (token) {
getUser(token)
  .then((res) => setUser(res.data!.user))
  .catch(() => setUser(null));    }
    
  }, [token]);

if (!user) return <div className="page"><p>Caricamento...</p></div>;

  return (
    <div className="page">
      <div className="home-card">
        <h2>Benvenuto, {user.name}!</h2>
        <p>{user.email}</p>
      </div>
    </div>
  );
}