<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Hasil UTBK - {{ $result->user->name }}</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .cert-container {
            width: 297mm;
            height: 210mm;
            padding: 20mm;
            box-sizing: border-box;
            position: relative;
            background: #fff;
            border: 20px solid #fff;
        }
        .cert-border-outer {
            border: 5px solid #1a237e;
            height: 100%;
            width: 100%;
            position: relative;
            box-sizing: border-box;
        }
        .cert-border-inner {
            border: 2px solid #c5a059;
            margin: 5px;
            height: calc(100% - 14px);
            width: calc(100% - 14px);
            box-sizing: border-box;
            background-color: rgba(197, 160, 89, 0.03); /* Soft Gold Tint */
        }
        .content {
            text-align: center;
            padding-top: 30px;
        }
        .header-logo {
            font-size: 24px;
            font-weight: bold;
            color: #1a237e;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 60px;
            margin: 0;
            color: #1a237e;
            text-transform: uppercase;
            letter-spacing: 10px;
        }
        .sub-header {
            font-size: 20px;
            color: #c5a059;
            margin-top: -10px;
            font-style: italic;
        }
        .given-to {
            font-size: 24px;
            margin: 30px 0 10px;
        }
        .name {
            font-size: 48px;
            font-weight: bold;
            color: #000;
            text-decoration: underline;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
        }
        .reason {
            font-size: 18px;
            line-height: 1.6;
            width: 80%;
            margin: 0 auto;
        }
        .exam-title {
            font-weight: bold;
            color: #1a237e;
        }
        .score-box {
            margin-top: 30px;
            display: inline-block;
            border: 2px double #c5a059;
            padding: 15px 40px;
            background: #1a237e;
            color: #fff;
            border-radius: 10px;
        }
        .score-label { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; }
        .score-value { font-size: 36px; font-weight: bold; }

        .footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            padding: 0 60px;
            box-sizing: border-box;
        }
        .qr-code { float: left; text-align: left; }
        .signature {
            float: right;
            text-align: center;
            border-top: 2px solid #000;
            width: 200px;
            padding-top: 10px;
        }
        .stamp {
            position: absolute;
            bottom: 150px;
            right: 80px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-border-outer">
            <div class="cert-border-inner">
                <div class="content">
                    <div class="header-logo">Professional UTBK Platform</div>
                    <h1>SERTIFIKAT</h1>
                    <div class="sub-header">Certificate of Achievement</div>

                    <div class="given-to">Diberikan Kepada :</div>
                    <div class="name">{{ $result->user->name }}</div>

                    <div class="reason">
                        Atas keberhasilannya dalam menyelesaikan simulasi ujian nasional<br>
                        <span class="exam-title text-uppercase">{{ $result->exam->title }}</span><br>
                        dengan hasil pencapaian yang memuaskan sebagai berikut:
                    </div>

                    <div class="score-box">
                        <div class="score-label">SKOR AKUMULASI (IRT)</div>
                        <div class="score-value">{{ number_format($result->total_score, 2) }}</div>
                    </div>

                    <p style="margin-top: 15px; font-weight: bold;">Predikat: 
                        <span style="color: #c5a059;">{{ $result->total_score >= 600 ? 'SANGAT BAIK' : ($result->total_score >= 400 ? 'BAIK' : 'CUKUP') }}</span>
                    </p>
                </div>

                <div class="footer">
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url('/verify-cert/'.$result->id) }}" width="80">
                        <div style="font-size: 10px; margin-top: 5px;">VERIFIKASI DIGITAL</div>
                    </div>
                    
                    <div class="signature">
                        <div style="margin-bottom: 40px; font-style: italic; color: #888;">E-Signature Verified</div>
                        <strong>SISTEM AKADEMIK</strong><br>
                        <span>Direktur Pelaksana</span>
                    </div>
                </div>

                <!-- Decorative Stamp -->
                <div class="stamp">
                    <svg width="120" height="120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#1a237e" stroke-width="4" stroke-dasharray="8,4" />
                        <text x="60" y="55" font-family="Arial" font-size="12" text-anchor="middle" fill="#1a237e" font-weight="bold">UTBK-SNBT</text>
                        <text x="60" y="75" font-family="Arial" font-size="10" text-anchor="middle" fill="#1a237e">VERIFIED</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to auto trigger print if needed -->
    <script>
        // window.print();
    </script>
</body>
</html>
