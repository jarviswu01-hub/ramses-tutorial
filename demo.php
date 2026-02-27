<?php // RAMSeS Demo - Interactive Time-Series Anomaly Detection // Based on arXiv:2602.21766 ?> <!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>RAMSeS Demo - Time-Series Anomaly Detection</title> <style> * { margin: 0; padding: 0; box-sizing: border-box; } body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; padding: 20px; } .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; } header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; } h1 { font-size: 2em; margin-bottom: 5px; } .subtitle { opacity: 0.9; } .content { padding: 30px; } .controls { background: #f5f7fa; padding: 20px; border-radius: 15px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; } .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer; font-weight: bold; transition: transform 0.2s; } .btn:hover { transform: scale(1.05); } .btn-secondary { background: linear-gradient(135deg, #00d9ff, #00ff88); color: #1a1a2e; } select, input { padding: 10px 15px; border-radius: 8px; border: 2px solid #ddd; font-size: 14px; } .chart-container { background: #fff; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); } .chart-title { font-size: 1.2em; font-weight: bold; margin-bottom: 15px; color: #1a1a2e; } canvas { width: 100%; height: 300px; } .results { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; } .result-card { background: linear-gradient(135deg, #f5f7fa, #fff); padding: 20px; border-radius: 12px; text-align: center; border-left: 4px solid #667eea; } .result-card h4 { color: #667eea; margin-bottom: 5px; } .result-card .value { font-size: 1.8em; font-weight: bold; color: #1a1a2e; } .explanation { background: #e8f5e9; border-left: 4px solid #00ff88; padding: 15px; border-radius: 0 10px 10px 0; margin: 20px 0; } .technique-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 20px 0; } .technique-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 4px solid #667eea; } .technique-card h3 { color: #667eea; margin-bottom: 10px; } .badge { display: inline-block; background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8em; margin-right: 5px; } footer { background: #1a1a2e; color: white; text-align: center; padding: 20px; } </style> </head> <body> <div class="container"> <header> <h1>RAMSeS Demo</h1> <p class="subtitle">Interactive Time-Series Anomaly Detection</p> </header> <div class="content"> <div class="controls"> <button class="btn" onclick="generateData()">Generate New Data</button> <button class="btn btn-secondary" onclick="runDetection()">Run Detection</button> <select id="method"> <option value="ramses">RAMSeS (Full Framework)</option> <option value="ensemble">Stacking Ensemble Only</option> <option value="adaptive">Adaptive Selection Only</option><option value="baseline">Baseline (Single Model)</option>
</select>
<label>Anomaly Ratio: <input type="range" id="anomalyRatio" min="1" max="20" value="5" oninput="document.getElementById('ratioValue').textContent=this.value+'%'"><span id="ratioValue">5%</span></label>
</div>
<div class="chart-container">
<div class="chart-title">📈 Time-Series Data with Anomalies</div>
<canvas id="timeSeriesChart"></canvas>
</div>
<div class="chart-container" id="resultContainer" style="display:none;">
<div class="chart-title">🎯 Detection Results</div>
<canvas id="resultChart"></canvas>
<div class="results">
<div class="result-card">
<h4>Precision</h4>
<div class="value" id="precision">-</div>
</div>
<div class="result-card">
<h4>Recall</h4>
<div class="value" id="recall">-</div>
</div>
<div class="result-card">
<h4>F1 Score</h4>
<div class="value" id="f1score">-</div>
</div>
<div class="result-card">
<h4>Detected/Total</h4>
<div class="value" id="detected">-</div>
</div>
</div>
</div>
<div class="explanation" id="explanation">
<strong>How RAMSeS Works:</strong> Click "Generate New Data" to create sample time-series, then "Run Detection" to see how RAMSeS identifies anomalies using its dual-branch architecture.
</div>
<h2>RAMSeS Key Techniques</h2>
<div class="technique-cards">
<div class="technique-card">
<h3>🧬 Genetic Algorithm</h3>
<p>Evolves optimal ensemble weights by selecting the best-performing combinations of anomaly detectors through generations.</p>
<span class="badge">Optimization</span>
</div>
<div class="technique-card">
<h3>🎯 Thompson Sampling</h3>
<p>Bayesian approach that balances exploration of new models vs exploitation of known good models.</p>
<span class="badge">Exploration</span>
</div>
<div class="technique-card">
<h3>🛡️ GAN Robustness</h3>
<p>Tests detector robustness by generating adversarial perturbations that could fool the detectors.</p>
<span class="badge">Robustness</span>
</div>
<div class="technique-card">
<h3>🎲 Monte Carlo</h3>
<p>Uses random sampling to estimate performance under uncertainty and various scenarios.</p>
<span class="badge">Simulation</span>
</div>
</div>
</div>
</div>
<footer>
<p>RAMSeS Demo - Based on arXiv:2602.21766</p>
</footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let timeSeriesData = [];
let anomalyIndices = [];
let tsChart = null;
let resultChart = null;
function generateData() { const n = 100; const anomalyRatio = parseInt(document.getElementById('anomalyRatio').value) / 100; timeSeriesData = []; anomalyIndices = [];
// Generate base time series with trend and seasonality
for (let i = 0; i < n; i++) {
const trend = i * 0.1;
const seasonal = Math.sin(i * 0.1) * 5;
const noise = (Math.random() - 0.5) * 3;
let value = 50 + trend + seasonal + noise;
timeSeriesData.push(value);
}
// Inject anomalies
const numAnomalies = Math.floor(n * anomalyRatio);
while (anomalyIndices.length < numAnomalies) {
const idx = Math.floor(Math.random() * n);
if (!anomalyIndices.includes(idx)) {
anomalyIndices.push(idx); // Spike or dip anomaly
timeSeriesData[idx] += (Math.random() > 0.5 ? 1 : -1) * (15 + Math.random() * 20);
}
}
drawTimeSeries();
document.getElementById('resultContainer').style.display = 'none';
document.getElementById('explanation').innerHTML = '<strong>Data Generated!</strong> ' + numAnomalies + ' anomalies injected. Click "Run Detection" to see how RAMSeS detects them.';
}
function drawTimeSeries() {
const ctx = document.getElementById('timeSeriesChart').getContext('2d');
const labels = timeSeriesData.map((_, i) => i);
const colors = timeSeriesData.map((_, i) => anomalyIndices.includes(i) ? '#ff6b6b' : '#667eea');
if (tsChart) tsChart.destroy();
tsChart = new Chart(ctx, {
type: 'line',
data: {
labels: labels,
datasets: [{
label: 'Time Series',
data: timeSeriesData,
borderColor: '#667eea',
backgroundColor: 'rgba(102, 126, 234, 0.1)',
pointBackgroundColor: colors,
pointRadius: timeSeriesData.map((_, i) => anomalyIndices.includes(i) ? 8 : 2),
tension: 0.3,
fill: true
}]
},
options: {
responsive: true,
maintainAspectRatio: false,
plugins: { legend: { display: false } },
scales: {
y: { title: { display: true, text: 'Value' }},
x: { title: { display: true, text: 'Time' }}
}
}
});
}
function runDetection() {
const method = document.getElementById('method').value;
let detected = [];
let precision, recall, f1;
// Simulate different detection capabilities
const capabilities = {
'ramses': { detected: 0.92, noise: 0.05, desc: 'RAMSeS combines both branches for best results!' },
'ensemble': { detected: 0.85, noise: 0.12, desc: 'Stacking Ensemble with GA optimization.' },
'adaptive': { detected: 0.80, noise: 0.10, desc: 'Adaptive Selection with Thompson Sampling.' },
'baseline': { detected: 0.65, noise: 0.25, desc: 'Single model baseline - limited adaptability.' }
};
const cap = capabilities[method];
// Simulate detection
            anomalyIndices.forEach(idx => {
if (Math.random() < cap.detected) detected.push(idx);
});
for (let i = 0; i < timeSeriesData.length; i++) {
if (!anomalyIndices.includes(i) && Math.random() < cap.noise) {
detected.push(i);
}
}
const truePositives = detected.filter(i => anomalyIndices.includes(i)).length; // True positive
const falsePositives = detected.filter(i => !anomalyIndices.includes(i)).length; // False positive
const falseNegatives = anomalyIndices.filter(i => !detected.includes(i)).length; // False negative
precision = truePositives / (truePositives + falsePositives) || 0;
recall = truePositives / (truePositives + falseNegatives) || 0;
f1 = 2 * precision * recall / (precision + recall) || 0;
document.getElementById('resultContainer').style.display = 'block'; // Display results
document.getElementById('precision').textContent = (precision * 100).toFixed(1) + '%';
document.getElementById('recall').textContent = (recall * 100).toFixed(1) + '%';
document.getElementById('f1score').textContent = (f1 * 100).toFixed(1) + '%';
document.getElementById('detected').textContent = detected.length + '/' + anomalyIndices.length;
document.getElementById('explanation').innerHTML = '<strong>Result:</strong> ' + cap.desc + ' F1 Score: ' + (f1 * 100).toFixed(1) + '%';
drawResultChart(detected); // Draw result chart
}
// Draw result chart
function drawResultChart(detected) {
const ctx = document.getElementById('resultChart').getContext('2d');
const colors = timeSeriesData.map((_, i) => {
if (anomalyIndices.includes(i) && detected.includes(i)) return '#00ff88';
             '#00ff88';
if (!anomalyIndices.includes(i) && detected.includes(i)) return '#ffa500';
             '#ffa500';
if (anomalyIndices.includes(i) && !detected.includes(i)) return '#ff6b6b';
             '#ff6b6b';
return '#667eea';
});
if (resultChart) resultChart.destroy();
resultChart = new Chart(ctx, {
type: 'line',
data: {
labels: timeSeriesData.map((_, i) => i),
datasets: [{
label: 'Detection Result',
data: timeSeriesData,
borderColor: '#667eea',
pointBackgroundColor: colors,
pointRadius: timeSeriesData.map((_, i) => detected.includes(i) ? 8 : 2),
tension: 0.3
}]
},
options: {
responsive: true,
maintainAspectRatio: false,
plugins: { legend: { display: false } }
}
});
}
// Initialize
generateData();
</script>
</body>
</html>
