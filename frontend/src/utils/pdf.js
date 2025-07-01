// src/utils/pdf.js
import axios from 'axios';

/**
 * Stream a backend PDF to a new tab or, if a popup blocker is active,
 * fall back to downloading the file.
 */
export async function openPdf(url, filename = 'sheet.pdf') {
    /* 1. Open a placeholder tab synchronously (won't be blocked) */
    const tab = window.open('', '_blank');

    /* If popup blocker still prevented it, tab is null */
    const popupBlocked = !tab;

    /* 2. Fetch PDF as Blob */
    const { data } = await axios.get(url, { responseType: 'blob' });
    const blobUrl = URL.createObjectURL(
        new Blob([data], { type: 'application/pdf' })
    );

    if (popupBlocked) {
        /* ---- Fallback: trigger a download ---- */
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(blobUrl);
        return;
    }

    /* 3. Stream PDF into the already-opened tab */
    tab.location = `${blobUrl}#toolbar=0`;    // hide toolbar for cleaner print
    tab.onload   = () => URL.revokeObjectURL(blobUrl);
}
