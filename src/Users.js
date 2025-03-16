import { 
  Avatar, Box, Button, ButtonGroup, Container, Paper, 
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow, 
  Typography 
} from "@mui/material";
import React, { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";

export default function Users() {
  const [users, setUsers] = useState([]);
  const navigate = useNavigate(); // ใช้ navigate แทน window.location.href

  // ดึงข้อมูลผู้ใช้จาก API เมื่อโหลดคอมโพเนนต์
  useEffect(() => {
    UsersGet();
  }, []);
  
  const UsersGet = () => {
    fetch("http://localhost:5000/users")
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data)) {
          setUsers(data);
        } else {
          console.error("Data is not an array:", data);
        }
      })
      .catch(error => console.error("Fetch users error:", error));
  };
  

  // ฟังก์ชันลบผู้ใช้
  const UserDelete = async (id) => {
    if (!window.confirm(`Are you sure you want to delete user ID ${id}?`)) return;

    try {
      const response = await fetch(`http://localhost:5000/users/delete/${id}`, {
        method: "DELETE",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const result = await response.json();
      console.log("Delete response:", result);

      if (result.affectedRows >= 1) {
        alert("User deleted successfully!");
        UsersGet(); // รีเฟรชรายการผู้ใช้
      } else {
        alert("Failed to delete user");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
    }
  };

  // ฟังก์ชันแก้ไขผู้ใช้
  const UpdateUser = (id) => {
    navigate(`/update/${id}`);
  };

  return (
    <Container sx={{ p: 2 }} maxWidth="lg">
      <Paper sx={{ p: 2 }}>
        <Box display="flex">
          <Box flexGrow={1}>
            <Typography component="h2" variant="h6" color="primary" gutterBottom>
              USERS
            </Typography>
          </Box>
          <Box>
            <Link to="/create">
              <Button variant="contained" color="primary">CREATE</Button>
            </Link>
          </Box>
        </Box>

        <TableContainer component={Paper}>
          <Table aria-label="users table">
            <TableHead>
              <TableRow>
                <TableCell align="right">ID</TableCell>
                <TableCell align="center">Avatar</TableCell>
                <TableCell align="left">First Name</TableCell>
                <TableCell align="left">Last Name</TableCell>
                <TableCell align="left">Username</TableCell>
                <TableCell align="center">Action</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {users.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={6} align="center">No users found</TableCell>
                </TableRow>
              ) : (
                users.map((user) => 
                  user && user.id ? (
                    <TableRow key={user.id}>
                      <TableCell align="right">{user.id}</TableCell>
                      <TableCell align="center">
                        <Box display="flex" justifyContent="center">
                          <Avatar src={user.avatar} />
                        </Box>
                      </TableCell>
                      <TableCell align="left">{user.fname}</TableCell>
                      <TableCell align="left">{user.lname}</TableCell>
                      <TableCell align="left">{user.username}</TableCell>
                      <TableCell align="center">
                        <ButtonGroup color="primary">
                          <Button onClick={() => UpdateUser(user.id)}>Edit</Button>
                          <Button onClick={() => UserDelete(user.id)}>Del</Button>
                        </ButtonGroup>
                      </TableCell>
                    </TableRow>
                  ) : null
                )
              )}
            </TableBody>
          </Table>
        </TableContainer>
      </Paper>
    </Container>
  );
}
