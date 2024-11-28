import logo from './logo.svg';
import './App.css';
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Login from './Components/Login';
import Register from './Components/Register';
import Auctions from './Components/Auctions';

function App() {
  return (
    <Router>
      <div className="App">
         <Routes>
            <Route path="/" element={<Login/>}/>
            <Route path="/register" element={<Register/>}/>
            <Route path="/aukcije" element={<Auctions/>}/>
         </Routes>
      </div>
    </Router>
  );
}

export default App;
