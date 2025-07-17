<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengadaan Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pengadaan-container {
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .pengadaan-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .pengadaan-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .form-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .coming-soon {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .coming-soon i {
            font-size: 64px;
            color: #10b981;
            margin-bottom: 20px;
        }
        
        .coming-soon h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #374151;
        }
        
        .coming-soon p {
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="pengadaan-container">
        <div class="pengadaan-header">
            <h2>
                <i class="fas fa-truck-loading"></i>
                Form Pengadaan Produk
            </h2>
        </div>
        
        <div class="form-section">
            <div class="coming-soon">
                <i class="fas fa-tools"></i>
                <h3>Dalam Pengembangan</h3>
                <p>Halaman form pengadaan produk sedang dalam tahap pengembangan.<br>
                Fitur ini akan segera tersedia untuk memudahkan proses pengadaan produk Anda.</p>
            </div>
        </div>
    </div>
</body>
</html>
