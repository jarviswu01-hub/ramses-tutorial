<?php
// RAMSeS Tutorial - Time-Series Anomaly Detection
// Based on arXiv:2602.21766
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAMSeS Tutorial - Time-Series Anomaly Detection</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.8; color: #333; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 40px; text-align: center; }
        h1 { font-size: 2.5em; margin-bottom: 10px; background: linear-gradient(90deg, #00d9ff, #00ff88); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .subtitle { font-size: 1.2em; opacity: 0.9; color: #00d9ff; }
        .meta { margin-top: 15px; font-size: 0.9em; opacity: 0.7; }
        nav { background: #f8f9fa; padding: 15px 40px; border-bottom: 1px solid #eee; display: flex; gap: 20px; flex-wrap: wrap; }
        nav a { color: #667eea; text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; }
        nav a:hover { background: #667eea; color: white; }
        .content { padding: 40px; }
        section { margin-bottom: 40px; }
        h2 { color: #1a1a2e; font-size: 1.8em; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 3px solid #667eea; }
        h3 { color: #764ba2; font-size: 1.3em; margin: 25px 0 15px; }
        p { margin-bottom: 15px; }
        .highlight-box { background: #f5f7fa; border-left: 5px solid #667eea; padding: 20px; border-radius: 0 10px 10px 0; margin: 20px 0; }
        .diagram { background: #f0f4ff; border: 2px dashed #667eea; border-radius: 15px; padding: 30px; text-align: center; margin: 30px 0; }
        .diagram-box { display: inline-block; background: white; padding: 15px 25px; border-radius: 10px; margin: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-weight: bold; }
        .branch { background: linear-gradient(135deg, #00d9ff, #00ff88); color: #1a1a2e; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0; }
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-top: 4px solid #667eea; }
        .btn { display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; margin: 10px 5px; }
        footer { background: #1a1a2e; color: white; text-align: center; padding: 30px; margin-top: 40px; }
        .problem-solution { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .problem { background: #ffe5e5; border-left: 5px solid #ff6b6b; padding: 25px; border-radius: 15px; }
        .solution { background: #e5ffe5; border-left: 5px solid #00ff88; padding: 25px; border-radius: 15px; }
        @media (max-width: 768px) { .problem-solution { grid-template-columns: 1fr; } h1 { font-size: 1.8em; } .content { padding: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>RAMSeS</h1>
            <p class="subtitle">Robust and Adaptive Model Selection for Time-Series Anomaly Detection</p>
            <p class="meta">arXiv:2602.21766 | Feb 2026 | cs.DB</p>
        </header>
        
        <nav>
            <a href="#problem">The Problem</a>
            <a href="#overview">Overview</a>
            <a href="#architecture">Architecture</a>
            <a href="#techniques">Techniques</a>
            <a href="#resources">Resources</a>
        </nav>
        
        <div class="content">
            <section id="problem">
                <h2>The Problem</h2>
                <div class="problem-solution">
                    <div class="problem">
                        <h3>Problem</h3>
                        <ul>
                            <li>No universal anomaly detector works for all time-series data</li>
                            <li>What counts as anomaly is context-dependent</li>
                            <li>Methods that work well on one dataset fail on others</li>
                            <li>Traditional approaches lack adaptability</li>
                        </ul>
                    </div>
                    <div class="solution">
                        <h3>Solution: RAMSeS</h3>
                        <ul>
                            <li>Dual-branch strategy (ensemble + selection)</li>
                            <li>Genetic algorithm for ensemble optimization</li>
                            <li>Adaptive model selection with Thompson sampling</li>
                            <li>GAN-based robustness testing</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <section id="overview">
                <h2>Overview</h2>
                <p><strong>RAMSeS</strong> (Robust and Adaptive Model Selection) is a framework for time-series anomaly detection that addresses domain shift.</p>
                <div class="highlight-box">
                    <strong>Key Insight:</strong> Uses a dual strategy combining ensemble strength with adaptive model selection.
                </div>
                <p>Outperforms prior methods on F1 score across diverse datasets.</p>
            </section>
            
            <section id="architecture">
                <h2>Architecture</h2>
                <div class="diagram">
                    <div class="diagram-box branch">Input Time-Series</div>
                    <br><br>
                    <div style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap;">
                        <div class="diagram-box branch">Branch 1:<br>Stacking Ensemble<br>+ Genetic Algorithm</div>
                        <div class="diagram-box branch">Branch 2:<br>Adaptive Selection<br>+ Thompson Sampling</div>
                    </div>
                    <br><br>
                    <div class="diagram-box branch">Final Output: Anomaly Predictions</div>
                </div>
                
                <h3>Branch 1: Stacking Ensemble</h3>
                <p>Combines multiple anomaly detectors. Genetic algorithm optimizes ensemble weights.</p>
                
                <h3>Branch 2: Adaptive Selection</h3>
                <p>Selects best detector using Thompson sampling, GAN robustness testing, and Monte Carlo simulations.</p>
            </section>
            
            <section id="techniques">
                <h2>Key Techniques</h2>
                <div class="card-grid">
                    <div class="card">
                        <h4>Thompson Sampling</h4>
                        <p>Bayesian approach for balancing exploration vs exploitation in model selection.</p>
                    </div>
                    <div class="card">
                        <h4>GAN Robustness</h4>
                        <p>Uses generative adversarial networks to test detector robustness against adversarial perturbations.</p>
                    </div>
                    <div class="card">
                        <h4>Monte Carlo</h4>
                        <p>Simulates various scenarios to estimate detector performance under uncertainty.</p>
                    </div>
                    <div class="card">
                        <h4>Genetic Algorithm</h4>
                        <p>Optimizes ensemble weights through evolutionary selection of best-performing detector combinations.</p>
                    </div>
                </div>
            </section>
            
            <section id="resources">
                <h2>Resources</h2>
                <p>Download the paper and learn more:</p>
                <br>
                <a href="paper.pdf" class="btn">Download PDF</a>
                <a href="https://arxiv.org/abs/2602.21766" target="_blank" class="btn">View on ArXiv</a>
                <a href="https://arxiv.org/html/2602.21766v1" target="_blank" class="btn">HTML Version</a>
            </section>
        </div>
        
        <footer>
            <p>RAMSeS Tutorial - Based on arXiv:2602.21766</p>
            <p>Created for educational purposes</p>
        </footer>
    </div>
</body>
</html>
