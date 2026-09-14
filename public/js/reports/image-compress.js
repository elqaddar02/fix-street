// Shrinks photos in the browser before upload so phone pictures stay small on slow connections
// and well under common PHP upload limits. Re-encoding to JPEG also drops EXIF metadata, such as
// the GPS position where the photo was taken, so read any GPS data from the original file first.
window.madinupImage = (function () {
    const MAX_DIMENSION = 1920;
    const MAX_BYTES = 1.9 * 1024 * 1024;
    // Server-side limit (ReportController validates image max:6144 KB).
    const UPLOAD_LIMIT_BYTES = 6 * 1024 * 1024;

    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const image = new Image();
            image.onload = () => {
                URL.revokeObjectURL(url);
                resolve(image);
            };
            image.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('Image could not be read.'));
            };
            image.src = url;
        });
    }

    async function compress(file) {
        const image = await loadImage(file);
        const canvas = document.createElement('canvas');
        let scale = Math.min(1, MAX_DIMENSION / Math.max(image.naturalWidth, image.naturalHeight));
        let quality = 0.85;

        for (let attempt = 0; attempt < 6; attempt++) {
            canvas.width = Math.round(image.naturalWidth * scale);
            canvas.height = Math.round(image.naturalHeight * scale);

            const context = canvas.getContext('2d');
            // Transparent PNG areas would otherwise become black in the JPEG.
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            if (blob && blob.size <= MAX_BYTES) {
                const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
            }

            quality = Math.max(0.5, quality - 0.1);
            scale *= 0.8;
        }

        throw new Error('Image could not be compressed below the size limit.');
    }

    // Returns false on browsers that cannot replace a file input's contents.
    function replaceInputFile(input, file) {
        try {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            return true;
        } catch (error) {
            return false;
        }
    }

    return { compress, replaceInputFile, MAX_BYTES, UPLOAD_LIMIT_BYTES };
})();
