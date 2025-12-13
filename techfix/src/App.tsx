import { useState } from 'react';
// Importamos a página de Login que criamos
import { Login } from './pages/Login';
// Importamos o UserCard (se você tiver criado ele no passo 1)
import { UserCard } from './components/UserCard'; 
import './App.css';

function App() {
  // Estado que controla se o usuário está logado ou não
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  return (
    <>
      {/* Renderização Condicional:
         SE (!isAuthenticated) for verdade -> Mostra Login
         SENÃO -> Mostra o Dashboard
      */}
      {!isAuthenticated ? (
        <Login onLoginSuccess={() => setIsAuthenticated(true)} />
      ) : (
        <div className="dashboard-container" style={{ padding: '2rem' }}>
          <h1>Bem-vindo ao Sistema</h1>
          <p>Você está logado com segurança.</p>
          
          <div style={{ marginTop: '20px' }}>
            <UserCard 
              name="Usuário Admin" 
              email="admin@admin.com" 
              isActive={true} 
            />
          </div>

          <button 
            onClick={() => setIsAuthenticated(false)}
            style={{ marginTop: '20px', background: '#dc3545' }}
          >
            Sair (Logout)
          </button>
        </div>
      )}
    </>
  );
}

export default App;