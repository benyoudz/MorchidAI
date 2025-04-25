<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام حجز الرحلات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .phone-mockup {
            width: 375px;
            height: 667px;
            background: white;
            border-radius: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            border: 12px solid #111;
        }
        
        .status-bar {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            font-size: 14px;
        }
        
        .screen-content {
            height: calc(100% - 50px);
            overflow-y: auto;
        }
        
        .current-position {
            background-color: #e3f2fd;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1px;
            font-size: 14px;
        }
        
        .trip-section {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            background-color: white;
            position: relative;
        }
        
        .transport-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
        }
        
        .trip-route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-right: 40px;
        }
        
        .station {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            margin: 0 5px;
            border: 1px solid #e0e0e0;
        }
        
        .trip-cost {
            text-align: center;
            font-size: 14px;
            padding-right: 40px;
        }
        
        .cost-label {
            color: #7f8c8d;
            margin-bottom: 3px;
        }
        
        .cost-value {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .destination-section {
            padding: 15px;
            background-color: white;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .destination-label {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .destination-value {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .total-section {
            padding: 20px;
            background-color: #f5f5f5;
            text-align: center;
            margin-top: 10px;
        }
        
        .total-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .total-price {
            font-weight: bold;
            font-size: 22px;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .done-button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .done-button:hover {
            background-color: #219653;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .arrow-icon {
            font-size: 20px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="phone-mockup">
        <div class="status-bar">
            <span>9:41</span>
            <span>DEPART</span>
            <span>100%</span>
        </div>
        
        <div class="screen-content">
            <div class="current-position">
                CURRENT POSITION
            </div>
            
            <!-- الرحلة الجوية -->
            <div class="trip-section">
                <img src="img/air.png" alt="طائرة" class="transport-icon">
                <div class="trip-route">
                    <div class="station">BECHAR AIRLINE</div>
                    <div class="arrow-icon">➔</div>
                    <div class="station">ORAN AIRLINE</div>
                </div>
                <div class="trip-cost">
                    <div class="cost-label">TRIP COST</div>
                    <div class="cost-value">8000 DZD</div>
                </div>
            </div>
            
            <!-- الرحلة بالسيارة -->
            <div class="trip-section">
                <img src="img/taxi.png" alt="تاكسي" class="transport-icon">
                <div class="trip-route">
                    <div class="station">ORAN AIRLINE</div>
                    <div class="arrow-icon">➔</div>
                    <div class="station">SENIA TRAMWAY STATION</div>
                </div>
                <div class="trip-cost">
                    <div class="cost-label">TRIP COST</div>
                    <div class="cost-value">300 DZD</div>
                </div>
            </div>
            
            <!-- الرحلة بالترام -->
            <div class="trip-section">
                <img src="img/trun.png" alt="ترام" class="transport-icon">
                <div class="trip-route">
                    <div class="station">SENIA TRAMWAY STATION</div>
                    <div class="arrow-icon">➔</div>
                    <div class="station">YOUR DESTINATION</div>
                </div>
                <div class="trip-cost">
                    <div class="cost-label">TRIP COST</div>
                    <div class="cost-value">40 DZD</div>
                </div>
            </div>
            
            <div class="destination-section">
                <div class="destination-label">DESTINATION</div>
                <div class="destination-value">VOLUNTARIES RESIDENCE</div>
            </div>
            
            <div class="total-section">
                <div class="total-label">TOTAL PRICE</div>
                <div class="total-price">8340 DZD</div>
                <button class="done-button">DONE</button>
            </div>
        </div>
    </div>
</body>
</html>