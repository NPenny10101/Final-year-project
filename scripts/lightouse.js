/* const { lighthouse, loadConfig } = require('lighthouse');
const chromeLauncher = require('chrome-launcher');
const fs = require('fs');

// Function to run Lighthouse and generate report
async function runLighthouse(url) {
    // Launch Chrome
    const chrome = await chromeLauncher.launch({ chromeFlags: ['--headless'] });
    const options = { logLevel: 'info', output: 'html', onlyCategories: ['performance'], port: chrome.port };

    // Run Lighthouse
    const runnerResult = await lighthouse(url, options);

    // Save report to file
    const reportHtml = runnerResult.report;
    fs.writeFileSync('lighthouse-report.html', reportHtml);

    // Output JSON results
    console.log(runnerResult.lhr.categories.performance.score);

    // Close Chrome
    await chrome.kill();
}

// Example usage
const url = 'https://example.com';
runLighthouse(url);
 */