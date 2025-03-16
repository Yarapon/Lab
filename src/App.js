import logo from './logo.svg';
import './App.css';
import React from 'react';
import Users from './Users';
import Navbar from './Navbar';
import { Routes, Route } from 'react-router-dom';
import AddUser from './AddUser';
import UserUpdate from './UserUpdate';
import UserDelete from "./UserDelete"; // Import UserDelete Component

export default function App() {
  return (
    <div> 
      <Navbar />
      <Routes>
        <Route path="/" element={<Users />} />
        <Route path="/create" element={<AddUser />} />
        <Route path="/update/:id" element={<UserUpdate />} />
        <Route path="/delete/:id" element={<UserDelete />} /> {/* เชื่อม UserDelete */}
      </Routes>
    </div>
  )

}