import React from 'react';

// 1. A Interface define o "formato" dos dados que esse card aceita.
// Isso impede que você passe um número onde deveria ser texto, por exemplo.
interface UserCardProps {
  name: string;
  email: string;
  age?: number; // O '?' torna esta propriedade opcional
  isActive: boolean;
}

export const UserCard = ({ name, email, age, isActive }: UserCardProps) => {
  return (
    <div style={styles.card}>
      <div style={styles.header}>
        <h3 style={styles.name}>{name}</h3>
        {/* Renderização condicional do status (bolinha verde/vermelha) */}
        <span 
          style={{ 
            ...styles.status, 
            backgroundColor: isActive ? '#28a745' : '#dc3545' 
          }} 
        />
      </div>
      
      <p style={styles.info}><strong>Email:</strong> {email}</p>
      
      {/* Só mostra a idade se ela tiver sido informada */}
      {age && <p style={styles.info}><strong>Idade:</strong> {age} anos</p>}

      <div style={styles.footer}>
        Status: <strong>{isActive ? 'Ativo' : 'Inativo'}</strong>
      </div>
    </div>
  );
};

// Estilos em objeto (CSS-in-JS simples) para facilitar o copy-paste
// Em projetos reais, você usaria classes CSS ou Styled Components
const styles = {
  card: {
    border: '1px solid #e1e4e8',
    borderRadius: '8px',
    padding: '16px',
    maxWidth: '300px',
    backgroundColor: '#fff',
    boxShadow: '0 2px 4px rgba(0,0,0,0.05)',
  },
  header: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '12px',
  },
  name: {
    margin: 0,
    fontSize: '1.2rem',
    color: '#333',
  },
  status: {
    width: '10px',
    height: '10px',
    borderRadius: '50%',
    display: 'inline-block',
  },
  info: {
    margin: '4px 0',
    color: '#666',
    fontSize: '0.9rem',
  },
  footer: {
    marginTop: '12px',
    paddingTop: '12px',
    borderTop: '1px solid #eee',
    fontSize: '0.85rem',
    color: '#888',
  }
};