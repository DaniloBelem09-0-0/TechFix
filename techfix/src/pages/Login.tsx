import { useState, type FormEvent } from 'react';
import './Login.css';

interface LoginProps {
  onLoginSuccess: () => void; // Função que será chamada quando o login der certo
}

export const Login = ({ onLoginSuccess }: LoginProps) => {
  // Estados para capturar o que o usuário digita
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  
  // Estados de controle de interface
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault(); // Evita que a página recarregue
    setError('');
    setIsLoading(true);

    // Validação básica frontend
    if (!email || !password) {
      setError('Por favor, preencha todos os campos.');
      setIsLoading(false);
      return;
    }

    try {
      // --- SIMULAÇÃO DE CHAMADA AO BACKEND ---
      // Aqui você usaria: await api.post('/login', { email, password })
      await new Promise((resolve) => setTimeout(resolve, 1500)); 

      // Vamos simular que o login só funciona com este email
      if (email === 'admin@admin.com' && password === '123456') {
        console.log('Login efetuado!');
        onLoginSuccess(); // Avisa o componente pai que logou
      } else {
        throw new Error('Credenciais inválidas');
      }
      // ---------------------------------------

    } catch (err) {
      setError('Email ou senha incorretos.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="login-container">
      <form className="login-form" onSubmit={handleSubmit}>
        <h2 style={{ textAlign: 'center', marginBottom: '1rem' }}>Acesso ao Sistema</h2>
        
        <div className="form-group">
          <label htmlFor="email">E-mail</label>
          <input 
            type="email" 
            id="email" 
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="admin@admin.com"
          />
        </div>

        <div className="form-group">
          <label htmlFor="password">Senha</label>
          <input 
            type="password" 
            id="password" 
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="123456"
          />
        </div>

        {error && <span className="error-message">{error}</span>}

        <button type="submit" disabled={isLoading}>
          {isLoading ? 'Carregando...' : 'Entrar'}
        </button>
      </form>
    </div>
  );
};