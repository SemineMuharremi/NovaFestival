<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - NOVA Festival 2026</title>
    <link rel="stylesheet" href="../../../style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #061621 0%, #0a1f2e 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
        }

        .eticket-container {
            max-width: 900px;
            margin: 40px auto;
        }

        .eticket {
            position: relative;
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(248, 166, 1, 0.25);
            break-inside: avoid;
            display: flex;
        }

        /* Background image covering full ticket */
        .eticket::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url("../../../images/homebg.png") center/cover no-repeat;
            z-index: 1;
        }

        /* Dark overlay for readability */
        .eticket::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(6,22,33,0.85) 0%, rgba(6,22,33,0.7) 50%, rgba(6,22,33,0.55) 100%);
            z-index: 2;
        }

        .eticket-left {
            position: relative;
            z-index: 3;
            flex: 0 0 280px;
            background: linear-gradient(135deg, #f8a601 0%, #d97e04 100%);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #000;
        }

        .ticket-code-section {
            text-align: center;
        }

        .ticket-code-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .ticket-code {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            word-break: break-all;
            line-height: 1.4;
            border: 2px dashed rgba(0,0,0,0.2);
        }

        .barcode-section {
            text-align: center;
        }

        .barcode {
            width: 100%;
            height: 80px;
            background: linear-gradient(90deg, 
                transparent 0%, transparent 10%,
                #000 10%, #000 12%,
                transparent 12%, transparent 18%,
                #000 18%, #000 22%,
                transparent 22%, transparent 26%,
                #000 26%, #000 28%,
                transparent 28%, transparent 35%,
                #000 35%, #000 38%,
                transparent 38%, transparent 42%,
                #000 42%, #000 45%,
                transparent 45%, transparent 52%,
                #000 52%, #000 55%,
                transparent 55%, transparent 58%,
                #000 58%, #000 60%,
                transparent 60%, transparent 68%,
                #000 68%, #000 72%,
                transparent 72%, transparent 76%,
                #000 76%, #000 78%,
                transparent 78%, transparent 85%,
                #000 85%, #000 88%,
                transparent 88%, transparent 95%,
                #000 95%, #000 98%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin: 15px 0;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .eticket-right {
            position: relative;
            z-index: 3;
            flex: 1;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .eticket-header {
            margin-bottom: 30px;
        }

        .festival-name {
            font-size: 32px;
            font-weight: 800;
            color: #f8a601;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }

        .event-type {
            font-size: 24px;
            color: #cbd9ff;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .status-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 1px;
        }

        .eticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px 40px;
            margin: 20px 0;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #f8a601;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .detail-value {
            font-size: 16px;
            color: #f5f8ff;
            font-weight: 600;
            line-height: 1.4;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .eticket-footer {
            border-top: 1px solid rgba(248, 166, 1, 0.3);
            padding-top: 15px;
            font-size: 11px;
            color: #cbd9ff;
        }

        .verification-code {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #f8a601;
            font-weight: bold;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        .eticket-actions {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-print, .btn-download, .btn-close {
            padding: 12px 30px;
            border: 2px solid #f8a601;
            background: transparent;
            color: #f8a601;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-print:hover, .btn-download:hover {
            background: #f8a601;
            color: #061621;
            box-shadow: 0 10px 30px rgba(248, 166, 1, 0.3);
            transform: translateY(-2px);
        }

        .btn-close {
            border-color: #cbd9ff;
            color: #cbd9ff;
        }

        .btn-close:hover {
            background: #cbd9ff;
            color: #061621;
        }

        @media print {
            body {
                background: white;
            }

            .eticket-container {
                margin: 0;
            }

            .eticket-actions {
                display: none;
            }

            .eticket {
                box-shadow: none;
                height: auto;
                min-height: 500px;
            }
        }

        @media (max-width: 768px) {
            .eticket {
                flex-direction: column;
                height: auto;
            }

            .eticket-left {
                flex: 0 0 auto;
                padding: 25px 20px;
            }

            .eticket-right {
                padding: 25px 20px;
            }

            .festival-name {
                font-size: 24px;
            }

            .event-type {
                font-size: 18px;
            }

            .eticket-details {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="eticket-container">
        <div class="eticket">
            <!-- Left Side - Golden ticket info -->
            <div class="eticket-left">
                <div class="ticket-code-section">
                    <div class="ticket-code-label">E-Ticket Code</div>
                    <div class="ticket-code" id="ticketCode">LOADING...</div>
                </div>

                <div class="barcode-section">
                    <div class="barcode"></div>
                    <div style="font-size: 10px; font-weight: bold; letter-spacing: 2px;">NOVA 2026</div>
                </div>
            </div>

            <!-- Right Side - Details -->
            <div class="eticket-right">
                <div>
                    <div class="eticket-header">
                        <div class="festival-name">NOVA</div>
                        <div class="event-type">FESTIVAL 2026</div>
                        <div class="status-badge">✓ ACTIVE</div>
                    </div>

                    <div class="eticket-details">
                        <div class="detail-item">
                            <div class="detail-label">Attendee</div>
                            <div class="detail-value" id="attendeeName">Loading...</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value" id="attendeeEmail">Loading...</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Ticket Type</div>
                            <div class="detail-value" id="ticketType">Loading...</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Quantity</div>
                            <div class="detail-value" id="quantity">Loading...</div>
                        </div>

                        <div class="detail-item full-width">
                            <div class="detail-label">Purchase Date</div>
                            <div class="detail-value" id="purchaseDate">Loading...</div>
                        </div>

                        <div class="detail-item full-width">
                            <div class="detail-label">Total Price</div>
                            <div class="detail-value" id="totalPrice">Loading...</div>
                        </div>
                    </div>
                </div>

                <div class="eticket-footer">
                    <div>This is a valid entry ticket for NOVA Festival 2026. Keep this ticket safe.</div>
                    <div class="verification-code" id="verificationCode">ID: Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="eticket-actions">
        <button class="btn-print" onclick="window.print()">🖨 Print</button>
        <button class="btn-download" onclick="downloadPDF()">⬇ Download PDF</button>
        <button class="btn-close" onclick="window.close()">✕ Close</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        function getQueryParam(name) {
            const params = new URLSearchParams(window.location.search);
            return params.get(name);
        }

        function populateTicket() {
            const token = getQueryParam('token');
            const name = getQueryParam('name');
            const email = getQueryParam('email');
            const type = getQueryParam('type');
            const qty = getQueryParam('qty');
            const date = getQueryParam('date');
            const price = getQueryParam('price');

            document.getElementById('ticketCode').textContent = token || 'INVALID';
            document.getElementById('attendeeName').textContent = decodeURIComponent(name || 'N/A');
            document.getElementById('attendeeEmail').textContent = decodeURIComponent(email || 'N/A');
            document.getElementById('ticketType').textContent = decodeURIComponent(type || 'N/A');
            document.getElementById('quantity').textContent = qty || '0';
            document.getElementById('purchaseDate').textContent = date || 'N/A';
            document.getElementById('totalPrice').textContent = price || '€0.00';
            document.getElementById('verificationCode').textContent = 'ID: ' + (token ? token.substring(0, 20) + '...' : 'INVALID');
        }

        async function downloadPDF() {
            const element = document.querySelector('.eticket');
            const canvas = await html2canvas(element, {
                scale: 2,
                backgroundColor: null,
                useCORS: true
            });

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'px',
                format: [canvas.width, canvas.height]
            });

            pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, canvas.width, canvas.height);
            pdf.save('NOVA-Festival-eticket.pdf');
        }

        populateTicket();
    </script>
</body>
</html>
