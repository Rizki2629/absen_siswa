const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const inputDir = 'public/images/habits';
const files = fs.readdirSync(inputDir).filter(f => f.endsWith('.png') && !f.startsWith('opt_'));

async function compressImage(file) {
    const input = path.join(inputDir, file);
    const output = path.join(inputDir, 'opt_' + file);
    
    try {
        await sharp(input)
            .resize(600, null, {
                withoutEnlargement: true,
                fit: 'inside'
            })
            .png({
                quality: 85,
                compressionLevel: 9,
                progressive: true
            })
            .toFile(output);
        
        const originalSize = fs.statSync(input).size;
        const compressedSize = fs.statSync(output).size;
        const reduction = ((originalSize - compressedSize) / originalSize * 100).toFixed(2);
        
        console.log(`✓ ${file}: ${(originalSize/1024).toFixed(2)}KB → ${(compressedSize/1024).toFixed(2)}KB (${reduction}% reduction)`);
    } catch (err) {
        console.error(`✗ Failed to compress ${file}:`, err.message);
    }
}

async function main() {
    console.log(`Compressing ${files.length} images...\n`);
    
    for (const file of files) {
        await compressImage(file);
    }
    
    console.log('\n✓ All images compressed successfully!');
}

main();
