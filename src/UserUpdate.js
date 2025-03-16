import React, { useEffect, useState } from "react";
import { Button, Container, Grid2, TextField, Typography, Dialog, DialogTitle, DialogContent, DialogActions } from "@mui/material";
import { useParams, useNavigate } from "react-router-dom";

export default function UserUpdate() {
  const [fname, setFname] = useState('');
  const [lname, setLname] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPass] = useState('');
  const [avatar, setAvatar] = useState('');
  const [openDialog, setOpenDialog] = useState(false);
  
  const navigate = useNavigate(); // Add useNavigate hook for redirection
  const { id } = useParams();

  useEffect(() => {
    fetch('http://localhost:5000/users/' + id)
      .then(response => response.json())
      .then(result => {
        if (result.length > 0) {
          setFname(result[0].fname);
          setLname(result[0].lname);
          setUsername(result[0].username);
          setPass(result[0].password);
          setAvatar(result[0].avatar);
        }
      });
  }, [id]);

  const handleSubmit = (e) => {
    e.preventDefault();
    setOpenDialog(true); // เปิด Dialog เมื่อกด Update
  };

  const confirmUpdate = () => {
    const data = {
      fname: fname,
      lname: lname,
      username: username,
      password: password,
      avatar: avatar,
    };
    fetch('http://localhost:5000/users/update/' + id, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    .then(res => res.json())
    .then(result => {
      if (result['affectedRows'] >= 1) {
        alert('The user has been updated successfully!'); // แสดงข้อความเมื่ออัปเดตสำเร็จ
        navigate('/'); // Redirect to the user list page after successful update
      }
      setOpenDialog(false); // ปิด Dialog
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred. Please try again.'); // แสดงข้อความเมื่อเกิดข้อผิดพลาด
      setOpenDialog(false); // ปิด Dialog
    });
  };

  return (
    <Container sx={{ p: 2 }} maxWidth="sm">
      <Typography component="h1" variant="h5">Update User</Typography>
      <form onSubmit={handleSubmit}>
        <Grid2 container spacing={2}>
          <Grid2 item xs={12} sm={6}>
            <TextField
              variant="outlined"
              required
              fullWidth
              label="First Name"
              value={fname}
              onChange={(e) => setFname(e.target.value)}
              autoFocus
            />
          </Grid2>
          <Grid2 item xs={12} sm={6}>
            <TextField
              variant="outlined"
              required
              fullWidth
              label="Last Name"
              value={lname}
              onChange={(e) => setLname(e.target.value)}
            />
          </Grid2>
          <Grid2 item xs={12}>
            <TextField
              variant="outlined"
              required
              fullWidth
              label="Username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
            />
          </Grid2>
          <Grid2 item xs={12}>
            <TextField
              variant="outlined"
              required
              fullWidth
              label="Password"
              type="password"
              value={password}
              onChange={(e) => setPass(e.target.value)}
            />
          </Grid2>
          <Grid2 item xs={12}>
            <TextField
              variant="outlined"
              required
              fullWidth
              label="Avatar"
              value={avatar}
              onChange={(e) => setAvatar(e.target.value)}
            />
          </Grid2>
          <Grid2 item xs={12}>
            <Button type="submit" fullWidth variant="contained" color="primary">
              Update
            </Button>
          </Grid2>
        </Grid2>
      </form>

      {/* Dialog สำหรับยืนยันการอัปเดต */}
      <Dialog open={openDialog} onClose={() => setOpenDialog(false)}>
        <DialogTitle>Confirm Update</DialogTitle>
        <DialogContent>
          <Typography>Are you sure you want to update this user?</Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpenDialog(false)} color="primary">
            Cancel
          </Button>
          <Button onClick={confirmUpdate} color="primary">
            Confirm
          </Button>
        </DialogActions>
      </Dialog>
    </Container>
  );
}