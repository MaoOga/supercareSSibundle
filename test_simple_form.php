<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
</head>
<body>
    <h2>Test Form for Drains and Post-Operative Monitoring</h2>
    
    <form action="debug_drains_postop.php" method="POST">
        <h3>Drains</h3>
        <p>Drain Used: 
            <input type="radio" name="drain-used" value="Yes"> Yes
            <input type="radio" name="drain-used" value="No"> No
        </p>
        <p>Drain 1: <textarea name="drain_1">Test drain 1</textarea></p>
        <p>Drain 2: <textarea name="drain_2">Test drain 2</textarea></p>
        <p>Drain 3: <textarea name="drain_3">Test drain 3</textarea></p>
        
        <h3>Post-Operative Monitoring</h3>
        <p>Date 1: <input type="text" name="post-operative[date]_1" value="01/01/2024"></p>
        <p>Dosage 1: <textarea name="post-dosage_1">Test dosage 1</textarea></p>
        <p>Discharge 1: <textarea name="type-ofdischarge_1">Test discharge 1</textarea></p>
        <p>Tenderness 1: <textarea name="tenderness-pain_1">Test tenderness 1</textarea></p>
        <p>Swelling 1: <textarea name="swelling_1">Test swelling 1</textarea></p>
        <p>Fever 1: <textarea name="Fever_1">Test fever 1</textarea></p>
        
        <p>Date 2: <input type="text" name="post-operative[date]_2" value="02/01/2024"></p>
        <p>Dosage 2: <textarea name="post-dosage_2">Test dosage 2</textarea></p>
        <p>Discharge 2: <textarea name="type-ofdischarge_2">Test discharge 2</textarea></p>
        <p>Tenderness 2: <textarea name="tenderness-pain_2">Test tenderness 2</textarea></p>
        <p>Swelling 2: <textarea name="swelling_2">Test swelling 2</textarea></p>
        <p>Fever 2: <textarea name="Fever_2">Test fever 2</textarea></p>
        
        <input type="submit" value="Test Submit">
    </form>
</body>
</html>
