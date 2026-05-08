const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const themeDir = path.join(root, 'wp-content', 'themes', 'pauza-rabotaet');
const outDir = path.join(root, 'dist');

const previewPath = path.join(themeDir, 'preview.html');
const stylePath = path.join(themeDir, 'style.css');
const themeJsPath = path.join(themeDir, 'assets', 'theme.js');
const stepsPath = path.join(themeDir, 'inc', 'step-full-texts.json');
const iconDir = path.join(themeDir, 'assets', 'step-icons');
const faviconPath = path.join(themeDir, 'assets', 'favicon.svg');

let html = fs.readFileSync(previewPath, 'utf8');
const css = fs.readFileSync(stylePath, 'utf8');
const themeJs = fs.readFileSync(themeJsPath, 'utf8');
const stepDataRaw = fs.readFileSync(stepsPath, 'utf8');
const icons = {};

for (const file of fs.readdirSync(iconDir)) {
  if (!/^step-\d+\.png$/.test(file)) {
    continue;
  }

  const number = Number(file.match(/step-(\d+)\.png/)[1]);
  icons[number] = `data:image/png;base64,${fs.readFileSync(path.join(iconDir, file)).toString('base64')}`;
}

html = html.replace(
  '<link rel="stylesheet" href="style.css">',
  `<style>\n${css}\n</style>`
);

html = html.replace(
  '<script src="assets/theme.js"></script>',
  `<script>\n${themeJs}\n</script>\n<script>\nwindow.PAUZA_STEP_DATA = ${stepDataRaw};\nwindow.PAUZA_STEP_ICONS = ${JSON.stringify(icons)};\n(function () {\n  const originalFetch = window.fetch ? window.fetch.bind(window) : null;\n  window.fetch = function (resource, init) {\n    const url = String(resource && resource.url ? resource.url : resource);\n    if (url.indexOf('inc/step-full-texts.json') !== -1) {\n      return Promise.resolve(new Response(JSON.stringify(window.PAUZA_STEP_DATA), {\n        headers: { 'Content-Type': 'application/json; charset=utf-8' }\n      }));\n    }\n    return originalFetch(resource, init);\n  };\n})();\n</script>`
);

html = html.replace(
  "img.src = 'assets/step-icons/step-' + String(stepNumber).padStart(2, '0') + '.png';",
  "img.src = (window.PAUZA_STEP_ICONS && window.PAUZA_STEP_ICONS[stepNumber]) || ('assets/step-icons/step-' + String(stepNumber).padStart(2, '0') + '.png');"
);

fs.rmSync(outDir, { recursive: true, force: true });
fs.mkdirSync(outDir, { recursive: true });
fs.mkdirSync(path.join(outDir, 'assets'), { recursive: true });
fs.writeFileSync(path.join(outDir, 'index.html'), html, 'utf8');
fs.copyFileSync(faviconPath, path.join(outDir, 'assets', 'favicon.svg'));
fs.writeFileSync(path.join(outDir, '.nojekyll'), '', 'utf8');

console.log(`Built ${path.join(outDir, 'index.html')} (${Buffer.byteLength(html)} bytes)`);
