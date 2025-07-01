// src/utils/toast.js
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';   // if not already imported

const BASE = {
    toast: true,
    position: 'top-center',   // adjust if you prefer "bottom-end"
    showConfirmButton: false,
    timer: 2200,
    timerProgressBar: true,
    customClass: { popup: 'swal-toast' },  // <- for extra z-index via CSS
};

/**
 * toast('success', 'Saved!');
 * toast('error',   'Oops…');
 * toast('info',    'Next op: Packaging');
 */
export function toast(type, text) {
    return Swal.fire({ ...BASE, icon: type, title: text });
}
