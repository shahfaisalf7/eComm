// import ImagePicker from './ImagePicker';
// import MediaPicker from './MediaPicker';
// import Uploader from './Uploader';
//
// window.MediaPicker = MediaPicker;
//
// if ($('.image-picker').length !== 0) {
//     new ImagePicker();
// }
//
// if ($('.dropzone').length !== 0) {
//     new Uploader();
// }
import ImagePicker from './ImagePicker';
import MediaPicker from './MediaPicker';
import Uploader from './Uploader';

window.MediaPicker = MediaPicker;

if ($('.image-picker').length !== 0) {
    new ImagePicker();
}

// Force Uploader init after a delay
setTimeout(() => {
    if ($('.dropzone').length !== 0) {
        new Uploader();
    } else {
        console.log('No .dropzone found');
    }
}, 1000); // Delay to allow MediaPicker to render
