import React, { useState, useEffect } from "react";
import axios from "axios";
import Navigation from "./Navigation";
import "./Auctions.css";

const Auctions = () => {
  const [auctions, setAuctions] = useState([]);
  const [statusFilter, setStatusFilter] = useState("closed"); // Defaultni status
  const [searchTerm, setSearchTerm] = useState(""); // Tekst za pretragu
  const [loading, setLoading] = useState(true); // Indikator učitavanja

  const authToken = sessionStorage.getItem("auth_token");

  // Funkcija za dobijanje aukcija
  const fetchAuctions = async () => {
    setLoading(true); // Početak učitavanja
     // Dohvatanje tokena iz sessionStorage
    try {
      const response = await axios.get("http://localhost:8000/api/auctions", {
        headers: {
          Authorization: `Bearer ${authToken}`, // Dodavanje tokena u zaglavlje
        },
        params: {
          status: statusFilter,
          name: searchTerm,
        },
      });
      if (response.data.success) {
        setAuctions(response.data.data.data); // Postavljanje aukcija
      }
    } catch (error) {
      console.error("Greška prilikom dobijanja aukcija:", error);
    } finally {
      setLoading(false); // Kraj učitavanja
    }
  };

  // Učitavanje aukcija kada se promeni filter ili pretraga
  useEffect(() => {
    fetchAuctions();
  }, [statusFilter, searchTerm]);

  return (
    <div className="home-page">
      <Navigation />
      <div className="filters">
        <button
          className={`filter-btn ${statusFilter === "active" ? "active" : ""}`}
          onClick={() => setStatusFilter("active")}
        >
          Aktivne
        </button>
        <button
          className={`filter-btn ${statusFilter === "closed" ? "active" : ""}`}
          onClick={() => setStatusFilter("closed")}
        >
          Zatvorene
        </button>
        <input
          type="text"
          placeholder="Pretraži aukcije"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="search-bar"
        />
      </div>
      <div className="auctions">
        {loading ? (
          <p>Učitavanje...</p>
        ) : auctions.length > 0 ? (
          auctions.map((auction) => (
            <div key={auction.id} className="auction-card">
              <h3>{auction.title}</h3>
              <p>Status: {auction.status}</p>
              <p>Kategorije: {auction.categories?.map((cat) => cat.name).join(", ")}</p>
            </div>
          ))
        ) : (
          <p>Nema dostupnih aukcija.</p>
        )}
      </div>
    </div>
  );
};

export default Auctions;
