import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import "./Navigation.css";

const Navigation = () => {
  const [role, setRole] = useState("");

  useEffect(() => {
    // Izvlačenje uloge iz sessionStorage
    const userRole = sessionStorage.getItem("role");
    setRole(userRole);
  }, []);

  return (
    <nav className="navigation">
      <ul className="nav-list">
        {role === "admin" && (
          <>
            <li>
              <Link to="/all-auctions">Sve Aukcije</Link>
            </li>
            <li>
              <Link to="/users">Prodavci i Kupci</Link>
            </li>
            <li>
              <Link to="/categories">Kategorije Aukcija</Link>
            </li>
          </>
        )}
        {role === "seller" && (
          <>
            <li>
              <Link to="/auctions">Aukcije</Link>
            </li>
            <li>
              <Link to="/my-auctions">Moje Aukcije</Link>
            </li>
            <li>
              <Link to="/create-auction">Kreiraj Aukciju</Link>
            </li>
            <li>
              <Link to="/ebay">eBay</Link>
            </li>
          </>
        )}
        {role === "buyer" && (
          <>
            <li>
              <Link to="/all-auctions">Sve Aukcije</Link>
            </li>
            <li>
              <Link to="/my-bids">Moje Ponude</Link>
            </li>
            <li>
              <Link to="/won-auctions">Pobedjene Aukcije</Link>
            </li>
            <li>
              <Link to="/ebay">eBay</Link>
            </li>
          </>
        )}
      </ul>
    </nav>
  );
};

export default Navigation;
