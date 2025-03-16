import React, { useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";



export default function UserDelete() {
  const { id } = useParams(); // รับ id จาก URL
  const navigate = useNavigate();

  useEffect(() => {
    const deleteUser = async () => {
      try {
        console.log(`Attempting to delete user with ID: ${id}`); // Debug log

        const response = await fetch(`http://localhost:5000/users/delete/${id}`, {
          method: "DELETE",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        console.log("Delete response:", result); // Debug log

        if (result.affectedRows >= 1) {
          alert("User deleted successfully!");
          navigate("/"); // กลับไปหน้าหลัก
        } else {
          alert("Failed to delete user");
        }
      } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      }
    };

    if (window.confirm(`Are you sure you want to delete user ID ${id}?`)) {
      deleteUser();
    } else {
      navigate("/"); // ถ้ายกเลิกให้กลับไปหน้าหลัก
    }
  }, [id, navigate]);

  return <h2>Deleting user...</h2>;
}

