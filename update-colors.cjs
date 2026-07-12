const fs = require('fs');
const path = require('path');

const files = [
    'public/assets/css/style.css',
    'public/assets/css/skin.css',
    'public/assets/css/main.css'
];

const basePath = 'c:\\Users\\shaheer\\Desktop\\project\\raimall';

files.forEach(file => {
    const filePath = path.join(basePath, file);
    if (fs.existsSync(filePath)) {
        let content = fs.readFileSync(filePath, 'utf8');

        // Replace primary color
        content = content.replace(/#cc6666/gi, '#a3834a');
        content = content.replace(/rgba\(204,\s*102,\s*102,/gi, 'rgba(163, 131, 74,');

        // Replace secondary color
        content = content.replace(/#fcb941/gi, '#252522');
        content = content.replace(/rgba\(252,\s*185,\s*65,/gi, 'rgba(37, 37, 34,');

        // Third color: let's replace #333333 and #333 with the secondary color since #252522 is dark,
        // and use #f1ece6 for light backgrounds? Or just apply the third color to the body bg in demo-7.css?
        // Let's hold off on #333 replacement unless we want to map standard text to #252522.
        // Actually, let's just do primary and secondary first, and add a body bg rule for the third color.

        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Updated ' + file);
    } else {
        console.log('File not found: ' + file);
    }
});
