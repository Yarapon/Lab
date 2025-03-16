import React, { useState } from 'react';
import { Container, Paper, Grid2, TextField, Button, Typography, Box } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import './App.css';

const AddUser = ({ onUserAdded }) => {
  const [user, setUser] = useState({
    fname: '',
    lname: '',
    username: '',
    password: '',
    avatar: ''
  });
  
  const [errors, setErrors] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const navigate = useNavigate();

  // ฟังก์ชัน update ค่าผู้ใช้เมื่อมีการเปลี่ยนแปลงใน input field
  const handleChange = (e) => {
    const { name, value } = e.target;
    setUser(prevUser => ({
      ...prevUser,
      [name]: value
    }));
  };

  // ตรวจสอบความถูกต้องของข้อมูลในฟอร์ม
  const validateForm = () => {
    let tempErrors = {};
    let isValid = true;
    
    if (!user.fname.trim()) {
      tempErrors.fname = "ชื่อจำเป็นต้องกรอก";
      isValid = false;
    }
    if (!user.lname.trim()) {
      tempErrors.lname = "นามสกุลจำเป็นต้องกรอก";
      isValid = false;
    }
    if (!user.username.trim()) {
      tempErrors.username = "ชื่อผู้ใช้จำเป็นต้องกรอก";
      isValid = false;
    }
    if (!user.password) {
      tempErrors.password = "รหัสผ่านจำเป็นต้องกรอก";
      isValid = false;
    } else if (user.password.length < 6) {
      tempErrors.password = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
      isValid = false;
    }
    if (!user.avatar.trim()) {
      tempErrors.avatar = "URL รูปโปรไฟล์จำเป็นต้องกรอก";
      isValid = false;
    } else if (!isValidUrl(user.avatar)) {
      tempErrors.avatar = "กรุณาใส่ URL ที่ถูกต้อง";
      isValid = false;
    }
    
    setErrors(tempErrors);
    return isValid;
  };

  // ตรวจสอบความถูกต้องของ URL
  const isValidUrl = (url) => {
    try {
      new URL(url);
      return true;
    } catch (e) {
      return false;
    }
  };

  // เมื่อผู้ใช้กด submit ฟอร์มจะเรียก handleSubmit
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (validateForm()) {
      setIsSubmitting(true);
      
      try {
        console.log('Sending user data:', JSON.stringify(user));
        
        // ส่งข้อมูลผู้ใช้ใหม่ไปยัง API
        const response = await fetch('http://localhost:5000/users/create', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(user),
        });
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let result;
        try {
          result = JSON.parse(responseText);
        } catch (e) {
          throw new Error(`Invalid JSON response: ${responseText}`);
        }
        
        if (!response.ok) {
          throw new Error(`Server error (${response.status}): ${result?.message || 'Unknown error'}`);
        }
        
        if (result.status === 'success') {
          // รีเซ็ตฟอร์ม
          setUser({
            fname: '',
            lname: '',
            username: '',
            password: '',
            avatar: ''
          });
          
          if (onUserAdded) {
            onUserAdded({
              id: result.id,
              ...user
            });
          }
          
          alert('เพิ่มผู้ใช้สำเร็จ!');
          // Redirect ไปหน้ารายการผู้ใช้
          navigate('/');
        } else {
          throw new Error(result.message || 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้');
        }
      } catch (error) {
        console.error('Error details:', error);
        alert(`เกิดข้อผิดพลาดในการเพิ่มผู้ใช้: ${error.message}`);
      } finally {
        setIsSubmitting(false);
      }
    }
  };

  return (
    <Container maxWidth="sm" sx={{ mt: 4 }}>
      <Paper sx={{ p: 4, boxShadow: 3 }}>
        <Typography variant="h4" align="center" gutterBottom>
          เพิ่มผู้ใช้ใหม่
        </Typography>
        <form onSubmit={handleSubmit}>
          <Grid2 container spacing={2}>
            <Grid2 item xs={12} sm={6}>
              <TextField
                label="ชื่อ"
                name="fname"
                fullWidth
                value={user.fname}
                onChange={handleChange}
                error={Boolean(errors.fname)}
                helperText={errors.fname}
              />
            </Grid2>
            <Grid2 item xs={12} sm={6}>
              <TextField
                label="นามสกุล"
                name="lname"
                fullWidth
                value={user.lname}
                onChange={handleChange}
                error={Boolean(errors.lname)}
                helperText={errors.lname}
              />
            </Grid2>
            <Grid2 item xs={12}>
              <TextField
                label="ชื่อผู้ใช้"
                name="username"
                fullWidth
                value={user.username}
                onChange={handleChange}
                error={Boolean(errors.username)}
                helperText={errors.username}
              />
            </Grid2>
            <Grid2 item xs={12}>
              <TextField
                label="รหัสผ่าน"
                name="password"
                type="password"
                fullWidth
                value={user.password}
                onChange={handleChange}
                error={Boolean(errors.password)}
                helperText={errors.password}
              />
            </Grid2>
            <Grid2 item xs={12}>
              <TextField
                label="URL รูปโปรไฟล์"
                name="avatar"
                fullWidth
                value={user.avatar}
                onChange={handleChange}
                error={Boolean(errors.avatar)}
                helperText={errors.avatar}
                placeholder="https://example.com/avatar.jpg"
              />
            </Grid2>
            {user.avatar && isValidUrl(user.avatar) && (
              <Grid2 item xs={12} sx={{ textAlign: 'center' }}>
                <Box
                  component="img"
                  src={user.avatar}
                  alt="Avatar Preview"
                  sx={{
                    width: 120,
                    height: 120,
                    borderRadius: '50%',
                    objectFit: 'cover',
                    mx: 'auto'
                  }}
                  onError={(e) => {
                    e.target.onerror = null;
                    e.target.src = 'https://via.placeholder.com/120?text=Error';
                    setErrors(prev => ({...prev, avatar: "ไม่สามารถโหลดรูปภาพได้"}));
                  }}
                />
              </Grid2>
            )}
            <Grid2 item xs={12}>
              <Button
                type="submit"
                variant="contained"
                color="primary"
                fullWidth
                disabled={isSubmitting}
              >
                {isSubmitting ? 'กำลังเพิ่ม...' : 'เพิ่มผู้ใช้'}
              </Button>
            </Grid2>
          </Grid2>
        </form>
      </Paper>
    </Container>
  );
};

export default AddUser;