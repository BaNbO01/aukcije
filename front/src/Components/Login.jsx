import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import './Login.css';

const Login = () => {
  const [userData, setUserData] = useState({ email: '', password: '' });
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate();

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setUserData({ ...userData, [name]: value });
  };

  const handleLogin = (e) => {
    e.preventDefault();
    axios
      .post('http://127.0.0.1:8000/api/login', userData)
      .then((response) => {
        if (response.data.success === true) {
          // Čuvanje podataka u session storage
          window.sessionStorage.setItem('auth_token', response.data.access_token);
          window.sessionStorage.setItem('role', response.data.role);
          window.sessionStorage.setItem('user_id', response.data.data.id);
          // Preusmeravanje korisnika nakon uspešne prijave
          navigate('/aukcije');
        } else {
          setErrorMessage(response.data.data);
        }
      })
      .catch((error) => {
        console.error('Greška pri prijavi:', error);
        setErrorMessage('Došlo je do greške. Molimo pokušajte ponovo.');
      });
  };

  return (
    <div className="login-container">
      <div className="login-card">
        <h1 className="login-title">Dobrodošli na Aukcijski Portal</h1>
        <p className="login-subtitle">Prijavite se da biste započeli svoje nadmetanje ili prodaju!</p>
        <form className="login-form" onSubmit={handleLogin}>
          <div className="form-group">
            <label htmlFor="email">Email adresa</label>
            <input
              type="email"
              id="email"
              name="email"
              value={userData.email}
              onChange={handleInputChange}
              placeholder="Unesite vašu email adresu"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="password">Šifra</label>
            <input
              type="password"
              id="password"
              name="password"
              value={userData.password}
              onChange={handleInputChange}
              placeholder="Unesite vašu šifru"
              required
            />
          </div>
          {errorMessage && <p className="error-message">{errorMessage}</p>}
          <button type="submit" className="btn login-btn">
            Prijavi se
          </button>
        </form>
        <p className="login-footer">
          Nemate nalog? <a href="/register">Registrujte se</a>
        </p>
      </div>
    </div>
  );
};

export default Login;
