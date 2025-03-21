<?php 
session_start();
include("../include/config.php");
error_reporting(0);

// ลบสินค้า
if(isset($_GET['did'])){
    $did = intval($_GET['did']); // แปลงเป็นเลขจำนวนเต็มเพื่อความปลอดภัย

    // ตรวจสอบว่าสินค้ามีอยู่จริงหรือไม่
    $checkQuery = $dbh->prepare("SELECT pro_img FROM product WHERE pro_id = :did");
    $checkQuery->bindParam(':did', $did, PDO::PARAM_INT);
    $checkQuery->execute();
    $product = $checkQuery->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // ลบรูปภาพที่เกี่ยวข้อง
        if (!empty($product['pro_img']) && file_exists("uploads/product/" . $product['pro_img'])) {
            unlink("uploads/product/" . $product['pro_img']);
        }

        // ลบข้อมูลสินค้า
        $sql = "DELETE FROM product WHERE pro_id = :did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':did', $did, PDO::PARAM_INT);
        $query->execute();

        echo "<script>alert('ลบสินค้าสำเร็จ'); window.location.href='manage_product.php';</script>";
    } else {
        echo "<script>alert('ไม่พบสินค้าที่ต้องการลบ'); window.location.href='manage_product.php';</script>";
    }
}

// เพิ่มสินค้า
if(isset($_POST['addProduct'])) {
    $pro_name = trim($_POST['pro_name']);
    $cat_id = intval($_POST['cat_id']);
    $pro_price = floatval($_POST['pro_price']);
    $pro_cost = floatval($_POST['pro_cost']);

    // ตรวจสอบข้อมูลที่กรอกเข้ามา
    if(empty($pro_name) || empty($cat_id) || empty($pro_price) || empty($pro_cost)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');</script>";
    } else {
        // อัปโหลดรูปภาพสินค้า
        if (!empty($_FILES["pro_img"]["name"])) {
            $fileName = $_FILES["pro_img"]["name"];
            $fileTmp = $_FILES["pro_img"]["tmp_name"];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ["jpg", "jpeg", "png", "gif"];

            // ตรวจสอบประเภทไฟล์
            if (!in_array($fileExt, $allowedExtensions)) {
                echo "<script>alert('ไฟล์รูปภาพต้องเป็น JPG, JPEG, PNG หรือ GIF เท่านั้น');</script>";
            } else {
                // เปลี่ยนชื่อไฟล์เพื่อป้องกันชื่อซ้ำ
                $newFileName = time() . "_" . uniqid() . "." . $fileExt;
                $uploadPath = "uploads/product/" . $newFileName;

                // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    // บันทึกข้อมูลสินค้า
                    $sql = "INSERT INTO product (pro_name, cat_id, pro_price, pro_cost, pro_img) 
                            VALUES (:pro_name, :cat_id, :pro_price, :pro_cost, :pro_img)";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':pro_name', $pro_name, PDO::PARAM_STR);
                    $query->bindParam(':cat_id', $cat_id, PDO::PARAM_INT);
                    $query->bindParam(':pro_price', $pro_price, PDO::PARAM_STR);
                    $query->bindParam(':pro_cost', $pro_cost, PDO::PARAM_STR);
                    $query->bindParam(':pro_img', $newFileName, PDO::PARAM_STR);

                    if($query->execute()) {
                        echo "<script>alert('เพิ่มสินค้าสำเร็จ!'); window.location.href='manage_product.php';</script>";
                    } else {
                        echo "<script>alert('เกิดข้อผิดพลาด! กรุณาลองใหม่');</script>";
                    }
                } else {
                    echo "<script>alert('อัปโหลดรูปภาพไม่สำเร็จ!');</script>";
                }
            }
        } else {
            echo "<script>alert('กรุณาอัปโหลดรูปภาพสินค้า');</script>";
        }
    }
}
?>

 
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>จัดการสินค้า | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="จัดการสินค้า | Admin Panel" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    
    <!-- Third Party CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="css/adminlte.css" />
    
    <!-- Additional CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />
    
    <!-- Custom styles -->
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .action-buttons .btn {
            margin: 2px;
        }
        .product-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .card-title {
            font-weight: 600;
        }
        .badge-product-count {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            margin-left: 8px;
        }
        .add-button {
            margin-bottom: 15px;
        }
        .product-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<a href="manage_product.php">จัดการสินค้า</a>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <?php include("include/navbar.php"); ?>
        
        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="dashboard.php" class="brand-link">
                    <img src="./assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">Admin Panel</span>
                </a>
            </div>
            <?php include("include/sidebar.php"); ?>
        </aside>
        
        <!-- Main Content -->
        <main class="app-main">
            <!-- Content Header -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">จัดการสินค้า</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="dashboard.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active" aria-current="page">จัดการสินค้า</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">รายการสินค้าทั้งหมด 
                                            <?php
                                            $productCount = $dbh->query("SELECT COUNT(*) FROM product")->fetchColumn();
                                            echo '<span class="badge bg-primary badge-product-count">'.$productCount.'</span>';
                                            ?>
                                        </h3>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                        <i class="bi bi-plus-circle"></i> เพิ่มสินค้า
                                    </button>
                                </div>

                                <!-- Modal เพิ่มสินค้า -->
                                <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addProductModalLabel">เพิ่มสินค้า</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="" method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">ชื่อสินค้า</label>
                                                        <input type="text" name="pro_name" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">หมวดหมู่</label>
                                                        <select name="cat_id" class="form-select" required>
                                                            <option value="">เลือกหมวดหมู่</option>
                                                            <?php
                                                            $cat_sql = "SELECT * FROM categories ORDER BY cat_name";
                                                            $cat_query = $dbh->prepare($cat_sql);
                                                            $cat_query->execute();
                                                            $categories = $cat_query->fetchAll(PDO::FETCH_OBJ);
                                                            
                                                            if($cat_query->rowCount() > 0) {
                                                                foreach($categories as $category) {
                                                                    echo '<option value="'.$category->cat_id.'">'.$category->cat_name.'</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">ราคาขาย</label>
                                                        <input type="number" name="pro_price" class="form-control" step="0.01" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">ต้นทุน</label>
                                                        <input type="number" name="pro_cost" class="form-control" step="0.01" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">รูปภาพสินค้า</label>
                                                        <input type="file" name="pro_img" class="form-control" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                    <button type="submit" name="addProduct" class="btn btn-primary">บันทึก</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover product-table">
                                            <thead>
                                                <tr class="table-light">
                                                    <th style="width: 50px" class="text-center">ลำดับ</th>
                                                    <th>รูปภาพ</th>
                                                    <th>ชื่อสินค้า</th>
                                                    <th>หมวดหมู่</th>
                                                    <th>ราคาขาย</th>
                                                    <th>ต้นทุน</th>
                                                    <th>กำไร</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $ret = "SELECT p.*, c.cat_name FROM product p 
                                                        LEFT JOIN categories c ON p.cat_id = c.cat_id 
                                                        ORDER BY p.pro_id DESC";
                                                $query = $dbh->prepare($ret);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;
                                                
                                                if($query->rowCount() > 0) {
                                                    foreach($results as $row) { 
                                                        $profit = $row->pro_price - $row->pro_cost;
                                                        ?>
                                                        <tr class="align-middle">
                                                            <td class="text-center"><?php echo $cnt; ?></td>
                                                            <td class="text-center">
                                                                <?php if($row->pro_img): ?>
                                                                    <img src="uploads/product/<?php echo htmlentities($row->pro_img); ?>" 
                                                                         class="product-image img-thumbnail" 
                                                                         alt="<?php echo htmlentities($row->pro_name); ?>">
                                                                <?php else: ?>
                                                                    <span class="text-muted">ไม่มีรูปภาพ</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlentities($row->pro_name); ?></td>
                                                            <td><?php echo htmlentities($row->cat_name); ?></td>
                                                            <td><?php echo number_format($row->pro_price, 2); ?> บาท</td>
                                                            <td><?php echo number_format($row->pro_cost, 2); ?> บาท</td>
                                                            <td>
                                                                <?php 
                                                                echo number_format($profit, 2) . ' บาท'; 
                                                                $profitPercent = ($profit / $row->pro_cost) * 100;
                                                                echo '<br><small class="text-muted">('.number_format($profitPercent, 2).'%)</small>';
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="action-buttons">
                                                                    <a href="edit-product.php?id=<?php echo $row->pro_id; ?>" 
                                                                       class="btn btn-warning btn-sm">
                                                                       <i class="bi bi-pencil-square"></i> แก้ไข
                                                                    </a>
                                                                    <a href="manage_product.php?did=<?php echo $row->pro_id; ?>" 
                                                                       class="btn btn-danger btn-sm" 
                                                                       onclick="return confirm('คุณต้องการลบสินค้า <?php echo $row->pro_name; ?> ใช่หรือไม่?');">
                                                                       <i class="bi bi-trash"></i> ลบ
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    $cnt++;
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">ไม่พบข้อมูลสินค้า</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Pagination (if needed) -->
                                <?php if($query->rowCount() > 0) { ?>
                                <div class="card-footer bg-white clearfix">
                                    <ul class="pagination pagination-sm m-0 float-end">
                                        <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                                    </ul>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <?php include("include/footer.php"); ?>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="../../dist/js/adminlte.js"></script>
    
    <!-- OverlayScrollbars -->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
</body>
</html>