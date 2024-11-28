import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import './Register.css';

const Register = () => {
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [role, setRole] = useState('seller'); // Defaultna uloga
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleRegister = async (e) => {
    e.preventDefault();

    // Provera da li se lozinke poklapaju
    if (password !== passwordConfirmation) {
      setError('Lozinke se ne poklapaju.');
      return;
    }

    try {
      // Slanje POST zahteva za registraciju
      const response = await axios.post('http://localhost:8000/api/register', {
        username: username,
        email: email,
        password: password,
        role: role,
      });

      // Ako je registracija uspešna
      if (response.data.success) {
        console.log('Registracija uspešna');
        localStorage.setItem('auth_token', response.data.access_token); // Čuvanje tokena
        navigate('/'); // Redirekcija na početnu stranicu
      } else {
        setError('Greška pri registraciji: ' + JSON.stringify(response.data.data)); // Prikaz greške
      }
    } catch (error) {
      console.error('Greška pri registraciji:', error);
      setError('Došlo je do greške prilikom registracije. Pokušajte ponovo.'); // Generička greška
    }
  };

  return (
    <div className="register-container">
      <div className="register-card">
        <h1 className="register-title">Registrujte se</h1>
        <p className="register-subtitle">Kreirajte svoj nalog da biste započeli aukciju!</p>
        <form className="register-form" onSubmit={handleRegister}>
          <div className="form-group">
            <label htmlFor="username">Korisničko ime</label>
            <input
              type="text"
              id="username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              placeholder="Unesite korisničko ime"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="email">Email adresa</label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Unesite email adresu"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="password">Šifra</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="Unesite šifru"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="passwordConfirmation">Potvrda šifre</label>
            <input
              type="password"
              id="passwordConfirmation"
              value={passwordConfirmation}
              onChange={(e) => setPasswordConfirmation(e.target.value)}
              placeholder="Ponovo unesite šifru"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="role">Uloga</label>
            <select
              id="role"
              value={role}
              onChange={(e) => setRole(e.target.value)}
            >
              <option value="seller">Prodavac</option>
              <option value="buyer">Kupac</option>
            </select>
          </div>
          {error && <p className="error-message">{error}</p>}
          <button type="submit" className="btn register-btn">
            Registrujte se
          </button>
        </form>
        <p className="register-footer">
          Već imate nalog? <a href="/">Prijavite se</a>
        </p>
      </div>
    </div>
  );
};

export default Register;
